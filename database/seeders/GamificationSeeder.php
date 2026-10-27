<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Level;
use Illuminate\Database\Seeder;

/** Level + katalog lencana awal. Aman diulang dan tidak menimpa hasil edit admin: php artisan db:seed --class=GamificationSeeder */
class GamificationSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Beginner' => 0, 'Intermediate' => 100, 'Advanced' => 300, 'Expert' => 700] as $name => $min) {
            Level::firstOrCreate(['name' => $name], ['min_xp' => $min]);
        }

        // [nama, deskripsi, ikon, tone, type, kode, required_xp, xp_reward]
        $badges = [
            ['First Spark', 'XP pertamamu terkumpul. Perjalanan baru dimulai!', 'flame', 'amber', 'xp', null, 10, 0],
            ['Rising Star', 'Mulai konsisten dan naik daun.', 'star', 'sky', 'xp', null, 50, 0],
            ['The Challenger', 'Berani menantang diri dan terus maju.', 'award', 'violet', 'xp', null, 150, 0],
            ['The Prodigy', 'Kemampuanmu menonjol di antara teman-temanmu.', 'award', 'brand', 'xp', null, 300, 0],
            ['The Mastermind', 'Otak strategis di balik prestasi yang konsisten.', 'bar-chart', 'emerald', 'xp', null, 500, 0],
            ['Living Legend', 'Namamu akan dikenang di NeuroTrack.', 'star', 'rose', 'xp', null, 1000, 0],

            ['Flawless Victory', 'Meraih nilai 100 pada sebuah kuis.', 'check-circle', 'emerald', 'auto', 'perfect_quiz', 0, 20],
            ['Brainstormer', 'Menyelesaikan 5 kuis yang berbeda.', 'book', 'sky', 'auto', 'quiz_5', 0, 15],
            ['On Fire', 'Hadir 5 hari berturut-turut.', 'flame', 'orange', 'auto', 'streak_5', 0, 20],
            ['Early Bird', 'Hadir tepat waktu sebanyak 10 kali.', 'clock', 'amber', 'auto', 'early_bird_10', 0, 20],

            ['Arena Contender', 'Berani turun ke arena: ikut serta dalam lomba.', 'send', 'sky', 'manual', null, 0, 25],
            ['Podium Finisher', 'Masuk 3 besar dalam sebuah lomba.', 'award', 'amber', 'manual', null, 0, 100],
            ['Grand Champion', 'Juara 1 dalam sebuah lomba.', 'star', 'rose', 'manual', null, 0, 200],
            ['The Class Champion', 'Juara 1 kelas.', 'star', 'violet', 'manual', null, 0, 75],
            ['Runner-Up Royale', 'Juara 2 kelas.', 'award', 'sky', 'manual', null, 0, 50],
            ['Bronze Contender', 'Juara 3 kelas.', 'award', 'orange', 'manual', null, 0, 30],
            ['Team Player', 'Kerja sama tim yang luar biasa.', 'users', 'emerald', 'manual', null, 0, 20],
        ];

        foreach ($badges as [$name, $description, $icon, $tone, $type, $code, $required, $reward]) {
            Badge::firstOrCreate(['name' => $name], [
                'description' => $description, 'icon' => $icon, 'tone' => $tone, 'type' => $type,
                'code' => $code, 'required_xp' => $required, 'xp_reward' => $reward,
            ]);
        }
    }
}
