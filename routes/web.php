<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TransactionController::class, 'index'])->name('transactions.index');
Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
Route::post('/transactions/quick-parse', [TransactionController::class, 'quickParse'])->name('transactions.quick-parse');
Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');
Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');

Route::get('/laporan/export', [ReportController::class, 'exportExcel'])->name('report.export');
