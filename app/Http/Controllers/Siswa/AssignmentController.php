<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    private function authorizeAssignment(Assignment $assignment): void
    {
        $class = auth()->user()->classes()->first();
        abort_unless($class && (int) $assignment->classSubject->class_id === (int) $class->id, 403);
    }

    private function mySubmission(Assignment $assignment): ?Submission
    {
        return Submission::where('assignment_id', $assignment->id)->where('student_id', auth()->id())->first();
    }

    public function index()
    {
        $class = auth()->user()->classes()->first();
        $assignments = $class
            ? Assignment::with('classSubject.subject:id,name')
                ->whereHas('classSubject', fn ($q) => $q->where('class_id', $class->id))
                ->orderByRaw('deadline is null')->orderBy('deadline')->get()
            : collect();
        $submissions = Submission::where('student_id', auth()->id())
            ->whereIn('assignment_id', $assignments->pluck('id'))->get()->keyBy('assignment_id');

        return view('siswa.assignments.index', compact('class', 'assignments', 'submissions'));
    }

    public function show(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        return view('siswa.assignments.show', [
            'assignment' => $assignment->load('classSubject.subject', 'classSubject.teacher:id,name'),
            'submission' => $this->mySubmission($assignment),
        ]);
    }

    /** Kumpulkan (atau kumpulkan ulang selama belum dinilai). Setelah tenggat tetap bisa, ditandai terlambat. */
    public function submit(Request $request, Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        $request->validate([
            'file' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,txt,jpg,jpeg,png'],
            'answer_text' => ['nullable', 'string', 'max:5000'],
        ], ['file.max' => 'Ukuran berkas maksimal 10 MB.', 'file.mimes' => 'Format berkas tidak didukung.']);

        $submission = $this->mySubmission($assignment);
        if ($submission && $submission->isGraded()) {
            return back()->with('error', 'Tugas sudah dinilai, tidak bisa dikumpulkan ulang.');
        }
        if (! $request->hasFile('file') && ! filled($request->input('answer_text')) && ! $submission?->file_path) {
            return back()->withInput()->withErrors(['file' => 'Unggah berkas atau tulis jawabanmu dulu.']);
        }

        $path = $submission?->file_path;
        $name = $submission?->file_name;
        if ($request->hasFile('file')) {
            if ($path) {
                Storage::disk('local')->delete($path);
            }
            $file = $request->file('file');
            $path = $file->store('submissions/'.$assignment->id, 'local');
            $name = $file->getClientOriginalName();
        }

        Submission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'student_id' => auth()->id()],
            ['file_path' => $path, 'file_name' => $name, 'answer_text' => $request->input('answer_text'), 'submitted_at' => now()],
        );

        return redirect()->route('siswa.assignments.show', $assignment)->with('success', 'Tugas dikumpulkan.');
    }

    public function file(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);
        $submission = $this->mySubmission($assignment);
        abort_unless($submission?->file_path && Storage::disk('local')->exists($submission->file_path), 404);

        return Storage::disk('local')->download($submission->file_path, $submission->file_name ?? basename($submission->file_path));
    }
}
