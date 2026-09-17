<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBudgetRequest;
use App\Models\Budget;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    /**
     * Simpan atau update budget (upsert berdasarkan user_id + category + month)
     */
    public function store(StoreBudgetRequest $request)
    {
        $validated = $request->validated();
        $monthDate = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();

        Budget::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'category' => $validated['category'],
                'month' => $monthDate,
            ],
            [
                'amount' => $validated['amount'],
            ]
        );

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Budget kategori "'.$validated['category'].'" berhasil disimpan.');
    }

    public function destroy(Budget $budget)
    {
        abort_if($budget->user_id !== Auth::id(), 403);

        $budget->delete();

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Budget berhasil dihapus.');
    }
}
