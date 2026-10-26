<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'stats' => [
                ['icon' => 'users', 'tone' => 'purple', 'value' => User::where('role', 'siswa')->count(), 'label' => 'Siswa Aktif'],
                ['icon' => 'book', 'tone' => 'green', 'value' => Quiz::where('is_published', true)->count(), 'label' => 'Kuis'],
                ['icon' => 'star', 'tone' => 'yellow', 'value' => Subject::count(), 'label' => 'Mata Pelajaran'],
            ],
            'features' => [
                ['tone' => 'blue', 'img' => 'grad-cap', 'title' => 'Kuis dari Guru', 'text' => 'Soal disusun langsung oleh guru mapelmu'],
                ['tone' => 'pink', 'img' => 'play', 'title' => 'Belajar Interaktif', 'text' => 'Pilihan ganda dan isian singkat, langsung dinilai'],
                ['tone' => 'green', 'img' => 'chart', 'title' => 'Pantau Progres', 'text' => 'Lihat perkembangan nilaimu secara real-time'],
                ['tone' => 'yellow', 'img' => 'megaphone', 'title' => 'Segera Hadir', 'text' => 'Absensi, statistik kemampuan, tugas, dan pengumuman'],
            ],
            // Satu kuis terbaru per mapel supaya katalog memperlihatkan beragam mapel (umum dulu, lalu kejuruan).
            'quizzes' => Quiz::where('is_published', true)->with('subject')->withCount('questions')->latest()->get()
                ->unique('subject_id')
                ->sortBy(fn ($q) => [$q->subject->type === 'umum' ? 0 : 1, $q->subject->name])
                ->take(6)->values(),
            'subjects' => Subject::with('major')->orderBy('type')->orderBy('name')->get(),
            'progressPoints' => [
                ['icon' => 'bar-chart', 'tone' => 'blue', 'text' => 'Statistik belajar real-time'],
                ['icon' => 'check-square', 'tone' => 'green', 'text' => 'Nilai terbaik tiap kuis tersimpan'],
                ['icon' => 'flame', 'tone' => 'orange', 'text' => 'Catat hari belajar berturut-turut'],
                ['icon' => 'star', 'tone' => 'yellow', 'text' => 'Statistik kemampuan segera hadir'],
            ],
        ]);
    }
}
