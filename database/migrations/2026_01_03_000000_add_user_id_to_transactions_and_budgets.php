<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Buat akun admin default jika belum ada user sama sekali di database
        $adminId = DB::table('users')->orderBy('id')->value('id');
        if (! $adminId) {
            $adminId = DB::table('users')->insertGetId([
                'name' => 'Admin Utama',
                'email' => 'admin@financee.local',
                'password' => Hash::make('admin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Tambah kolom user_id pada tabel transactions & migrasikan data yang sudah ada
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->index(['user_id', 'date']);
        });

        // Hubungkan semua transaksi yang sudah ada ke akun admin
        DB::table('transactions')->whereNull('user_id')->update(['user_id' => $adminId]);

        // Jadikan user_id non-nullable setelah data lama terisi
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });

        // 3. Tambah kolom user_id pada tabel budgets & sesuaikan index unik
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropUnique(['category', 'month']);
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->cascadeOnDelete();
        });

        // Hubungkan semua budget yang sudah ada ke akun admin
        DB::table('budgets')->whereNull('user_id')->update(['user_id' => $adminId]);

        // Buat index unik baru: [user_id, category, month]
        Schema::table('budgets', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->unique(['user_id', 'category', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'category', 'month']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->unique(['category', 'month']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'date']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
