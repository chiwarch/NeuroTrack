<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Seeder;

/**
 * Daftar jurusan sekolah. Aman dijalankan berulang dan di produksi
 * (tidak menimpa nama jurusan yang sudah diubah admin):
 *   php artisan db:seed --class=MajorSeeder
 */
class MajorSeeder extends Seeder
{
    public const MAJORS = [
        'RPL' => 'Rekayasa Perangkat Lunak',
        'TKJ' => 'Teknik Komputer dan Jaringan',
        'DKV' => 'Desain Komunikasi Visual',
        'ANM' => 'Animasi',
        'PSPT' => 'Produksi dan Siaran Program Televisi',
        'DPB' => 'Desain dan Produksi Busana',
        'TKKR' => 'Tata Kecantikan Kulit dan Rambut',
        'KKKR' => 'Kriya Kreatif Kayu dan Rotan',
        'ULW' => 'Usaha Layanan Wisata',
        'DPIB' => 'Desain Permodelan dan Informasi Bangunan',
    ];

    public function run(): void
    {
        foreach (self::MAJORS as $code => $name) {
            Major::firstOrCreate(['code' => $code], ['name' => $name]);
        }
    }
}
