<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Tipe transaksi: pemasukan atau pengeluaran
            $table->enum('type', ['pemasukan', 'pengeluaran'])->index();

            // Kategori / tipe pengeluaran (bebas diketik atau dipilih dari datalist)
            // Contoh: Makan, Jajan, Bensin, Kewajiban, Donasi/Amal, Beli Barang, dll
            $table->string('category');

            // Judul singkat transaksi
            $table->string('title');

            // Keterangan / catatan tambahan (opsional)
            $table->text('description')->nullable();

            // Jumlah nominal transaksi (Rupiah), gunakan decimal agar aman untuk desimal
            $table->decimal('amount', 15, 2);

            // Tanggal transaksi (bisa dipilih manual, beda dengan created_at/timestamp input)
            $table->date('date')->index();

            // Path file gambar bukti transaksi (opsional)
            $table->string('image')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
