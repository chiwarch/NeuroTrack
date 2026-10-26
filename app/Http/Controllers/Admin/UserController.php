<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use App\Models\Major;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private function rules(?User $user = null): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'nis' => ['nullable', 'required_if:role,siswa', 'string', 'max:20', Rule::unique('users', 'nis')->ignore($user?->id)],
            'major_id' => ['nullable', 'required_if:role,siswa', 'exists:majors,id'],
            'grade_level' => ['nullable', 'required_if:role,siswa', 'integer', 'between:10,12'],
        ];
    }

    private function form(User $user)
    {
        return view('admin.users.form', ['user' => $user, 'majors' => Major::orderBy('name')->get()]);
    }

    private function clean(array $data): array
    {
        if ($data['role'] !== 'siswa') {
            $data['nis'] = $data['major_id'] = $data['grade_level'] = null;
        }
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }

    public function index(Request $request)
    {
        $users = User::with('major')
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->string('role')))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%'.$request->string('q').'%')
                ->orWhere('email', 'like', '%'.$request->string('q').'%')
                ->orWhere('nis', 'like', '%'.$request->string('q').'%')))
            ->orderBy('role')->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return $this->form(new User(['role' => 'siswa']));
    }

    public function store(Request $request)
    {
        User::create($this->clean($request->validate($this->rules())));

        return redirect()->route('admin.users.index')->with('success', 'Pengguna ditambahkan.');
    }

    public function edit(User $user)
    {
        return $this->form($user);
    }

    public function update(Request $request, User $user)
    {
        $data = $this->clean($request->validate($this->rules($user)));

        if ($user->id === auth()->id() && $data['role'] !== 'admin') {
            return back()->with('error', 'Kamu tidak bisa mengubah role akunmu sendiri.')->withInput();
        }
        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna disimpan.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Kamu tidak bisa menghapus akunmu sendiri.');
        }
        if ($user->isGuru() && $user->quizzes()->exists()) {
            return back()->with('error', 'Guru ini masih punya kuis. Hapus atau pindahkan kuisnya dulu.');
        }
        if ($user->isGuru() && ClassSubject::where('teacher_id', $user->id)->exists()) {
            return back()->with('error', 'Guru ini masih mengampu mapel di kelas. Ganti pengampunya di menu Kelas dulu.');
        }
        $user->delete();

        return back()->with('success', 'Pengguna dihapus.');
    }
}
