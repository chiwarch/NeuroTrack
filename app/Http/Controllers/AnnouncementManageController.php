<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Major;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Kelola pengumuman. Dipakai dua kali lewat route: admin (semua sasaran) dan guru
 * (hanya ke kelas yang ia ajar/walikan). Prefix route ('admin' | 'guru') diambil dari peran.
 */
class AnnouncementManageController extends Controller
{
    private function isAdmin(): bool
    {
        return auth()->user()->role === 'admin';
    }

    private function prefix(): string
    {
        return $this->isAdmin() ? 'admin' : 'guru';
    }

    private function classOptions()
    {
        $query = SchoolClass::orderBy('name');

        return $this->isAdmin() ? $query->get() : $query->whereIn('id', Announcement::teacherClassIds(auth()->user()))->get();
    }

    private function authorizeOwn(Announcement $announcement): void
    {
        abort_unless($this->isAdmin() || (int) $announcement->created_by === (int) auth()->id(), 403);
    }

    public function index()
    {
        $user = auth()->user();
        $mine = Announcement::with('creator:id,name', 'classes:id,name', 'majors:id,code')
            ->when(! $this->isAdmin(), fn ($q) => $q->where('created_by', $user->id))
            ->latest()->paginate(15);

        $forMe = $this->isAdmin() ? collect()
            : Announcement::visibleTo($user)->where('created_by', '!=', $user->id)
                ->with('creator:id,name', 'classes:id,name', 'majors:id,code')->latest('published_at')->take(10)->get();

        return view('announcements.index', ['prefix' => $this->prefix(), 'announcements' => $mine, 'forMe' => $forMe]);
    }

    public function create()
    {
        return view('announcements.form', ['prefix' => $this->prefix(), 'announcement' => new Announcement, 'classes' => $this->classOptions(), 'isAdmin' => $this->isAdmin(), 'chosen' => [], 'majors' => Major::orderBy('code')->get(), 'chosenMajors' => []]);
    }

    public function store(Request $request)
    {
        $announcement = new Announcement;
        $announcement->created_by = auth()->id();

        return $this->save($request, $announcement, 'Pengumuman dibuat.');
    }

    public function edit(Announcement $announcement)
    {
        $this->authorizeOwn($announcement);

        return view('announcements.form', [
            'prefix' => $this->prefix(), 'announcement' => $announcement, 'classes' => $this->classOptions(),
            'isAdmin' => $this->isAdmin(), 'chosen' => $announcement->classes()->pluck('classes.id')->all(),
            'majors' => Major::orderBy('code')->get(), 'chosenMajors' => $announcement->majors()->pluck('majors.id')->all(),
        ]);
    }

    public function update(Request $request, Announcement $announcement)
    {
        $this->authorizeOwn($announcement);

        return $this->save($request, $announcement, 'Pengumuman disimpan.');
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorizeOwn($announcement);
        $announcement->delete();

        return back()->with('success', 'Pengumuman dihapus.');
    }

    private function save(Request $request, Announcement $announcement, string $message)
    {
        $allowedTargets = $this->isAdmin() ? array_keys(Announcement::TARGETS) : ['class'];
        $allowedClassIds = $this->classOptions()->pluck('id')->all();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string', 'max:5000'],
            'target_type' => ['required', Rule::in($allowedTargets)],
            'classes' => ['nullable', 'array', 'required_if:target_type,class'],
            'classes.*' => [Rule::in($allowedClassIds)],
            'majors' => ['nullable', 'array', 'required_if:target_type,major'],
            'majors.*' => ['exists:majors,id'],
            'publish' => ['required', Rule::in(['now', 'draft', 'schedule'])],
            'publish_at' => ['nullable', 'required_if:publish,schedule', 'date', 'after:now'],
        ], ['classes.required_if' => 'Pilih minimal satu kelas.', 'majors.required_if' => 'Pilih minimal satu jurusan.', 'publish_at.required_if' => 'Isi waktu penerbitan.', 'publish_at.after' => 'Waktu penerbitan harus di masa depan.']);

        $publishedAt = match ($data['publish']) {
            'now' => ($announcement->exists && $announcement->status() === 'published') ? $announcement->published_at : now(),
            'schedule' => $data['publish_at'],
            default => null,
        };

        $announcement->fill([
            'title' => $data['title'], 'content' => $data['content'],
            'target_type' => $data['target_type'], 'published_at' => $publishedAt,
        ])->save();

        $announcement->classes()->sync($data['target_type'] === 'class' ? ($data['classes'] ?? []) : []);
        $announcement->majors()->sync($data['target_type'] === 'major' ? ($data['majors'] ?? []) : []);

        return redirect()->route($this->prefix().'.announcements.index')->with('success', $message);
    }
}
