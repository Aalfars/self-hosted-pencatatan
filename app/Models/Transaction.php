<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'category',
        'title',
        'description',
        'amount',
        'date',
        'image',
    ];

    protected $casts = [
        'date'   => 'date',
        'amount' => 'decimal:2',
    ];

    // Kategori bawaan yang disarankan (dipakai untuk datalist di form)
    public const DEFAULT_CATEGORIES = [
        'Makan',
        'Jajan',
        'Bensin',
        'Kewajiban',
        'Donasi/Amal',
        'Beli Barang',
        'Transportasi',
        'Tagihan',
        'Kesehatan',
        'Hiburan',
    ];

    // Accessor: format rupiah rapi, misal Rp 150.000
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->amount, 0, ',', '.');
    }

    // Accessor: URL publik gambar bukti transaksi
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        return Storage::disk('public')->url($this->image);
    }

    public function scopePemasukan($query)
    {
        return $query->where('type', 'pemasukan');
    }

    public function scopePengeluaran($query)
    {
        return $query->where('type', 'pengeluaran');
    }

    public function scopeBulanIni($query)
    {
        return $query->whereMonth('date', now()->month)
                      ->whereYear('date', now()->year);
    }
}
