<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Badge;
use App\Models\XpTransaction;
use App\Services\Gamification;
use App\Models\Assignment;
use App\Models\ClassSubject;
use App\Models\Major;
use App\Models\Quiz;
use App\Models\SchoolClass;
use App\Models\Skill;
use App\Models\SkillAssessment;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Data contoh untuk pengembangan. Semua akun memakai kata sandi "password".
 * JANGAN dipakai di produksi.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(MajorSeeder::class);
        $rpl = Major::where('code', 'RPL')->firstOrFail();

        $mtk = Subject::create(['name' => 'Matematika', 'type' => 'umum', 'icon' => 'mapel-mtk']);
        $ind = Subject::create(['name' => 'Bahasa Indonesia', 'type' => 'umum', 'icon' => 'mapel-indonesia']);
        $ing = Subject::create(['name' => 'Bahasa Inggris', 'type' => 'umum', 'icon' => 'mapel-english']);
        $seni = Subject::create(['name' => 'Seni dan Budaya', 'type' => 'umum', 'icon' => 'mapel-seni']);
        $pemrograman = Subject::create(['name' => 'Pemrograman Dasar', 'type' => 'kejuruan', 'major_id' => $rpl->id, 'icon' => 'mapel-rpl']);

        $this->call(VocationalSubjectSeeder::class);

        $admin = User::create(['name' => 'Admin Sekolah', 'email' => 'admin@neurotrack.test', 'password' => 'password', 'role' => 'admin']);

        $guru = User::create(['name' => 'Bu Rina Wulandari', 'email' => 'guru@neurotrack.test', 'password' => 'password', 'role' => 'guru']);
        $guru->subjects()->sync([$mtk->id, $ing->id, $pemrograman->id]);

        $guru2 = User::create(['name' => 'Pak Dimas Prakoso', 'email' => 'guru2@neurotrack.test', 'password' => 'password', 'role' => 'guru']);
        $guru2->subjects()->sync([$ind->id, $seni->id]);

        $andi = User::create(['name' => 'Andi Saputra', 'email' => 'siswa@neurotrack.test', 'password' => 'password', 'role' => 'siswa', 'nis' => '10001', 'major_id' => $rpl->id, 'grade_level' => 10]);
        $bella = User::create(['name' => 'Bella Putri', 'email' => 'bella@neurotrack.test', 'password' => 'password', 'role' => 'siswa', 'nis' => '10002', 'major_id' => $rpl->id, 'grade_level' => 10]);

        $ortu = User::create(['name' => 'Ibu Sari Saputra', 'email' => 'ortu@neurotrack.test', 'password' => 'password', 'role' => 'orang_tua']);
        $ortu->children()->attach($andi->id, ['relationship' => 'Ibu']);

        $kelas = SchoolClass::create(['name' => 'X RPL 1', 'academic_year' => '2026/2027', 'homeroom_teacher_id' => $guru->id]);
        $kelas->syncStudents([$andi->id, $bella->id]);

        foreach ([$ind, $seni] as $umum) {
            ClassSubject::create(['class_id' => $kelas->id, 'subject_id' => $umum->id, 'teacher_id' => $guru2->id]);
        }
        $kelasMapel = collect([$mtk, $ing, $pemrograman])->mapWithKeys(fn ($s) => [
            $s->id => ClassSubject::create(['class_id' => $kelas->id, 'subject_id' => $s->id, 'teacher_id' => $guru->id]),
        ]);
        Assignment::create([
            'class_subject_id' => $kelasMapel[$mtk->id]->id, 'created_by' => $guru->id,
            'title' => 'Latihan Soal Persamaan Linear', 'description' => 'Kerjakan 10 soal di buku latihan, foto lalu unggah sebagai PDF atau gambar.',
            'deadline' => now()->addDays(7)->setTime(23, 59), 'max_score' => 100,
        ]);

        foreach ([
            [$admin, 'Selamat datang di NeuroTrack', "Mulai semester ini kita belajar dan absen lewat NeuroTrack. Masuk dengan akun dari sekolah ya.", 'all', null],
            [$admin, 'Pertemuan Orang Tua dan Wali Kelas', "Pertemuan orang tua dan wali kelas dilaksanakan Sabtu pekan depan pukul 09.00 di aula sekolah.", 'parents', null],
            [$guru, 'Ulangan Harian Matematika', "Ulangan harian Persamaan Linear hari Jumat. Kerjakan juga kuis latihannya di menu Kuis.", 'class', $kelas],
        ] as [$pembuat, $judul, $isi, $sasaran, $untukKelas]) {
            $pengumuman = new Announcement(['title' => $judul, 'content' => $isi, 'target_type' => $sasaran, 'published_at' => now()]);
            $pengumuman->created_by = $pembuat->id;
            $pengumuman->save();
            if ($untukKelas) {
                $pengumuman->classes()->attach($untukKelas->id);
            }
        }

        $this->call(GamificationSeeder::class);
        Gamification::grant($andi->id, Badge::where('name', 'Arena Contender')->firstOrFail(), $guru->id, 'Peserta Lomba Cerdas Cermat');
        XpTransaction::create(['user_id' => $bella->id, 'amount' => 60, 'source' => 'Kuis', 'description' => 'Contoh XP awal', 'reference' => 'seed:contoh']);
        Gamification::refresh($andi->id);
        Gamification::refresh($bella->id);

        $this->call(SkillSeeder::class);
        $nilai = SkillAssessment::create(['student_id' => $andi->id, 'assessed_by' => $guru->id, 'source_type' => 'teacher',
            'period_start' => now()->startOfMonth(), 'period_end' => now()->endOfMonth()]);
        foreach (Skill::softSkills() as $i => $skill) {
            $nilai->scores()->create(['skill_id' => $skill->id, 'score' => [4, 3, 3, 4, 5, 4][$i] ?? 3]);
        }

        $this->quiz($mtk, $guru, 'Persamaan Linear Satu Variabel', 10, [
            ['mc', 'Nilai x dari 2x + 6 = 14 adalah ...', ['3', '4', '5', '6'], 1],
            ['mc', 'Jika 3x = 21, maka x = ...', ['5', '6', '7', '8'], 2],
            ['mc', 'Hasil dari 15 + 3 × 4 adalah ...', ['27', '72', '24', '60'], 0],
            ['isian', 'Nilai x dari x − 9 = 10 adalah ...', ['19']],
        ]);
        $this->quiz($ing, $guru, 'Simple Present Tense', 8, [
            ['mc', 'She ___ to school every day.', ['go', 'goes', 'going', 'gone'], 1],
            ['mc', 'They ___ football on Sunday.', ['play', 'plays', 'playing', 'played'], 0],
            ['isian', 'Bentuk jamak dari "child" adalah ...', ['children']],
        ]);
        $this->quiz($ind, $guru2, 'Teks Prosedur dan Kata Baku', 8, [
            ['mc', 'Teks yang berisi langkah-langkah melakukan sesuatu disebut teks ...', ['Prosedur', 'Eksposisi', 'Narasi', 'Deskripsi'], 0],
            ['mc', 'Gagasan utama sebuah paragraf terdapat pada ...', ['Kalimat penjelas', 'Kalimat utama', 'Kalimat penutup', 'Judul'], 1],
            ['isian', 'Bentuk baku dari kata "apotik" adalah ...', ['apotek']],
        ]);
        $this->quiz($seni, $guru2, 'Seni Budaya Nusantara', 8, [
            ['mc', 'Tari Pendet berasal dari daerah ...', ['Bali', 'Jawa Barat', 'Sumatera Utara', 'Sulawesi'], 0],
            ['mc', 'Alat musik gamelan banyak dimainkan di ...', ['Papua', 'Jawa dan Bali', 'Aceh', 'Kalimantan'], 1],
            ['isian', 'Kain bermotif yang dibuat dengan teknik menutup memakai malam disebut ...', ['batik']],
        ]);
        $this->quiz($pemrograman, $guru, 'Dasar Algoritma', 10, [
            ['mc', 'Struktur kontrol untuk mengulang perintah disebut ...', ['Percabangan', 'Perulangan', 'Variabel', 'Fungsi'], 1],
            ['mc', 'Operator "==" pada kebanyakan bahasa pemrograman berfungsi untuk ...', ['Memberi nilai', 'Membandingkan kesamaan', 'Menjumlahkan', 'Mengurangi'], 1],
            ['isian', 'Urutan langkah logis untuk menyelesaikan masalah disebut ...', ['algoritma']],
        ]);
    }

    /** @param array<int, array> $questions  ['mc', soal, [pilihan], indexBenar] | ['isian', soal, [jawaban diterima]] */
    private function quiz(Subject $subject, User $teacher, string $title, int $minutes, array $questions): void
    {
        $quiz = new Quiz(['subject_id' => $subject->id, 'title' => $title, 'duration_minutes' => $minutes, 'passing_score' => 70, 'show_answers' => true, 'is_published' => true]);
        $quiz->teacher_id = $teacher->id;
        $quiz->save();

        foreach ($questions as $i => $q) {
            if ($q[0] === 'mc') {
                $question = $quiz->questions()->create(['type' => 'pilihan_ganda', 'body' => $q[1], 'points' => 1, 'position' => $i + 1]);
                foreach ($q[2] as $k => $body) {
                    $question->options()->create(['body' => $body, 'is_correct' => $k === $q[3]]);
                }
            } else {
                $quiz->questions()->create(['type' => 'isian', 'body' => $q[1], 'points' => 1, 'position' => $i + 1, 'accepted_answers' => $q[2]]);
            }
        }
    }
}
