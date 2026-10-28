<?php

return [
    // Kode QR absensi berganti tiap N detik. Sah selama jendela sekarang + satu jendela sebelumnya (N–2N detik).
    // Ubah lewat .env: ATTENDANCE_QR_STEP=7 (jangan di bawah 3). Setelah mengubah .env: php artisan config:clear
    'qr_step' => (int) env('ATTENDANCE_QR_STEP', 7),
];
