<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\Major;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'students' => User::where('role', 'siswa')->count(),
            'teachers' => User::where('role', 'guru')->count(),
            'majors' => Major::count(),
            'subjects' => Subject::count(),
            'quizzes' => Quiz::where('is_published', true)->count(),
            'attempts' => Attempt::where('status', 'selesai')->count(),
            'unassigned' => Subject::doesntHave('teachers')->orderBy('name')->get(),
        ]);
    }
}
