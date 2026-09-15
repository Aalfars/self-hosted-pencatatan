<?php

namespace App\Console\Commands;

use App\Models\Budget;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsappSummary extends Command
{
    protected $signature = 'finance:send-summary';

    protected $description = 'Kirim ringkasan pengeluaran bulan berjalan ke WhatsApp via ZAWA API';

    public function handle(): int
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();

        $pengeluaranBulanIni = Transaction::pengeluaran()->bulanIni()->get();
        $totalPengeluaran = $pengeluaranBulanIni->sum('amount');

        $totalPengeluaranBulanLalu = Transaction::pengeluaran()
            ->whereYear('date', $lastMonth->year)
            ->whereMonth('date', $lastMonth->month)
            ->sum('amount');

        $rincianPerKategori = $pengeluaranBulanIni
            ->groupBy('category')
            ->map(fn ($items) => $items->sum('amount'))
            ->sortDesc();

        $namaBulan = $now->translatedFormat('F Y');

        $pesan = $this->buildMessage(
            $namaBulan,
            $totalPengeluaran,
            $totalPengeluaranBulanLalu,
            $rincianPerKategori
        );

        $terkirim = $this->sendToZawaWithRetry($pesan);

        if ($terkirim) {
            $this->info('Ringkasan keuangan berhasil dikirim ke WhatsApp.');
            return self::SUCCESS;
        }

        $this->error('Gagal mengirim ringkasan keuangan ke WhatsApp setelah beberapa percobaan. Cek log untuk detail.');
        return self::FAILURE;
    }

    private function buildMessage(
        string $namaBulan,
        float $total,
        float $totalBulanLalu,
        $rincianPerKategori
    ): string {
        $rp = fn ($angka) => 'Rp ' . number_format($angka, 0, ',', '.');

        $pesan  = "📊 *Laporan Keuangan {$namaBulan}*\n\n";
        $pesan .= "💰 Total: {$rp($total)}\n";

        // Perbandingan dengan bulan lalu
        if ($totalBulanLalu > 0) {
            $selisihPersen = round((($total - $totalBulanLalu) / $totalBulanLalu) * 100, 1);
            if ($selisihPersen > 0) {
                $pesan .= "📈 Naik {$selisihPersen}% dibanding bulan lalu ({$rp($totalBulanLalu)})\n";
            } elseif ($selisihPersen < 0) {
                $pesan .= "📉 Turun " . abs($selisihPersen) . "% dibanding bulan lalu ({$rp($totalBulanLalu)})\n";
            } else {
                $pesan .= "➖ Sama seperti bulan lalu ({$rp($totalBulanLalu)})\n";
            }
        }

        $pesan .= "\n📌 *Rincian per Kategori:*\n";

        if ($rincianPerKategori->isEmpty()) {
            $pesan .= "_Belum ada transaksi pengeluaran bulan ini._\n";
        } else {
            foreach ($rincianPerKategori as $kategori => $jumlah) {
                $pesan .= "• {$kategori}: {$rp($jumlah)}\n";
            }

            // Top 3 kategori terbesar
            $top3 = $rincianPerKategori->take(3);
            $pesan .= "\n🔥 *Top " . $top3->count() . " Pengeluaran Terbesar:*\n";
            $no = 1;
            foreach ($top3 as $kategori => $jumlah) {
                $pesan .= "{$no}. {$kategori} — {$rp($jumlah)}\n";
                $no++;
            }
        }

        // Peringatan budget yang mendekati/melebihi batas (>= 80%)
        $peringatanBudget = $this->cekBudgetWarning();
        if ($peringatanBudget->isNotEmpty()) {
            $pesan .= "\n⚠️ *Peringatan Budget:*\n";
            foreach ($peringatanBudget as $line) {
                $pesan .= "{$line}\n";
            }
        }

        return $pesan;
    }

    /**
     * Cek kategori yang sudah >= 80% dari budget bulan ini.
     */
    private function cekBudgetWarning()
    {
        $rp = fn ($angka) => 'Rp ' . number_format($angka, 0, ',', '.');

        return Budget::bulan(Carbon::now())->get()
            ->filter(fn ($budget) => $budget->percent >= 80)
            ->map(function ($budget) use ($rp) {
                $status = $budget->is_over ? 'MELEBIHI budget' : 'mendekati batas budget';
                return "• {$budget->category}: {$rp($budget->spent)} / {$rp($budget->amount)} ({$budget->percent}%) — {$status}";
            })
            ->values();
    }

    /**
     * Kirim pesan ke ZAWA dengan retry (3x percobaan, jeda naik bertahap).
     *
     * Header wajib ZAWA: `id` (ID sesi) dan `session-id` (Session ID),
     * bukan Authorization Bearer token tunggal.
     */
    private function sendToZawaWithRetry(string $pesan, int $maxAttempt = 3): bool
    {
        $url         = config('services.zawa.url');
        $sessionId   = config('services.zawa.id');
        $sessionKey  = config('services.zawa.session_id');
        $targetPhone = config('services.zawa.target_phone');

        for ($attempt = 1; $attempt <= $maxAttempt; $attempt++) {
            try {
                $response = Http::timeout(15)->withHeaders([
                    'id'         => $sessionId,
                    'session-id' => $sessionKey,
                ])->post($url, [
                    'phone' => $targetPhone,
                    'type'  => 'text',
                    'text'  => $pesan,
                ]);

                if ($response->successful()) {
                    Log::info('ZAWA WA summary sent', ['attempt' => $attempt, 'response' => $response->json()]);
                    return true;
                }

                Log::warning('ZAWA WA summary failed, will retry if attempts remain', [
                    'attempt' => $attempt,
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                ]);
            } catch (\Throwable $e) {
                Log::warning("ZAWA WA summary exception on attempt {$attempt}: " . $e->getMessage());
            }

            // Jeda sebelum retry berikutnya (2s, 4s, ...)
            if ($attempt < $maxAttempt) {
                sleep($attempt * 2);
            }
        }

        Log::error('ZAWA WA summary permanently failed after ' . $maxAttempt . ' attempts.');
        return false;
    }
}
