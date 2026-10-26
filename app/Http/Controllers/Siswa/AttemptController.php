<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Services\QuizGrader;
use Illuminate\Http\Request;

class AttemptController extends Controller
{
    private function own(Attempt $attempt): Attempt
    {
        abort_unless($attempt->user_id === auth()->id(), 403);

        return $attempt->load('quiz.subject');
    }

    public function show(Attempt $attempt)
    {
        $attempt = $this->own($attempt);

        if ($attempt->isFinished()) {
            return redirect()->route('siswa.attempts.result', $attempt);
        }
        if ($attempt->deadline()->isPast()) {
            QuizGrader::expire($attempt);

            return redirect()->route('siswa.attempts.result', $attempt)->with('error', 'Waktu pengerjaan sudah habis.');
        }

        // Hanya teks soal & pilihan yang dikirim ke view (tanpa kunci jawaban).
        $questions = $attempt->quiz->questions()->with('options:id,question_id,body')->get();

        return view('siswa.attempt', [
            'attempt' => $attempt,
            'questions' => $questions,
            'remaining' => max(0, (int) now()->diffInSeconds($attempt->deadline(), false)),
        ]);
    }

    public function submit(Request $request, Attempt $attempt)
    {
        $attempt = $this->own($attempt);

        if (! $attempt->isFinished()) {
            if (now()->greaterThan($attempt->deadline()->addSeconds(QuizGrader::GRACE_SECONDS))) {
                QuizGrader::expire($attempt);

                return redirect()->route('siswa.attempts.result', $attempt)->with('error', 'Waktu pengerjaan sudah habis, jawaban tidak dihitung.');
            }
            QuizGrader::grade($attempt, (array) $request->input('answers', []));
        }

        return redirect()->route('siswa.attempts.result', $attempt);
    }

    public function result(Attempt $attempt)
    {
        $attempt = $this->own($attempt);
        if (! $attempt->isFinished()) {
            return redirect()->route('siswa.attempts.show', $attempt);
        }

        return view('siswa.result', [
            'attempt' => $attempt,
            'questions' => $attempt->quiz->questions()->with('options')->get(),
            'answers' => $attempt->answers()->get()->keyBy('question_id'),
        ]);
    }
}
