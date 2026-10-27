<?php

namespace Database\Seeders;

use App\Models\Major;
use App\Models\Subject;
use Illuminate\Database\Seeder;

/**
 * Satu mapel kejuruan awal per jurusan, SEMENTARA memakai ikon RPL sampai ikon tiap jurusan tersedia.
 * Nama mapel hanya usulan awal; admin bebas mengubahnya di menu Mata Pelajaran.
 * Aman diulang: php artisan db:seed --class=VocationalSubjectSeeder
 */
class VocationalSubjectSeeder extends Seeder
{
    public const SUBJECTS = [
        'RPL' => 'Pemrograman Dasar',
        'TKJ' => 'Jaringan Dasar',
        'DKV' => 'Dasar Desain Grafis',
        'ANM' => 'Dasar Animasi',
        'PSPT' => 'Dasar Produksi Siaran',
        'DPB' => 'Dasar Desain Busana',
        'TKKR' => 'Dasar Kecantikan Kulit dan Rambut',
        'KKKR' => 'Dasar Kriya Kayu dan Rotan',
        'ULW' => 'Dasar Pariwisata',
        'DPIB' => 'Dasar Desain Pemodelan Bangunan',
    ];

    public function run(): void
    {
        $this->call(MajorSeeder::class); // pastikan 10 jurusan sudah ada

        foreach (self::SUBJECTS as $code => $name) {
            $major = Major::where('code', $code)->first();
            if ($major) {
                Subject::firstOrCreate(
                    ['name' => $name, 'major_id' => $major->id],
                    ['type' => 'kejuruan', 'icon' => 'mapel-rpl'],
                );
            }
        }
    }
}
