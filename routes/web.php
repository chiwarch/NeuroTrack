<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Ortu;
use App\Http\Controllers\AnnouncementManageController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Guru;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\Siswa;
use App\Http\Middleware\EnsureRole;
use Illuminate\Support\Facades\Route;

// ---- Publik ----
Route::get('/', HomeController::class)->name('home');
Route::post('/newsletter', [NewsletterController::class, 'store'])->middleware('throttle:5,1')->name('newsletter.store');

Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
Route::post('/masuk', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');
Route::post('/keluar', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ---- Wajib login ----
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Siswa
    Route::middleware(EnsureRole::class.':siswa')->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/', Siswa\DashboardController::class)->name('dashboard');
        Route::get('/kuis', [Siswa\QuizController::class, 'index'])->name('quizzes.index');
        Route::get('/kuis/{quiz}', [Siswa\QuizController::class, 'show'])->name('quizzes.show');
        Route::post('/kuis/{quiz}/mulai', [Siswa\QuizController::class, 'start'])->name('quizzes.start');
        Route::get('/riwayat', [Siswa\QuizController::class, 'history'])->name('history');
        Route::get('/statistik', Siswa\StatsController::class)->name('stats');
        Route::get('/pengumuman', [Siswa\AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/prestasi', [Siswa\AchievementController::class, 'index'])->name('achievements');
        Route::get('/peringkat', LeaderboardController::class)->name('leaderboard');

        Route::get('/tugas', [Siswa\AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/tugas/{assignment}', [Siswa\AssignmentController::class, 'show'])->name('assignments.show');
        Route::post('/tugas/{assignment}', [Siswa\AssignmentController::class, 'submit'])->middleware('throttle:20,1')->name('assignments.submit');
        Route::get('/tugas/{assignment}/berkas', [Siswa\AssignmentController::class, 'file'])->name('assignments.file');

        Route::get('/absensi', [Siswa\AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/absen', [Siswa\AttendanceController::class, 'scan'])->name('attendance.scan');
        Route::post('/absen', [Siswa\AttendanceController::class, 'checkIn'])->middleware('throttle:20,1')->name('attendance.checkin');
        Route::get('/percobaan/{attempt}', [Siswa\AttemptController::class, 'show'])->name('attempts.show');
        Route::post('/percobaan/{attempt}', [Siswa\AttemptController::class, 'submit'])->name('attempts.submit');
        Route::get('/percobaan/{attempt}/hasil', [Siswa\AttemptController::class, 'result'])->name('attempts.result');
    });

    // Orang tua
    Route::middleware(EnsureRole::class.':orang_tua')->prefix('orang-tua')->name('orang_tua.')->group(function () {
        Route::get('/', Ortu\DashboardController::class)->name('dashboard');
        Route::get('/anak/{student}', [Ortu\ChildController::class, 'show'])->name('children.show');
        Route::get('/pengumuman', [Ortu\AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/peringkat', LeaderboardController::class)->name('leaderboard');
    });

    // Guru
    Route::middleware(EnsureRole::class.':guru')->prefix('guru')->name('guru.')->group(function () {
        Route::get('/', Guru\DashboardController::class)->name('dashboard');
        Route::resource('kuis', Guru\QuizController::class)->parameters(['kuis' => 'quiz'])->except('show')->names('quizzes');
        Route::get('kuis/{quiz}/hasil', [Guru\QuizController::class, 'results'])->name('quizzes.results');
        Route::get('kuis/{quiz}/soal/buat', [Guru\QuestionController::class, 'create'])->name('questions.create');
        Route::post('kuis/{quiz}/soal', [Guru\QuestionController::class, 'store'])->name('questions.store');
        Route::get('kuis/{quiz}/soal/{question}/ubah', [Guru\QuestionController::class, 'edit'])->name('questions.edit');
        Route::put('kuis/{quiz}/soal/{question}', [Guru\QuestionController::class, 'update'])->name('questions.update');
        Route::delete('kuis/{quiz}/soal/{question}', [Guru\QuestionController::class, 'destroy'])->name('questions.destroy');

        Route::resource('pengumuman', AnnouncementManageController::class)->parameters(['pengumuman' => 'announcement'])->except('show')->names('announcements');

        Route::get('penghargaan', [AwardController::class, 'index'])->name('awards.index');
        Route::post('penghargaan', [AwardController::class, 'store'])->name('awards.store');
        Route::delete('penghargaan/{userBadge}', [AwardController::class, 'destroy'])->name('awards.destroy');
        Route::get('peringkat', LeaderboardController::class)->name('leaderboard');

        Route::get('tugas/berkas/{submission}', [Guru\AssignmentController::class, 'file'])->name('assignments.file');
        Route::resource('tugas', Guru\AssignmentController::class)->parameters(['tugas' => 'assignment'])->names('assignments');
        Route::post('tugas/{assignment}/nilai', [Guru\AssignmentController::class, 'grade'])->name('assignments.grade');

        Route::get('siswa', [Guru\StudentController::class, 'index'])->name('students.index');
        Route::get('siswa/{student}', [Guru\StudentController::class, 'show'])->name('students.show');
        Route::post('siswa/{student}/nilai', [Guru\StudentController::class, 'rate'])->name('students.rate');

        Route::get('absensi', [Guru\AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('absensi/kelas/{class}/mulai', [Guru\AttendanceController::class, 'open'])->name('attendance.open');
        Route::get('absensi/{session}', [Guru\AttendanceController::class, 'show'])->name('attendance.show');
        Route::get('absensi/{session}/live', [Guru\AttendanceController::class, 'live'])->name('attendance.live');
        Route::post('absensi/{session}', [Guru\AttendanceController::class, 'update'])->name('attendance.update');
        Route::post('absensi/{session}/tutup', [Guru\AttendanceController::class, 'close'])->name('attendance.close');
        Route::post('absensi/{session}/buka', [Guru\AttendanceController::class, 'reopen'])->name('attendance.reopen');
    });

    // Admin
    Route::middleware(EnsureRole::class.':admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');
        Route::resource('jurusan', Admin\MajorController::class)->parameters(['jurusan' => 'major'])->except('show')->names('majors');
        Route::resource('mapel', Admin\SubjectController::class)->parameters(['mapel' => 'subject'])->except('show')->names('subjects');
        Route::resource('pengumuman', AnnouncementManageController::class)->parameters(['pengumuman' => 'announcement'])->except('show')->names('announcements');
        Route::get('penghargaan', [AwardController::class, 'index'])->name('awards.index');
        Route::post('penghargaan', [AwardController::class, 'store'])->name('awards.store');
        Route::delete('penghargaan/{userBadge}', [AwardController::class, 'destroy'])->name('awards.destroy');
        Route::get('peringkat', LeaderboardController::class)->name('leaderboard');
        Route::resource('lencana', Admin\BadgeController::class)->parameters(['lencana' => 'badge'])->except('show')->names('badges');
        Route::resource('kelas', Admin\ClassController::class)->parameters(['kelas' => 'class'])->except('show')->names('classes');
        Route::resource('pengguna', Admin\UserController::class)->parameters(['pengguna' => 'user'])->except('show')->names('users');
    });
});
