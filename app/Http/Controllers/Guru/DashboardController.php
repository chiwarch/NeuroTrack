<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\Question;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();
        $quizIds = $user->quizzes()->pluck('id');

        return view('guru.dashboard', [
            'subjects' => $user->subjects()->orderBy('name')->get(),
            'quizCount' => $quizIds->count(),
            'publishedCount' => $user->quizzes()->where('is_published', true)->count(),
            'questionCount' => Question::whereIn('quiz_id', $quizIds)->count(),
            'attemptCount' => Attempt::whereIn('quiz_id', $quizIds)->where('status', 'selesai')->count(),
            'recent' => Attempt::with('user:id,name', 'quiz:id,title,passing_score')
                ->whereIn('quiz_id', $quizIds)->where('status', 'selesai')->latest('finished_at')->take(8)->get(),
        ]);
    }
}
