<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Parser teks bebas -> data transaksi terstruktur.
 * Contoh input: "makan coto sama ayang 39k"
 * Hasil: type=pengeluaran, category=Makan, title="Coto sama ayang",
 *        amount=39000, date=hari ini
 *
 * Murni rule-based (regex + kamus kata kunci), tidak butuh API eksternal,
 * jadi instan dan tidak ada biaya per-request.
 */
class TransactionTextParser
{
    /**
     * Kamus kata kunci -> kategori. Urutan menentukan prioritas saat ada
     * kata kunci yang tumpang tindih.
     */
    private const CATEGORY_KEYWORDS = [
        'Makan' => ['makan', 'sarapan', 'nasi', 'ayam', 'sate', 'bakso', 'mie', 'soto', 'coto',
            'warteg', 'resto', 'restoran', 'kfc', 'mcd', 'burger', 'pizza', 'gofood', 'grabfood', 'catering'],
        'Jajan' => ['jajan', 'snack', 'cemilan', 'boba', 'es krim', 'kopi', 'gorengan', 'kue', 'roti'],
        'Bensin' => ['bensin', 'pertalite', 'pertamax', 'solar', 'bbm'],
        'Transportasi' => ['ojek', 'gojek', 'grab', 'angkot', 'busway', 'kereta', 'krl', 'parkir', 'tol', 'taksi', 'taxi'],
        'Tagihan' => ['listrik', 'pln', 'pdam', 'wifi', 'internet', 'pulsa', 'token', 'indihome', 'air pam'],
        'Kesehatan' => ['obat', 'dokter', 'apotek', 'rumah sakit', 'vitamin', 'klinik'],
        'Hiburan' => ['nonton', 'bioskop', 'netflix', 'spotify', 'game', 'liburan', 'wisata'],
        'Donasi/Amal' => ['donasi', 'sedekah', 'amal', 'infaq', 'zakat'],
        'Kewajiban' => ['cicilan', 'utang', 'iuran', 'kewajiban'],
        'Beli Barang' => ['beli', 'belanja', 'baju', 'sepatu', 'barang', 'elektronik', 'skincare'],
    ];

    /** Kata kunci yang menandakan transaksi ini pemasukan, bukan pengeluaran. */
    private const INCOME_KEYWORDS = [
        'gaji', 'bonus', 'thr', 'dapat uang', 'transfer masuk', 'terima uang',
        'cashback', 'refund', 'dividen', 'bunga bank', 'freelance', 'honor', 'komisi',
    ];

    public function parse(string $text): array
    {
        $remaining = trim($text);

        $date = $this->extractDate($remaining);
        $amount = $this->extractAmount($remaining) ?? 0;
        $type = $this->detectType($remaining);
        $category = $type === 'pemasukan' ? 'Pemasukan' : $this->detectCategory($remaining);

        $remaining = trim(preg_replace('/\s+/', ' ', $remaining));
        $title = $remaining !== '' ? Str::ucfirst($remaining) : ($category ?: 'Transaksi');

        return [
            'type'        => $type,
            'category'    => $category ?: 'Lainnya',
            'title'       => $title,
            'description' => '',
            'amount'      => $amount,
            'date'        => $date->format('Y-m-d'),
        ];
    }

    private function extractDate(string &$remaining): Carbon
    {
        // Format eksplisit: 19/06/2026, 19-06-26, 19/06
        if (preg_match('/\b(\d{1,2})[\/\-](\d{1,2})(?:[\/\-](\d{2,4}))?\b/', $remaining, $m)) {
            $day   = (int) $m[1];
            $month = (int) $m[2];
            $year  = isset($m[3]) ? (int) $m[3] : (int) now()->year;
            if ($year < 100) {
                $year += 2000;
            }

            try {
                $date = Carbon::create($year, $month, $day);
                $remaining = trim(str_replace($m[0], '', $remaining));
                return $date;
            } catch (\Throwable $e) {
                // format tidak valid, lanjut ke pengecekan kata relatif
            }
        }

        if (preg_match('/\bkemarin\b/iu', $remaining, $m)) {
            $remaining = trim(preg_replace('/\bkemarin\b/iu', '', $remaining, 1));
            return Carbon::yesterday();
        }

        if (preg_match('/\bbesok\b/iu', $remaining, $m)) {
            $remaining = trim(preg_replace('/\bbesok\b/iu', '', $remaining, 1));
            return Carbon::tomorrow();
        }

        if (preg_match('/\blusa\b/iu', $remaining, $m)) {
            $remaining = trim(preg_replace('/\blusa\b/iu', '', $remaining, 1));
            return Carbon::today()->addDays(2);
        }

        return Carbon::today();
    }

    private function extractAmount(string &$remaining): ?float
    {
        // "1.5 juta" / "1 jt"
        if (preg_match('/(\d+(?:[.,]\d+)?)\s*(juta|jt)\b/iu', $remaining, $m)) {
            $num = (float) str_replace(',', '.', $m[1]);
            $remaining = trim(str_replace($m[0], '', $remaining));
            return $num * 1_000_000;
        }

        // "39 ribu" / "39rb" / "39k"
        if (preg_match('/(\d+(?:[.,]\d+)?)\s*(ribu|rb|k)\b/iu', $remaining, $m)) {
            $num = (float) str_replace(',', '.', $m[1]);
            $remaining = trim(str_replace($m[0], '', $remaining));
            return $num * 1_000;
        }

        // "Rp39.000" / "Rp 39000"
        if (preg_match('/rp\.?\s*([\d.,]+)/iu', $remaining, $m)) {
            $num = (float) preg_replace('/[^\d]/', '', $m[1]);
            $remaining = trim(str_replace($m[0], '', $remaining));
            return $num;
        }

        // Angka polos, minimal 3 digit (hindari salah tangkap angka kecil seperti "2 ekor")
        if (preg_match('/\b(\d{1,3}(?:\.\d{3})+|\d{3,})\b/u', $remaining, $m)) {
            $num = (float) str_replace('.', '', $m[1]);
            $remaining = trim(str_replace($m[0], '', $remaining));
            return $num;
        }

        return null;
    }

    private function detectType(string $remaining): string
    {
        foreach (self::INCOME_KEYWORDS as $keyword) {
            if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/iu', $remaining)) {
                return 'pemasukan';
            }
        }
        return 'pengeluaran';
    }

    private function detectCategory(string &$remaining): ?string
    {
        foreach (self::CATEGORY_KEYWORDS as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/iu', $remaining)) {
                    $remaining = trim(preg_replace('/\b' . preg_quote($keyword, '/') . '\b/iu', '', $remaining, 1));
                    return $category;
                }
            }
        }
        return null;
    }
}
