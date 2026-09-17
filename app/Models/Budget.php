<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'category', 'amount', 'month'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'month' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Hitung total pengeluaran aktual untuk kategori & bulan budget milik pengguna ini.
     */
    public function getSpentAttribute(): float
    {
        return (float) Transaction::pengeluaran()
            ->where('user_id', $this->user_id)
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
