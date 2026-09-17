<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        private int $userId,
        private ?string $bulan = null,   // format Y-m
        private ?string $kategori = null,
        private ?string $type = null,
    ) {}

    public function collection(): Enumerable
    {
        $query = Transaction::where('user_id', $this->userId)->orderBy('date');

        if ($this->bulan) {
            [$year, $month] = explode('-', $this->bulan);
            $query->whereYear('date', $year)->whereMonth('date', $month);
        }
        if ($this->kategori) {
            $query->where('category', $this->kategori);
        }
        if ($this->type) {
            $query->where('type', $this->type);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['Timestamp', 'Tipe', 'Kategori', 'Judul', 'Keterangan', 'Jumlah (Rp)', 'Tanggal'];
    }

    public function map(mixed $row): array
    {
        /** @var Transaction $row */
        return [
            $row->created_at->format('d/m/Y H:i'),
            ucfirst($row->type),
            $row->category,
            $row->title,
            $row->description,
            (float) $row->amount,
            $row->date->format('d/m/Y'),
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
