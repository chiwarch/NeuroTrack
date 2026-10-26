<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'type' => ['required', 'in:umum,kejuruan'],
            'major_id' => ['nullable', 'required_if:type,kejuruan', 'exists:majors,id'],
            'icon' => ['required', Rule::in(array_keys(Subject::ICONS))],
            'teachers' => ['nullable', 'array'],
            'teachers.*' => [Rule::exists('users', 'id')->where('role', 'guru')],
        ];
    }

    private function form(Subject $subject)
    {
        return view('admin.subjects.form', [
            'subject' => $subject->loadMissing('teachers'),
            'majors' => Major::orderBy('name')->get(),
            'teachers' => User::where('role', 'guru')->orderBy('name')->get(),
        ]);
    }

    private function persist(Request $request, Subject $subject): void
    {
        $data = $request->validate($this->rules());
        if ($data['type'] === 'umum') {
            $data['major_id'] = null;
        }
        $subject->fill(collect($data)->only(['name', 'type', 'major_id', 'icon'])->all())->save();
        $subject->teachers()->sync($data['teachers'] ?? []);
    }

    public function index()
    {
        return view('admin.subjects.index', [
            'subjects' => Subject::with('major')->withCount(['teachers', 'quizzes'])->orderBy('type')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return $this->form(new Subject(['type' => 'umum', 'icon' => 'books']));
    }

    public function store(Request $request)
    {
        $this->persist($request, new Subject);

        return redirect()->route('admin.subjects.index')->with('success', 'Mapel ditambahkan.');
    }

    public function edit(Subject $subject)
    {
        return $this->form($subject);
    }

    public function update(Request $request, Subject $subject)
    {
        $this->persist($request, $subject);

        return redirect()->route('admin.subjects.index')->with('success', 'Mapel disimpan.');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->quizzes()->exists()) {
            return back()->with('error', 'Mapel ini sudah punya kuis dan tidak bisa dihapus.');
        }
        $subject->delete();

        return back()->with('success', 'Mapel dihapus.');
    }
}
