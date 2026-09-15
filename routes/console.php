<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwal otomatis: kirim ringkasan keuangan ke WhatsApp setiap hari jam 20:00
Schedule::command('finance:send-summary')
    ->dailyAt('20:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

/*
|--------------------------------------------------------------------------
| PENTING: Cara mengaktifkan scheduler
|--------------------------------------------------------------------------
| Laravel scheduler butuh 1 cron job di server yang berjalan tiap menit,
| lalu Laravel sendiri yang menentukan kapan job di atas benar-benar
| dieksekusi (jam 20:00). Tambahkan baris berikut di crontab server:
|
| * * * * * cd /path-ke-project && php artisan schedule:run >> /dev/null 2>&1
|
| Untuk uji coba manual tanpa menunggu jadwal, jalankan langsung:
| php artisan finance:send-summary
*/
