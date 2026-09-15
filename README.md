# Finance App — Laravel + MySQL (Dark Mode) — v2

Aplikasi pencatat keuangan pribadi dengan CRUD transaksi, upload bukti transaksi,
otomatisasi kirim ringkasan ke WhatsApp via **ZAWA API**, dilengkapi:
- 📊 Grafik dashboard (pie kategori bulan ini + tren 6 bulan, pakai Chart.js)
- 🎯 Sistem Budget per kategori per bulan (progress bar + peringatan WA otomatis)
- 🔍 Search & sorting transaksi
- 📥 Export laporan ke Excel (.xlsx)
- 🧾 Form Request terpisah (validasi lebih rapi)
- 🔁 Retry otomatis saat kirim WA gagal + perbandingan bulan lalu + Top 3 kategori

## 🎨 Desain v3 — UI Baru

Tampilan sudah dirombak total jadi lebih modern, santai, dan mobile-friendly:
- **Palet warna baru**: dasar hitam-keunguan hangat + aksen apricot lembut (bukan dark mode generik neon-on-black).
- **Font Plus Jakarta Sans** (via Google Fonts CDN) — rounded, santai, modern.
- **Hero saldo** besar di atas, bukan kartu statistik kaku.
- **Filter jadi chip** yang bisa di-scroll (kategori) + segmented control (tipe) — bukan dropdown biasa.
- **List transaksi bergaya app** dengan avatar emoji per kategori, bukan tabel — otomatis rapi di HP.
- **Tombol tambah jadi FAB** (floating action button) yang membuka **bottom-sheet** (form naik dari bawah di HP, modal di desktop) — pola aplikasi keuangan modern (mis. mirip Money Manager/Splitwise).
- **Bottom navigation bar** khusus mobile (Beranda, Laporan, Tambah, Budget, Export).
- Budget ditampilkan sebagai kartu horizontal yang bisa digeser.

Tidak perlu instalasi tambahan untuk desain ini — Google Fonts & Chart.js tetap dari CDN, jadi asal ada koneksi internet, langsung jalan.

## ⚡ Fitur Baru: Catat Cepat (Quick Add via Teks Bebas)

Sekarang ada input "Catat Cepat" di dalam modal Tambah Transaksi. Ketik satu baris
bebas seperti dulu di WA, contoh:
```
makan coto sama ayang 39k
```
Tekan Enter / tombol ✨, lalu sistem otomatis mendeteksi:
- **Tipe** — pengeluaran (default) atau pemasukan (kalau ada kata seperti "gaji", "bonus", "transfer masuk", dll)
- **Kategori** — dicocokkan ke kamus kata kunci (Makan, Jajan, Bensin, Transportasi, Tagihan, dll)
- **Jumlah** — paham format "39k", "50rb", "1.5 juta", "Rp39.000", dan angka polos
- **Tanggal** — default hari ini, tapi paham "kemarin", "besok", "lusa", atau format tanggal eksplisit (19/06/2026)

Field form di bawahnya otomatis terisi (bisa dikoreksi dulu) sebelum ditekan "Simpan Transaksi" —
jadi tidak ada risiko salah simpan kalau parsing kurang tepat.

**100% berjalan di server sendiri (rule-based, tanpa API eksternal)** — tidak butuh WA
webhook (yang sudah dimatikan di ZAWA), tidak ada biaya tambahan, dan responnya instan.

Kalau nanti ingin parsing yang lebih pintar (paham kalimat lebih kompleks/typo), tinggal
ganti isi method `parse()` di `app/Services/TransactionTextParser.php` untuk memanggil
API AI (misal Anthropic API) alih-alih regex — strukturnya sudah dipisah rapi supaya gampang di-swap.

## 📁 Isi Paket Ini

File-file di sini adalah **potongan kode untuk ditempel ke project Laravel baru**,
bukan project Laravel yang utuh (tidak menyertakan vendor/, bootstrap Laravel, dll).

```
database/migrations/2026_01_01_000000_create_transactions_table.php
database/migrations/2026_01_02_000000_create_budgets_table.php
app/Models/Transaction.php
app/Models/Budget.php
app/Http/Requests/StoreTransactionRequest.php
app/Http/Requests/UpdateTransactionRequest.php
app/Http/Requests/StoreBudgetRequest.php
app/Http/Controllers/TransactionController.php
app/Http/Controllers/BudgetController.php
app/Http/Controllers/ReportController.php
app/Exports/TransactionsExport.php
app/Services/TransactionTextParser.php
app/Console/Commands/SendWhatsappSummary.php
routes/web.php
routes/console.php
resources/views/layouts/app.blade.php
resources/views/transactions/index.blade.php
config/services-zawa-snippet.php   <- cara nambah config, bukan file config utuh
.env.example.zawa                  <- contoh variabel .env yang perlu ditambahkan
```

## 🚀 Langkah Instalasi

### 1. Buat project Laravel baru
```bash
composer create-project laravel/laravel finance-app
cd finance-app
```

### 2. Salin file-file dari paket ini
Copy semua file sesuai struktur folder yang sama ke dalam project Laravel Anda
(timpa `routes/web.php` dan `routes/console.php` yang sudah ada).

### 3. Konfigurasi Database
Edit `.env`, isi kredensial MySQL:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=finance_app
DB_USERNAME=root
DB_PASSWORD=
```
Buat database `finance_app` di MySQL, lalu jalankan migrasi:
```bash
php artisan migrate
```

### 4. Buat symlink storage (untuk upload gambar)
```bash
php artisan storage:link
```

### 5. Tambahkan konfigurasi ZAWA API

**Penting — koreksi terhadap dokumentasi ZAWA:**
Setelah saya cek dokumentasi resmi ZAWA (https://azickri.gitbook.io/zawa/send-api/kirim-pesan),
endpoint `POST /message` ternyata **tidak** memakai satu API key/Bearer token,
melainkan 2 header wajib:
- `id` → ID sesi WhatsApp
- `session-id` → Session ID dari sesi tersebut

Keduanya didapat dari dashboard ZAWA saat Anda membuat sesi/device WhatsApp.
Jadi variabel env-nya saya sesuaikan sedikit dari permintaan awal (`ZAWA_API_KEY`
dipecah jadi `ZAWA_ID` + `ZAWA_SESSION_ID`) supaya benar-benar bisa jalan.

Tambahkan ke `.env` (lihat juga `.env.example.zawa`):
```
ZAWA_URL=https://api-zawa.azickri.com/message
ZAWA_ID=isi_dengan_id_sesi_zawa
ZAWA_SESSION_ID=isi_dengan_session_id_zawa
TARGET_PHONE_NUMBER=6281234567890
APP_TIMEZONE=Asia/Jakarta
```

Lalu buka `config/services.php` bawaan Laravel, tambahkan blok berikut
di dalam array yang di-return (lihat contoh di `config/services-zawa-snippet.php`):
```php
'zawa' => [
    'url'          => env('ZAWA_URL', 'https://api-zawa.azickri.com/message'),
    'id'           => env('ZAWA_ID'),
    'session_id'   => env('ZAWA_SESSION_ID'),
    'target_phone' => env('TARGET_PHONE_NUMBER'),
],
```

### 5b. Install package Excel (untuk fitur export laporan)
```bash
composer require maatwebsite/excel
```
Tidak perlu konfigurasi tambahan, `ReportController` sudah langsung memanggil
`Maatwebsite\Excel\Facades\Excel`.

### 6. Set timezone aplikasi
Di `config/app.php`, ubah:
```php
'timezone' => env('APP_TIMEZONE', 'Asia/Jakarta'),
```

### 7. Jalankan aplikasi
```bash
php artisan serve
```
Buka `http://127.0.0.1:8000`.

### 8. Aktifkan scheduler (kirim WA otomatis jam 20:00)
Tambahkan cron job berikut di server (Linux):
```
* * * * * cd /path-ke-project && php artisan schedule:run >> /dev/null 2>&1
```

Untuk uji coba manual tanpa menunggu jam 20:00:
```bash
php artisan finance:send-summary
```

Jika pakai Windows/local dev tanpa cron, jalankan sementara:
```bash
php artisan schedule:work
```

## ✅ Fitur yang Sudah Tersedia
- CRUD transaksi (pemasukan & pengeluaran)
- Kategori bebas (datalist + custom input)
- Upload bukti transaksi + preview modal
- Filter by bulan, kategori, tipe, **search judul/keterangan**, dan **sorting** + pagination
- Kartu ringkasan total pemasukan/pengeluaran
- **Grafik pie kategori bulan ini** dan **grafik tren 6 bulan** (Chart.js)
- **Sistem Budget**: atur batas per kategori per bulan, progress bar di dashboard
- **Export laporan ke Excel** (menghormati filter yang aktif)
- Dark mode modern (Bootstrap 5 + custom CSS variables)
- Modal konfirmasi hapus (bukan native browser confirm)
- Ringkasan otomatis harian ke WhatsApp via ZAWA API, dengan:
  - Perbandingan total pengeluaran vs bulan lalu (naik/turun berapa %)
  - Top 3 kategori pengeluaran terbesar
  - Peringatan otomatis jika suatu kategori sudah ≥80% dari budget
  - Retry otomatis 3x jika gagal kirim (jeda 2s, 4s)

## 🔧 Yang Perlu Anda Sesuaikan Sendiri
- Nomor `TARGET_PHONE_NUMBER` — bisa dikembangkan jadi multi-nomor (array) bila perlu.
- Validasi ukuran/format gambar di `TransactionController::validateData()` bisa disesuaikan.
- Jika ingin autentikasi user (multi-user), tambahkan Laravel Breeze/Fortify dan
  kolom `user_id` di tabel `transactions`.
