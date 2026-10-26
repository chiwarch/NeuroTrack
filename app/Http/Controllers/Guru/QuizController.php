<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class QuizController extends Controller
{
    private function own(Quiz $quiz): Quiz
    {
        abort_unless($quiz->teacher_id === auth()->id(), 403);

        return $quiz;
    }

    private function rules(): array
    {
        return [
            'subject_id' => ['required', Rule::exists('subject_teacher', 'subject_id')->where('user_id', auth()->id())],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'duration_minutes' => ['required', 'integer', 'between:1,180'],
            'passing_score' => ['required', 'integer', 'between:0,100'],
        ];
    }

    public function index()
    {
        $quizzes = auth()->user()->quizzes()->with('subject')->withCount(['questions', 'attempts'])->latest()->paginate(12);

        return view('guru.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('guru.quizzes.form', ['quiz' => new Quiz(['duration_minutes' => 15, 'passing_score' => 70, 'show_answers' => true]), 'subjects' => auth()->user()->subjects()->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $quiz = new Quiz($data);
        $quiz->teacher_id = auth()->id();
        $quiz->show_answers = $request->boolean('show_answers');
        $quiz->is_published = false;
        $quiz->save();

        return redirect()->route('guru.quizzes.edit', $quiz)->with('success', 'Kuis dibuat. Sekarang tambahkan soal.');
    }

    public function edit(Quiz $quiz)
    {
        $this->own($quiz)->load('questions.options');

        return view('guru.quizzes.form', ['quiz' => $quiz, 'subjects' => auth()->user()->subjects()->orderBy('name')->get()]);
    }

    public function update(Request $request, Quiz $quiz)
    {
        $this->own($quiz);
        $data = $request->validate($this->rules());
        $publish = $request->boolean('is_published');

        if ($publish && $quiz->questions()->doesntExist()) {
            throw ValidationException::withMessages(['is_published' => 'Tambahkan minimal satu soal sebelum kuis diterbitkan.']);
        }

        $quiz->fill($data);
        $quiz->show_answers = $request->boolean('show_answers');
        $quiz->is_published = $publish;
        $quiz->save();

        return back()->with('success', 'Kuis disimpan.');
    }

    public function destroy(Quiz $quiz)
    {
        $this->own($quiz);
        if ($quiz->attempts()->exists()) {
            return back()->with('error', 'Kuis yang sudah dikerjakan siswa tidak bisa dihapus. Cabut centang "Terbitkan" untuk menyembunyikannya.');
        }
        $quiz->delete();

        return redirect()->route('guru.quizzes.index')->with('success', 'Kuis dihapus.');
    }

    /** Hasil per siswa, urut nama (tanpa peringkat). */
    public function results(Quiz $quiz)
    {
        $this->own($quiz)->load('subject');

        $rows = Attempt::with('user:id,name,nis')->where('quiz_id', $quiz->id)->where('status', 'selesai')->get()
            ->groupBy('user_id')
            ->map(fn ($g) => [
                'user' => $g->first()->user,
                'tries' => $g->count(),
                'best' => $g->max('score'),
                'last' => $g->max('finished_at'),
            ])->sortBy(fn ($r) => mb_strtolower($r['user']->name))->values();

        return view('guru.quizzes.results', compact('quiz', 'rows'));
    }
}
