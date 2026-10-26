<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\Quiz;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();
        $availableIds = Quiz::availableFor($user)->pluck('id');

        $finished = Attempt::with('quiz:id,passing_score')
            ->where('user_id', $user->id)->where('status', 'selesai')->get();

        $best = $finished->groupBy('quiz_id')->map(fn ($g) => $g->max('score'));
        $completed = $best->keys()->intersect($availableIds)->count();
        $passed = $finished->groupBy('quiz_id')
            ->filter(fn ($g) => $g->max('score') >= $g->first()->quiz->passing_score)->count();

        $minutes = $finished->sum(fn ($a) => $a->started_at->diffInMinutes($a->finished_at));

        // Hari berturut-turut: dihitung mundur dari hari ini (atau kemarin).
        $days = $finished->map(fn ($a) => $a->finished_at->toDateString())->unique()->flip();
        $cursor = Carbon::today();
        if (! $days->has($cursor->toDateString())) {
            $cursor->subDay();
        }
        $streak = 0;
        while ($days->has($cursor->toDateString())) {
            $streak++;
            $cursor->subDay();
        }

        return view('siswa.dashboard', [
            'available' => $availableIds->count(),
            'completed' => $completed,
            'progress' => $availableIds->count() ? (int) round($completed / $availableIds->count() * 100) : 0,
            'average' => $best->isNotEmpty() ? round($best->avg(), 1) : null,
            'passed' => $passed,
            'hours' => round($minutes / 60, 1),
            'streak' => $streak,
            'running' => Attempt::with('quiz.subject')->where('user_id', $user->id)->where('status', 'berjalan')->latest()->get()
                ->filter(fn ($a) => $a->deadline()->isFuture()),
            'latest' => Quiz::availableFor($user)->with('subject')->withCount('questions')->latest()->take(4)->get(),
            'recent' => Attempt::with('quiz.subject')->where('user_id', $user->id)->where('status', 'selesai')->latest('finished_at')->take(5)->get(),
        ]);
    }
}
