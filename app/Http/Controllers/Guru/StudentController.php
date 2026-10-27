<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use App\Models\Skill;
use App\Models\SkillAssessment;
use App\Models\User;
use App\Services\SkillStats;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /** Siswa aktif di kelas-kelas yang mapelnya diampu guru ini (class_subjects). */
    private function students()
    {
        $classIds = ClassSubject::where('teacher_id', auth()->id())->pluck('class_id')->unique()->all();

        return User::where('role', 'siswa')
            ->whereHas('classes', fn ($q) => $q->whereIn('classes.id', $classIds));
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $students = $this->students()->with('major:id,code', 'classes:id,name')
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('nis', 'like', "%{$q}%")))
            ->orderBy('name')->paginate(25)->withQueryString();

        return view('guru.students.index', compact('students', 'q'));
    }

    public function show(User $student)
    {
        abort_unless($this->students()->whereKey($student->id)->exists(), 403);

        $latest = SkillAssessment::with('scores')->where('student_id', $student->id)
            ->where('assessed_by', auth()->id())->where('source_type', 'teacher')
            ->orderByDesc('period_end')->orderByDesc('id')->first();

        return view('guru.students.show', [
            'student' => $student->load('major:id,code', 'classes:id,name'),
            'hard' => SkillStats::hardSkills($student),
            'soft' => SkillStats::softSkills($student),
            'skills' => Skill::softSkills(),
            'mine' => $latest ? $latest->scores->pluck('score', 'skill_id') : collect(),
            'notes' => $latest?->notes,
        ]);
    }

    /** Satu penilaian per guru per siswa per bulan; menyimpan ulang di bulan yang sama = memperbarui. */
    public function rate(Request $request, User $student)
    {
        abort_unless($this->students()->whereKey($student->id)->exists(), 403);

        $skills = Skill::softSkills();
        $rules = ['scores' => ['required', 'array'], 'notes' => ['nullable', 'string', 'max:1000']];
        foreach ($skills as $skill) {
            $rules["scores.{$skill->id}"] = ['required', 'integer', 'between:1,5'];
        }
        $data = $request->validate($rules, ['scores.*.required' => 'Pilih nilai 1–5 untuk setiap aspek.']);

        $assessment = SkillAssessment::updateOrCreate(
            [
                'student_id' => $student->id, 'assessed_by' => auth()->id(), 'source_type' => 'teacher',
                'period_start' => now()->startOfMonth()->toDateString(), 'period_end' => now()->endOfMonth()->toDateString(),
            ],
            ['assessed_at' => now(), 'notes' => $data['notes'] ?? null],
        );

        foreach ($skills as $skill) {
            $assessment->scores()->updateOrCreate(['skill_id' => $skill->id], ['score' => $data['scores'][$skill->id]]);
        }

        return back()->with('success', 'Penilaian soft skill disimpan.');
    }
}
