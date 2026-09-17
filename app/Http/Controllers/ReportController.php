<?php

namespace App\Http\Controllers;

use App\Exports\TransactionsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Export laporan transaksi milik user ke Excel (.xlsx), menghormati filter yang aktif
     * di dashboard (bulan, kategori, tipe).
     */
    public function exportExcel(Request $request)
    {
        $bulan = $request->get('bulan');
        $kategori = $request->get('kategori');
        $type = $request->get('type');

        $namaFile = 'laporan-keuangan'.($bulan ? "-{$bulan}" : '').'.xlsx';

        return Excel::download(
            new TransactionsExport(Auth::id(), $bulan, $kategori, $type),
            $namaFile
        );
    }
}
