<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'academic_year' => ['required', 'string', 'max:20'],
            'homeroom_teacher_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'guru')],
            'students' => ['nullable', 'array'],
            'students.*' => [Rule::exists('users', 'id')->where('role', 'siswa')],
            'teachers' => ['nullable', 'array'],
            'teachers.*' => ['nullable', Rule::exists('users', 'id')->where('role', 'guru')],
        ];
    }

    private function form(SchoolClass $class)
    {
        return view('admin.classes.form', [
            'class' => $class,
            'teachers' => User::where('role', 'guru')->orderBy('name')->get(),
            'students' => User::where('role', 'siswa')->with('major:id,code')->orderBy('name')->get(),
            'selected' => $class->exists ? $class->students()->pluck('users.id')->all() : [],
            'subjects' => Subject::with('major:id,code')->orderByDesc('type')->orderBy('name')->get(),
            'assigned' => $class->exists ? $class->classSubjects()->pluck('teacher_id', 'subject_id') : collect(),
        ]);
    }

    private function done(string $message, array $skipped)
    {
        $redirect = redirect()->route('admin.classes.index')->with('success', $message);

        return $skipped
            ? $redirect->with('error', 'Tidak dilepas karena sudah punya tugas: '.implode(', ', $skipped).'.')
            : $redirect;
    }

    public function index()
    {
        return view('admin.classes.index', [
            'classes' => SchoolClass::with('homeroomTeacher:id,name')->withCount('students')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return $this->form(new SchoolClass);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $class = SchoolClass::create(collect($data)->only(['name', 'academic_year', 'homeroom_teacher_id'])->all());
        $class->syncStudents($data['students'] ?? []);
        $skipped = $class->syncSubjects($data['teachers'] ?? []);

        return $this->done('Kelas ditambahkan.', $skipped);
    }

    public function edit(SchoolClass $class)
    {
        return $this->form($class);
    }

    public function update(Request $request, SchoolClass $class)
    {
        $data = $request->validate($this->rules());
        $class->update(collect($data)->only(['name', 'academic_year', 'homeroom_teacher_id'])->all());
        $class->syncStudents($data['students'] ?? []);
        $skipped = $class->syncSubjects($data['teachers'] ?? []);

        return $this->done('Kelas disimpan.', $skipped);
    }

    public function destroy(SchoolClass $class)
    {
        if ($class->sessions()->exists() || $class->classSubjects()->whereHas('assignments')->exists()) {
            return back()->with('error', 'Kelas sudah punya riwayat absensi atau tugas sehingga tidak bisa dihapus.');
        }
        $class->delete();

        return back()->with('success', 'Kelas dihapus.');
    }
}
