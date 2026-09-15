<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = ['category', 'amount', 'month'];

    protected $casts = [
        'month'  => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Hitung total pengeluaran aktual untuk kategori & bulan budget ini.
     */
    public function getSpentAttribute(): float
    {
        return (float) Transaction::pengeluaran()
            ->where('category', $this->category)
            ->whereYear('date', $this->month->year)
            ->whereMonth('date', $this->month->month)
            ->sum('amount');
    }

    /**
     * Persentase pemakaian budget (dibatasi maks 100 untuk progress bar, nilai asli bisa >100).
     */
    public function getPercentAttribute(): float
    {
        if ((float) $this->amount <= 0) {
            return 0;
        }
        return round(($this->spent / (float) $this->amount) * 100, 1);
    }

    public function getIsOverAttribute(): bool
    {
        return $this->spent > (float) $this->amount;
    }

    public function scopeBulan($query, $month)
    {
        return $query->whereYear('month', $month->year)->whereMonth('month', $month->month);
    }
}
