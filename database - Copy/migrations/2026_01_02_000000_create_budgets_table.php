<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->decimal('amount', 15, 2); // batas anggaran untuk kategori ini
            $table->date('month'); // disimpan sebagai tanggal 1 di bulan tsb, misal 2026-06-01
            $table->timestamps();

            // Satu kategori hanya boleh punya 1 budget per bulan
            $table->unique(['category', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
