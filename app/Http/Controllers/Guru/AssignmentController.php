<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\ClassSubject;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AssignmentController extends Controller
{
    private function own(Assignment $assignment): void
    {
        abort_unless((int) $assignment->classSubject->teacher_id === (int) auth()->id(), 403);
    }

    private function myClassSubjects()
    {
        return ClassSubject::with('schoolClass:id,name', 'subject:id,name')->where('teacher_id', auth()->id())->get()
            ->sortBy(fn ($c) => $c->schoolClass->name.' '.$c->subject->name)->values();
    }

    private function fields(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'deadline' => ['nullable', 'date'],
            'max_score' => ['required', 'numeric', 'between:1,100'],
        ];
    }

    public function index()
    {
        $assignments = Assignment::with('classSubject.schoolClass:id,name', 'classSubject.subject:id,name')
            ->withCount('submissions')
            ->whereHas('classSubject', fn ($q) => $q->where('teacher_id', auth()->id()))
            ->latest()->paginate(15);

        return view('guru.assignments.index', compact('assignments'));
    }

    public function create()
    {
        return view('guru.assignments.form', ['assignment' => new Assignment, 'classSubjects' => $this->myClassSubjects()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->fields() + [
            'class_subject_id' => ['required', Rule::exists('class_subjects', 'id')->where('teacher_id', auth()->id())],
        ]);

        $assignment = new Assignment($data);
        $assignment->created_by = auth()->id();
        $assignment->save();

        return redirect()->route('guru.assignments.show', $assignment)->with('success', 'Tugas dibuat.');
    }

    public function edit(Assignment $assignment)
    {
        $this->own($assignment);

        return view('guru.assignments.form', ['assignment' => $assignment->load('classSubject.schoolClass', 'classSubject.subject'), 'classSubjects' => collect()]);
    }

    public function update(Request $request, Assignment $assignment)
    {
        $this->own($assignment);
        $data = $request->validate($this->fields());

        // Nilai maksimal tidak boleh lebih kecil dari nilai yang sudah diberikan.
        if ($assignment->submissions()->where('score', '>', $data['max_score'])->exists()) {
            return back()->withInput()->withErrors(['max_score' => 'Sudah ada nilai yang lebih besar dari angka ini.']);
        }
        $assignment->update($data);

        return redirect()->route('guru.assignments.show', $assignment)->with('success', 'Tugas disimpan.');
    }

    public function destroy(Assignment $assignment)
    {
        $this->own($assignment);
        if ($assignment->submissions()->exists()) {
            return back()->with('error', 'Tugas sudah punya pengumpulan sehingga tidak bisa dihapus.');
        }
        $assignment->delete();

        return redirect()->route('guru.assignments.index')->with('success', 'Tugas dihapus.');
    }

    /** Halaman pengumpulan + penilaian. */
    public function show(Assignment $assignment)
    {
        $this->own($assignment);
        $assignment->load('classSubject.schoolClass', 'classSubject.subject');

        return view('guru.assignments.show', [
            'assignment' => $assignment,
            'students' => $assignment->classSubject->schoolClass->students()->orderBy('name')->get(),
            'submissions' => $assignment->submissions()->get()->keyBy('student_id'),
        ]);
    }

    public function grade(Request $request, Assignment $assignment)
    {
        $this->own($assignment);

        $data = $request->validate([
            'scores' => ['nullable', 'array'],
            'scores.*' => ['nullable', 'numeric', 'min:0', 'max:'.$assignment->max_score],
            'feedback' => ['nullable', 'array'],
            'feedback.*' => ['nullable', 'string', 'max:1000'],
        ]);

        foreach ($assignment->submissions as $submission) {
            $score = $data['scores'][$submission->id] ?? null;
            $submission->update([
                'score' => $score,
                'feedback' => $data['feedback'][$submission->id] ?? null,
                'graded_by' => $score !== null ? auth()->id() : null,
            ]);
        }

        return back()->with('success', 'Nilai disimpan.');
    }

    public function file(Submission $submission)
    {
        $this->own($submission->assignment);
        abort_unless($submission->file_path && Storage::disk('local')->exists($submission->file_path), 404);

        return Storage::disk('local')->download($submission->file_path, $submission->file_name ?? basename($submission->file_path));
    }
}
