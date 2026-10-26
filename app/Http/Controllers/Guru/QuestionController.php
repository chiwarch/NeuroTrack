<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{
    private function check(Quiz $quiz, ?Question $question = null): void
    {
        abort_unless($quiz->teacher_id === auth()->id(), 403);
        abort_if($question && $question->quiz_id !== $quiz->id, 404);
    }

    public function create(Quiz $quiz)
    {
        $this->check($quiz);

        return view('guru.questions.form', ['quiz' => $quiz, 'question' => new Question(['type' => 'pilihan_ganda', 'points' => 1])]);
    }

    public function store(Request $request, Quiz $quiz)
    {
        $this->check($quiz);
        $this->save($request, $quiz, new Question);

        return redirect()->route('guru.quizzes.edit', $quiz)->with('success', 'Soal ditambahkan.');
    }

    public function edit(Quiz $quiz, Question $question)
    {
        $this->check($quiz, $question);

        return view('guru.questions.form', ['quiz' => $quiz, 'question' => $question->load('options')]);
    }

    public function update(Request $request, Quiz $quiz, Question $question)
    {
        $this->check($quiz, $question);
        $this->save($request, $quiz, $question);

        return redirect()->route('guru.quizzes.edit', $quiz)->with('success', 'Soal disimpan.');
    }

    public function destroy(Quiz $quiz, Question $question)
    {
        $this->check($quiz, $question);
        if ($quiz->attempts()->exists()) {
            return back()->with('error', 'Soal tidak bisa dihapus karena kuis sudah dikerjakan siswa.');
        }
        $question->delete();

        return back()->with('success', 'Soal dihapus.');
    }

    private function save(Request $request, Quiz $quiz, Question $question): void
    {
        $data = $request->validate([
            'type' => ['required', 'in:pilihan_ganda,isian'],
            'body' => ['required', 'string', 'max:2000'],
            'points' => ['required', 'integer', 'between:1,100'],
        ]);

        $options = collect();
        $accepted = null;

        if ($data['type'] === 'pilihan_ganda') {
            $options = collect($request->input('options', []))->map(fn ($o) => trim((string) $o))->filter(fn ($o) => $o !== '');
            if ($options->count() < 2) {
                throw ValidationException::withMessages(['options' => 'Isi minimal 2 pilihan jawaban.']);
            }
            if (! $options->has($request->input('correct'))) {
                throw ValidationException::withMessages(['correct' => 'Tandai satu jawaban yang benar.']);
            }
        } else {
            $accepted = collect(preg_split('/\R/', (string) $request->input('accepted')))
                ->map(fn ($a) => trim($a))->filter()->unique()->values();
            if ($accepted->isEmpty()) {
                throw ValidationException::withMessages(['accepted' => 'Isi minimal satu jawaban yang diterima.']);
            }
            $accepted = $accepted->all();
        }

        DB::transaction(function () use ($data, $options, $accepted, $request, $quiz, $question) {
            $question->fill($data + ['accepted_answers' => $accepted]);
            if (! $question->exists) {
                $question->quiz_id = $quiz->id;
                $question->position = (int) $quiz->questions()->max('position') + 1;
            }
            $question->save();

            $question->options()->delete();
            foreach ($options as $i => $body) {
                $question->options()->create(['body' => $body, 'is_correct' => (string) $i === (string) $request->input('correct')]);
            }
        });
    }
}
