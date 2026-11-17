<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\ClassSubject;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Skill;
use App\Models\SkillAssessment;
use App\Models\SkillAssessmentScore;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Seed E2E (Playwright). Berjalan hanya di luar APP_ENV=production.
 * Idempotent: hapus dan buat ulang data E2E di setiap run.
 *
 * Password  : env E2E_PASSWORD  (default: Password123!)
 * QR step   : env QR_INTERVAL_SECONDS (default: 20) → disimpan sebagai ATTENDANCE_QR_STEP
 */
class E2ESeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->error('E2ESeeder DITOLAK di production.');

            return;
        }

        $password = env('E2E_PASSWORD', 'Password123!');
        $qrStep = (int) env('QR_INTERVAL_SECONDS', 20);

        // Pastikan config attendance.qr_step mengikuti env saat ini
        // (di lingkungan e2e, QR_INTERVAL_SECONDS sudah masuk .env.e2e)

        $this->command?->info('E2ESeeder: membersihkan data lama…');
        $this->teardown();

        $this->command?->info('E2ESeeder: membuat data baru…');

        // ----------------------------------------------------------------
        // Jurusan
        // ----------------------------------------------------------------
        $rpl = Major::firstOrCreate(['code' => 'RPL'], ['name' => 'Rekayasa Perangkat Lunak']);
        $tkj = Major::firstOrCreate(['code' => 'TKJ'], ['name' => 'Teknik Komputer dan Jaringan']);
        // Jurusan dengan < 3 sumbu (hanya punya 1 mapel kejuruan + 0 umum di seed ini)
        $fewMajor = Major::firstOrCreate(['code' => 'AKL'], ['name' => 'Akuntansi dan Keuangan Lembaga']);

        // ----------------------------------------------------------------
        // Mata pelajaran
        // ----------------------------------------------------------------
        // Umum (untuk semua jurusan → radar X RPL)
        $sejarah = Subject::firstOrCreate(['name' => 'Sejarah'], ['type' => 'umum', 'icon' => 'mapel-sejarah']);
        $mtk = Subject::firstOrCreate(['name' => 'Matematika E2E'], ['type' => 'umum', 'icon' => 'mapel-mtk']);
        $bing = Subject::firstOrCreate(['name' => 'Bahasa Inggris E2E'], ['type' => 'umum', 'icon' => 'mapel-english']);

        // Kejuruan RPL
        $dpkRpl = Subject::firstOrCreate(
            ['name' => 'DPK-RPL'],
            ['type' => 'kejuruan', 'major_id' => $rpl->id, 'icon' => 'mapel-rpl']
        );

        // Kejuruan TKJ (hanya untuk kelas TKJ — tidak masuk kelas X RPL 1)
        $dpkTkj = Subject::firstOrCreate(
            ['name' => 'DPK-TKJ'],
            ['type' => 'kejuruan', 'major_id' => $tkj->id, 'icon' => 'mapel-rpl']
        );

        // Jurusan < 3 sumbu: hanya 1 mapel kejuruan (tidak ada umum — agar total < 3)
        $dpkFew = Subject::firstOrCreate(
            ['name' => 'DPK-AKL'],
            ['type' => 'kejuruan', 'major_id' => $fewMajor->id, 'icon' => 'mapel-rpl']
        );

        // ----------------------------------------------------------------
        // Skill (soft skill) — gunakan SkillSeeder supaya konsisten
        // ----------------------------------------------------------------
        (new SkillSeeder)->run();
        $softSkills = Skill::softSkills();

        // ----------------------------------------------------------------
        // Akun staf
        // ----------------------------------------------------------------
        $makeStaff = fn (string $name, string $email, string $role, ?Major $major = null) => User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role' => $role,
                'major_id' => $major?->id,
                'must_change_password' => false,
                'password_changed_at' => now(),
            ]
        );

        $admin = $makeStaff('Admin E2E', 'admin@neurotrack.test', 'admin');
        $kepsek = $makeStaff('Kepsek E2E', 'kepsek@neurotrack.test', 'admin'); // kepsek pakai role admin/guru
        $guruRpl = $makeStaff('Guru RPL E2E', 'guru.rpl@neurotrack.test', 'guru');
        $guruTkj = $makeStaff('Guru TKJ E2E', 'guru.tkj@neurotrack.test', 'guru');
        $wali = $makeStaff('Wali X RPL 1 E2E', 'wali.xrpl1@neurotrack.test', 'guru');

        // Pastikan password diperbarui jika seeder dijalankan ulang
        foreach ([$admin, $kepsek, $guruRpl, $guruTkj, $wali] as $u) {
            $u->update([
                'password' => Hash::make($password),
                'must_change_password' => false,
                'password_changed_at' => now(),
            ]);
        }

        // Penugasan mapel guru
        $guruRpl->subjects()->syncWithoutDetaching([$dpkRpl->id, $sejarah->id, $mtk->id, $bing->id]);
        $guruTkj->subjects()->syncWithoutDetaching([$dpkTkj->id]);
        $wali->subjects()->syncWithoutDetaching([$sejarah->id, $mtk->id, $bing->id, $dpkRpl->id]);

        // ----------------------------------------------------------------
        // Kelas X RPL 1
        // ----------------------------------------------------------------
        $xRpl1 = SchoolClass::firstOrCreate(
            ['name' => 'X RPL 1'],
            ['academic_year' => '2026/2027', 'homeroom_teacher_id' => $wali->id]
        );
        $xRpl1->update(['homeroom_teacher_id' => $wali->id]);

        // Mapel di kelas X RPL 1
        ClassSubject::firstOrCreate(['class_id' => $xRpl1->id, 'subject_id' => $sejarah->id], ['teacher_id' => $guruRpl->id]);
        ClassSubject::firstOrCreate(['class_id' => $xRpl1->id, 'subject_id' => $mtk->id], ['teacher_id' => $guruRpl->id]);
        ClassSubject::firstOrCreate(['class_id' => $xRpl1->id, 'subject_id' => $bing->id], ['teacher_id' => $guruRpl->id]);
        ClassSubject::firstOrCreate(['class_id' => $xRpl1->id, 'subject_id' => $dpkRpl->id], ['teacher_id' => $guruRpl->id]);

        // ----------------------------------------------------------------
        // 35 Siswa X RPL 1 (NIS 2026001-2026035)
        // ----------------------------------------------------------------
        $xRpl1Students = [];
        for ($n = 1; $n <= 35; $n++) {
            $nis = '202600'.(str_pad((string) $n, 2, '0', STR_PAD_LEFT));
            $nis = '2026'.str_pad((string) $n, 3, '0', STR_PAD_LEFT);
            $email = "siswa.{$nis}@neurotrack.test";
            $s = User::firstOrCreate(
                ['nis' => $nis],
                [
                    'name' => "Siswa E2E {$nis}",
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => 'siswa',
                    'major_id' => $rpl->id,
                    'grade_level' => 10,
                    'must_change_password' => false,
                    'password_changed_at' => now(),
                ]
            );
            $s->update([
                'password' => Hash::make($password),
                'must_change_password' => false,
                'password_changed_at' => now(),
            ]);
            $xRpl1Students[$nis] = $s;
        }
        $xRpl1->syncStudents(collect($xRpl1Students)->pluck('id')->values()->all());

        // Orang tua NIS 2026001
        $ortu = User::firstOrCreate(
            ['email' => 'ortu.2026001@neurotrack.test'],
            [
                'name' => 'Ortu Siswa 2026001',
                'password' => Hash::make($password),
                'role' => 'orang_tua',
                'must_change_password' => false,
                'password_changed_at' => now(),
            ]
        );
        $ortu->update(['password' => Hash::make($password)]);
        $siswa2026001 = $xRpl1Students['2026001'];
        DB::table('parent_student')->updateOrInsert(
            ['parent_id' => $ortu->id, 'student_id' => $siswa2026001->id],
            ['relationship' => 'Orang Tua', 'created_at' => now(), 'updated_at' => now()]
        );

        // ----------------------------------------------------------------
        // Kelas X TKJ 1 — siswa 2026101
        // ----------------------------------------------------------------
        $xTkj1 = SchoolClass::firstOrCreate(
            ['name' => 'X TKJ 1'],
            ['academic_year' => '2026/2027', 'homeroom_teacher_id' => $guruTkj->id]
        );
        $xTkj1->update(['homeroom_teacher_id' => $guruTkj->id]);
        ClassSubject::firstOrCreate(['class_id' => $xTkj1->id, 'subject_id' => $dpkTkj->id], ['teacher_id' => $guruTkj->id]);

        $siswa2026101 = User::firstOrCreate(
            ['nis' => '2026101'],
            [
                'name' => 'Siswa TKJ 2026101',
                'email' => 'siswa.2026101@neurotrack.test',
                'password' => Hash::make($password),
                'role' => 'siswa',
                'major_id' => $tkj->id,
                'grade_level' => 10,
                'must_change_password' => false,
                'password_changed_at' => now(),
            ]
        );
        $siswa2026101->update(['password' => Hash::make($password)]);
        $xTkj1->syncStudents([$siswa2026101->id]);

        // ----------------------------------------------------------------
        // Kelas XI RPL — struktur mapel TANPA Sejarah
        // ----------------------------------------------------------------
        $xiRpl1 = SchoolClass::firstOrCreate(
            ['name' => 'XI RPL 1'],
            ['academic_year' => '2026/2027', 'homeroom_teacher_id' => $wali->id]
        );
        $xiRpl1->update(['homeroom_teacher_id' => $wali->id]);
        // XI hanya punya Matematika, B.Inggris, DPK-RPL (tanpa Sejarah)
        ClassSubject::firstOrCreate(['class_id' => $xiRpl1->id, 'subject_id' => $mtk->id], ['teacher_id' => $guruRpl->id]);
        ClassSubject::firstOrCreate(['class_id' => $xiRpl1->id, 'subject_id' => $bing->id], ['teacher_id' => $guruRpl->id]);
        ClassSubject::firstOrCreate(['class_id' => $xiRpl1->id, 'subject_id' => $dpkRpl->id], ['teacher_id' => $guruRpl->id]);

        $siswa2025001 = User::firstOrCreate(
            ['nis' => '2025001'],
            [
                'name' => 'Siswa XI RPL 2025001',
                'email' => 'siswa.2025001@neurotrack.test',
                'password' => Hash::make($password),
                'role' => 'siswa',
                'major_id' => $rpl->id,
                'grade_level' => 11,
                'must_change_password' => false,
                'password_changed_at' => now(),
            ]
        );
        $siswa2025001->update(['password' => Hash::make($password)]);

        // NIS 2025950 — siswa XI RPL yang dapat dinaikkan
        $siswa2025950 = User::firstOrCreate(
            ['nis' => '2025950'],
            [
                'name' => 'Siswa XI Promotable 2025950',
                'email' => 'siswa.2025950@neurotrack.test',
                'password' => Hash::make($password),
                'role' => 'siswa',
                'major_id' => $rpl->id,
                'grade_level' => 11,
                'must_change_password' => false,
                'password_changed_at' => now(),
            ]
        );
        $siswa2025950->update(['password' => Hash::make($password)]);

        $xiRpl1->syncStudents([$siswa2025001->id, $siswa2025950->id]);

        // ----------------------------------------------------------------
        // Siswa kasus rate kehadiran
        // ----------------------------------------------------------------
        // NIS 2026900: 60 hari efektif, 2 dispensasi, Sakit 2, Izin 1, Alpa 3 → 89,7%
        // Rate = (60-2-2-1-3)/(60-2) = 52/58 ... hmm, let's re-read the formula
        // AttendanceRate: COUNTED = present|late|sick|permission|absent (dispensasi tidak masuk)
        // ATTENDED = present|late
        // Rate = attended/counted * 100
        // 60 hari efektif: counted = 60-2 dispensasi = 58... tapi dispensasi tidak dihitung sama sekali
        // Jadi: 60 hari efektif total. 2 dispensasi (tidak dihitung). Sakit 2, Izin 1, Alpa 3 = 6 tidak masuk
        // counted = 60 - 2 = 58 (dispensasi tidak masuk counted)
        // attended = 58 - 2 - 1 - 3 = 52
        // rate = 52/58 * 100 = 89.655... ≈ 89.7% ✓
        $siswa2026900 = $this->makeStudentRateCase('2026900', $rpl, 10, $password);

        // Kelas khusus untuk siswa rate case (tidak perlu kelas nyata, langsung buat attendance)
        // Gunakan kelas fiktif/dummy yang sudah ada atau buat kelas sementara untuk seed
        $rateClass = SchoolClass::firstOrCreate(
            ['name' => 'Rate Case E2E'],
            ['academic_year' => '2026/2027', 'homeroom_teacher_id' => $guruRpl->id]
        );
        $rateClass->syncStudents([$siswa2026900->id]);

        // 60 hari efektif: 2 dispensasi, 2 sakit, 1 izin, 3 alfa → sisanya hadir (52)
        $this->seedAttendanceForRate(
            student: $siswa2026900,
            class: $rateClass,
            teacher: $guruRpl,
            dispensasi: 2,
            sakit: 2,
            izin: 1,
            alfa: 3,
            hadir: 52,
            isFinalized: true
        );

        // NIS 2025901: PKL 3 bulan, rate 100%
        $siswa2025901 = $this->makeStudentRateCase('2025901', $rpl, 11, $password);
        $rateClass->syncStudents(array_merge(
            DB::table('class_memberships')->where('class_id', $rateClass->id)->whereNull('left_at')->pluck('student_id')->all(),
            [$siswa2025901->id]
        ));
        // PKL = dispensasi semua (rate 100% karena dispensasi tidak dihitung)
        $this->seedAttendanceForRate(
            student: $siswa2025901,
            class: $rateClass,
            teacher: $guruRpl,
            dispensasi: 60,
            sakit: 0,
            izin: 0,
            alfa: 0,
            hadir: 0,
            isFinalized: true
        );

        // NIS 2026902: 5× terlambat, tanpa alfa, rate 100%
        $siswa2026902 = $this->makeStudentRateCase('2026902', $rpl, 10, $password);
        $rateClass->syncStudents(array_merge(
            DB::table('class_memberships')->where('class_id', $rateClass->id)->whereNull('left_at')->pluck('student_id')->all(),
            [$siswa2026902->id]
        ));
        // 5 terlambat + 15 hadir = 20 hari, rate = 20/20 = 100%
        $this->seedAttendanceForRate(
            student: $siswa2026902,
            class: $rateClass,
            teacher: $guruRpl,
            dispensasi: 0,
            sakit: 0,
            izin: 0,
            alfa: 0,
            hadir: 15,
            isFinalized: true,
            terlambat: 5
        );

        // ----------------------------------------------------------------
        // Siswa dengan struktur mapel < 3 sumbu (NIS 2026910)
        // ----------------------------------------------------------------
        // Jurusan AKL hanya punya 1 mapel (DPK-AKL) + tidak ada mapel umum di kelasnya
        // → hardSkills() akan menghasilkan array dengan 1 elemen
        $siswa2026910 = User::firstOrCreate(
            ['nis' => '2026910'],
            [
                'name' => 'Siswa Few Axes 2026910',
                'email' => 'siswa.2026910@neurotrack.test',
                'password' => Hash::make($password),
                'role' => 'siswa',
                'major_id' => $fewMajor->id,
                'grade_level' => 10,
                'must_change_password' => false,
                'password_changed_at' => now(),
            ]
        );
        $siswa2026910->update(['password' => Hash::make($password)]);

        // Kelas AKL dengan hanya 1 mapel
        $fewClass = SchoolClass::firstOrCreate(
            ['name' => 'X AKL 1 E2E'],
            ['academic_year' => '2026/2027', 'homeroom_teacher_id' => $guruRpl->id]
        );
        ClassSubject::firstOrCreate(['class_id' => $fewClass->id, 'subject_id' => $dpkFew->id], ['teacher_id' => $guruRpl->id]);
        $fewClass->syncStudents([$siswa2026910->id]);

        // ----------------------------------------------------------------
        // Siswa NIS 2026003: harus punya ≥1 sumbu radar belum bernilai di kelas X RPL 1
        // (cukup biarkan tanpa attempt kuis → hardSkills akan null untuk mapel yang belum ada nilai)
        // ----------------------------------------------------------------
        // NIS 2026003 sudah ada di $xRpl1Students, tidak perlu tambahan data

        // ----------------------------------------------------------------
        // Periode penilaian & soft skill
        // ----------------------------------------------------------------
        // Periode terbuka: NIS 2026001 belum punya nilai DPK-RPL
        // (tidak ada nilai = terbuka, karena tidak ada tabel periode terpisah — dicatat lewat SkillAssessment)

        // Soft skill terbuka untuk wali dan guru mapel
        // Buat satu penilaian soft skill dari semester lalu untuk poligon pembanding (NIS 2026001)
        $lastSemesterEnd = Carbon::now()->subMonths(6)->endOfMonth()->toDateString();
        $lastSemesterStart = Carbon::now()->subMonths(6)->startOfMonth()->toDateString();

        $prevAssessment = SkillAssessment::firstOrCreate(
            [
                'student_id' => $siswa2026001->id,
                'assessed_by' => $guruRpl->id,
                'source_type' => 'teacher',
                'period_start' => $lastSemesterStart,
                'period_end' => $lastSemesterEnd,
            ],
            ['assessed_at' => Carbon::now()->subMonths(6)]
        );
        foreach ($softSkills as $skill) {
            $prevAssessment->scores()->firstOrCreate(
                ['skill_id' => $skill->id],
                ['score' => 3]
            );
        }

        // Hari belum difinalisasi untuk guru.rpl:
        // Buat sesi hari ini untuk kelas X RPL 1 yang belum di-finalize (closed_at = null)
        // Ini sudah ada secara default karena "sesi hari ini belum ada" menurut kontrak seed.
        // Tapi test 02-rate menginginkan guru.rpl melihat "unfinalized-warning".
        // Kita buat sesi kemarin yang belum ditutup (attendances kemarin dengan open session).
        $yesterday = Carbon::yesterday();
        $unfinalSession = AttendanceSession::firstOrCreate(
            ['class_id' => $xRpl1->id, 'attendance_date' => $yesterday->toDateString()],
            [
                'teacher_id' => $guruRpl->id,
                'opened_at' => $yesterday->copy()->setTime(7, 0),
                // closed_at NULL = belum difinalisasi
            ]
        );
        // Buat beberapa catatan kehadiran kemarin (tidak semua siswa → memunculkan warning)
        $firstStudent = $xRpl1Students['2026001'];
        Attendance::firstOrCreate(
            ['student_id' => $firstStudent->id, 'class_id' => $xRpl1->id, 'attendance_date' => $yesterday->toDateString()],
            [
                'recorded_by' => $guruRpl->id,
                'attendance_session_id' => $unfinalSession->id,
                'status' => 'present',
                'check_in_at' => $yesterday->copy()->setTime(7, 5),
                'method' => 'qr',
            ]
        );

        $this->command?->info('E2ESeeder: selesai.');
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    private function makeStudentRateCase(string $nis, Major $major, int $gradeLevel, string $password): User
    {
        $s = User::firstOrCreate(
            ['nis' => $nis],
            [
                'name' => "Siswa Rate {$nis}",
                'email' => "siswa.{$nis}@neurotrack.test",
                'password' => Hash::make($password),
                'role' => 'siswa',
                'major_id' => $major->id,
                'grade_level' => $gradeLevel,
                'must_change_password' => false,
                'password_changed_at' => now(),
            ]
        );
        $s->update([
            'password' => Hash::make($password),
            'must_change_password' => false,
            'password_changed_at' => now(),
        ]);

        return $s;
    }

    /**
     * Buat catatan kehadiran masa lalu untuk siswa kasus rate.
     * Semua sesi dibuat closed (closed_at != null) = terfinalisasi.
     */
    private function seedAttendanceForRate(
        User $student,
        SchoolClass $class,
        User $teacher,
        int $dispensasi,
        int $sakit,
        int $izin,
        int $alfa,
        int $hadir,
        bool $isFinalized,
        int $terlambat = 0
    ): void {
        $statuses = array_merge(
            array_fill(0, $hadir, 'present'),
            array_fill(0, $terlambat, 'late'),
            array_fill(0, $sakit, 'sick'),
            array_fill(0, $izin, 'permission'),
            array_fill(0, $alfa, 'absent'),
            array_fill(0, $dispensasi, 'dispensation'),
        );

        // Mulai dari 70 hari yang lalu, lewati akhir pekan
        $date = Carbon::now()->subDays(70);
        $idx = 0;

        while ($idx < count($statuses)) {
            // Lewati akhir pekan
            if ($date->isWeekend()) {
                $date->addDay();

                continue;
            }

            $status = $statuses[$idx];
            $dateStr = $date->toDateString();

            $session = AttendanceSession::firstOrCreate(
                ['class_id' => $class->id, 'attendance_date' => $dateStr],
                [
                    'teacher_id' => $teacher->id,
                    'opened_at' => $date->copy()->setTime(7, 0),
                    'closed_at' => $isFinalized ? $date->copy()->setTime(14, 0) : null,
                ]
            );

            Attendance::firstOrCreate(
                ['student_id' => $student->id, 'class_id' => $class->id, 'attendance_date' => $dateStr],
                [
                    'recorded_by' => $teacher->id,
                    'attendance_session_id' => $session->id,
                    'status' => $status,
                    'check_in_at' => in_array($status, ['present', 'late'])
                        ? $date->copy()->setTime(7, rand(0, 30))
                        : null,
                    'method' => in_array($status, ['present', 'late']) ? 'qr' : 'manual',
                ]
            );

            $date->addDay();
            $idx++;
        }
    }

    /**
     * Hapus semua data E2E (berdasarkan email/NIS dengan domain neurotrack.test atau prefix E2E).
     */
    private function teardown(): void
    {
        // Ambil semua user E2E
        $e2eEmails = [
            'admin@neurotrack.test', 'kepsek@neurotrack.test',
            'guru.rpl@neurotrack.test', 'guru.tkj@neurotrack.test',
            'wali.xrpl1@neurotrack.test', 'ortu.2026001@neurotrack.test',
        ];

        // Siswa dengan NIS E2E
        $e2eNisPatterns = ['2026%', '2025%'];
        $studentIds = User::where(function ($q) use ($e2eNisPatterns) {
            foreach ($e2eNisPatterns as $pattern) {
                $q->orWhere('nis', 'like', $pattern);
            }
        })->pluck('id')->all();

        $staffIds = User::whereIn('email', $e2eEmails)->pluck('id')->all();
        $allIds = array_merge($staffIds, $studentIds);

        if (empty($allIds)) {
            return;
        }

        // Hapus attendance records
        Attendance::whereIn('student_id', $studentIds)->delete();
        Attendance::whereIn('recorded_by', $allIds)->delete();

        // Hapus attendance sessions
        $classIds = SchoolClass::where(function ($q) use ($staffIds) {
            $q->whereIn('homeroom_teacher_id', $staffIds)
                ->orWhere('name', 'like', '%E2E%')
                ->orWhereIn('name', ['X RPL 1', 'X TKJ 1', 'XI RPL 1', 'Rate Case E2E', 'X AKL 1 E2E']);
        })->pluck('id')->all();

        if ($classIds) {
            AttendanceSession::whereIn('class_id', $classIds)->delete();
        }

        // Hapus skill assessments
        SkillAssessmentScore::whereHas('assessment', fn ($q) => $q->whereIn('student_id', $studentIds))->delete();
        SkillAssessment::whereIn('student_id', $studentIds)->delete();
    }
}
