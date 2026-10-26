<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

/** Aman dijalankan berulang dan di produksi: php artisan db:seed --class=SkillSeeder */
class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $soft = [
            ['Komunikasi', 'Menyampaikan ide dan mendengarkan dengan jelas, lisan maupun tulisan.'],
            ['Kerja Sama', 'Bekerja dalam tim, saling membantu, menghargai pendapat teman.'],
            ['Kepemimpinan', 'Berinisiatif, mengarahkan dan memotivasi teman, berani mengambil keputusan.'],
            ['Berpikir & Pemecahan Masalah', 'Berpikir kritis, memecahkan masalah, kreatif dan inovatif.'],
            ['Etos Kerja & Tanggung Jawab', 'Disiplin, tekun, menyelesaikan tugas dan bertanggung jawab atas pekerjaannya.'],
            ['Adaptabilitas', 'Mau belajar hal baru dan menyesuaikan diri dengan perubahan.'],
        ];

        foreach ($soft as [$name, $description]) {
            Skill::firstOrCreate(['name' => $name, 'category' => 'soft_skill'], ['description' => $description]);
        }
    }
}
