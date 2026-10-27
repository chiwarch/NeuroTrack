<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            'children' => ['nullable', 'array', 'required_if:role,orang_tua'],
            'children.*' => [Rule::exists('users', 'id')->where('role', 'siswa')],
            'relationship' => ['nullable', Rule::in(['Ayah', 'Ibu', 'Wali'])],
        ];
    }

    private function form(User $user)
    {
        return view('admin.users.form', [
            'user' => $user,
            'majors' => Major::orderBy('name')->get(),
            'students' => User::where('role', 'siswa')->with('classes:id,name')->orderBy('name')->get(['id', 'name', 'nis']),
            'linked' => $user->exists ? $user->children()->pluck('users.id')->all() : [],
            'relationship' => $user->exists ? $user->children()->first()?->pivot->relationship : null,
        ]);
    }

    private function clean(array $data): array
    {
        if ($data['role'] !== 'siswa') {
            $data['nis'] = $data['major_id'] = $data['grade_level'] = null;
        }
        if (empty($data['password'])) {
            unset($data['password']);
        }
        unset($data['children'], $data['relationship']); // bukan kolom tabel users

        return $data;
    }

    /** Hubungkan orang tua dengan anak-anaknya (kosongkan bila role bukan orang tua). */
    private function syncChildren(User $user, array $data): void
    {
        $ids = $user->isOrangTua() ? ($data['children'] ?? []) : [];
        $user->children()->sync(collect($ids)->mapWithKeys(fn ($id) => [$id => ['relationship' => $data['relationship'] ?? null]])->all());
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
        $data = $request->validate($this->rules());
        $user = User::create($this->clean($data));
        $this->syncChildren($user, $data);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna ditambahkan.');
    }

    public function edit(User $user)
    {
        return $this->form($user);
    }

    public function update(Request $request, User $user)
    {
        $input = $request->validate($this->rules($user));
        $data = $this->clean($input);

        if ($user->id === auth()->id() && $data['role'] !== 'admin') {
            return back()->with('error', 'Kamu tidak bisa mengubah role akunmu sendiri.')->withInput();
        }
        $user->update($data);
        $this->syncChildren($user->refresh(), $input);

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
        if ($user->isGuru() && \App\Models\ClassSubject::where('teacher_id', $user->id)->exists()) {
            return back()->with('error', 'Guru ini masih mengampu mapel di kelas. Ganti pengampunya di menu Kelas dulu.');
        }
        $user->delete();

        return back()->with('success', 'Pengguna dihapus.');
    }
}
