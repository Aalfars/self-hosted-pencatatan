<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Budget;
use App\Models\Transaction;
use App\Services\TransactionTextParser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    /**
     * Dashboard: form input + tabel transaksi + grafik + budget progress milik user yang login
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = Transaction::where('user_id', $userId);

        // Filter bulan (format: YYYY-MM)
        if ($request->filled('bulan')) {
            [$year, $month] = explode('-', $request->bulan);
            $query->whereYear('date', $year)->whereMonth('date', $month);
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('category', $request->kategori);
        }

        // Filter tipe
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search judul / keterangan
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Sorting
        $sort = $request->get('sort', 'date_desc');
        $sortMap = [
            'date_desc' => ['date', 'desc'],
            'date_asc' => ['date', 'asc'],
            'amount_desc' => ['amount', 'desc'],
            'amount_asc' => ['amount', 'asc'],
        ];
        [$sortColumn, $sortDirection] = $sortMap[$sort] ?? $sortMap['date_desc'];
        $query->orderBy($sortColumn, $sortDirection)->orderBy('id', 'desc');

        $transactions = $query->paginate(10)->withQueryString();

        $totalPemasukan = (clone $query)->pemasukan()->sum('amount');
        $totalPengeluaran = (clone $query)->pengeluaran()->sum('amount');

        $categories = Transaction::where('user_id', $userId)
            ->select('category')
            ->distinct()
            ->pluck('category')
            ->merge(Transaction::DEFAULT_CATEGORIES)
            ->unique()
            ->sort()
            ->values();

        // ===== Data untuk grafik pie: breakdown pengeluaran per kategori bulan berjalan =====
        $pengeluaranBulanIni = Transaction::where('user_id', $userId)
            ->pengeluaran()
            ->bulanIni()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $chartCategoryLabels = $pengeluaranBulanIni->pluck('category');
        $chartCategoryValues = $pengeluaranBulanIni->pluck('total');

        // ===== Data untuk grafik tren 6 bulan terakhir (pemasukan vs pengeluaran) =====
        $trendMonths = collect(range(5, 0))->map(fn ($i) => Carbon::now()->subMonths($i)->startOfMonth());

        $trendLabels = $trendMonths->map(fn ($m) => $m->translatedFormat('M Y'));
        $trendPemasukan = $trendMonths->map(function ($m) use ($userId) {
            return (float) Transaction::where('user_id', $userId)
                ->pemasukan()
                ->whereYear('date', $m->year)
                ->whereMonth('date', $m->month)
                ->sum('amount');
        });
        $trendPengeluaran = $trendMonths->map(function ($m) use ($userId) {
            return (float) Transaction::where('user_id', $userId)
                ->pengeluaran()
                ->whereYear('date', $m->year)
                ->whereMonth('date', $m->month)
                ->sum('amount');
        });

        // ===== Budget progress bulan berjalan milik user =====
        $budgets = Budget::where('user_id', $userId)->bulan(Carbon::now())->get();

        return view('transactions.index', compact(
            'transactions',
            'totalPemasukan',
            'totalPengeluaran',
            'categories',
            'chartCategoryLabels',
            'chartCategoryValues',
            'trendLabels',
            'trendPemasukan',
            'trendPengeluaran',
            'budgets'
        ));
    }

    public function store(StoreTransactionRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('bukti-transaksi', 'public');
        }

        Transaction::create($validated);

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        abort_if($transaction->user_id !== Auth::id(), 403);

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($transaction->image) {
                Storage::disk('public')->delete($transaction->image);
            }
            $validated['image'] = $request->file('image')->store('bukti-transaksi', 'public');
        }

        $transaction->update($validated);

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction)
    {
        abort_if($transaction->user_id !== Auth::id(), 403);

        if ($transaction->image) {
            Storage::disk('public')->delete($transaction->image);
        }

        $transaction->delete();

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Catat Cepat: parse teks bebas ("makan coto sama ayang 39k") jadi
     * data transaksi terstruktur.
     */
    public function quickParse(Request $request, TransactionTextParser $parser)
    {
        $request->validate(['text' => ['required', 'string', 'max:255']]);

        $result = $parser->parse($request->input('text'));

        return response()->json($result);
    }
}
