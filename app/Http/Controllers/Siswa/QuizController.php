<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\Quiz;
use App\Models\Subject;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    private function authorizeQuiz(Quiz $quiz): void
    {
        abort_unless(Quiz::availableFor(auth()->user())->whereKey($quiz->id)->exists(), 404);
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $quizzes = Quiz::availableFor($user)->with('subject')->withCount('questions')
            ->when($request->filled('mapel'), fn ($q) => $q->where('subject_id', $request->integer('mapel')))
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->latest()->paginate(12)->withQueryString();

        $best = Attempt::where('user_id', $user->id)->where('status', 'selesai')
            ->selectRaw('quiz_id, max(score) as best')->groupBy('quiz_id')->pluck('best', 'quiz_id');

        $subjects = Subject::where(fn ($s) => $s->where('type', 'umum')->orWhere('major_id', $user->major_id))
            ->orderBy('type')->orderBy('name')->get();

        return view('siswa.quizzes', compact('quizzes', 'best', 'subjects'));
    }

    public function show(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);
        $user = auth()->user();
        $quiz->load('subject', 'teacher')->loadCount('questions');

        $attempts = Attempt::where('user_id', $user->id)->where('quiz_id', $quiz->id)->latest()->get();
        $running = $attempts->first(fn ($a) => $a->status === 'berjalan' && $a->deadline()->isFuture());

        return view('siswa.quiz-show', [
            'quiz' => $quiz,
            'attempts' => $attempts->where('status', '!=', 'berjalan'),
            'running' => $running,
            'best' => $attempts->where('status', 'selesai')->max('score'),
            'points' => $quiz->totalPoints(),
        ]);
    }

    public function start(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);
        $user = auth()->user();

        if ($quiz->questions()->doesntExist()) {
            return back()->with('error', 'Kuis ini belum punya soal.');
        }

        $running = Attempt::where('user_id', $user->id)->where('quiz_id', $quiz->id)->where('status', 'berjalan')->latest()->first();
        if ($running && $running->deadline()->isFuture()) {
            return redirect()->route('siswa.attempts.show', $running);
        }
        if ($running) {
            $running->update(['status' => 'kedaluwarsa', 'finished_at' => now(), 'score' => 0]);
        }

        $attempt = Attempt::create(['quiz_id' => $quiz->id, 'user_id' => $user->id, 'started_at' => now(), 'status' => 'berjalan']);

        return redirect()->route('siswa.attempts.show', $attempt);
    }

    public function history()
    {
        $attempts = Attempt::with('quiz.subject')->where('user_id', auth()->id())->where('status', '!=', 'berjalan')
            ->latest('finished_at')->paginate(15);

        return view('siswa.history', compact('attempts'));
    }
}
