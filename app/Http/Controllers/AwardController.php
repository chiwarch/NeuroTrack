<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\ClassSubject;
use App\Models\User;
use App\Models\UserBadge;
use App\Services\Gamification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Pemberian lencana penghargaan (lomba, juara kelas, dll). Admin: semua siswa. Guru: siswa di kelas yang mapelnya ia ampu. */
class AwardController extends Controller
{
    private function isAdmin(): bool
    {
        return auth()->user()->role === 'admin';
    }

    private function students()
    {
        $query = User::where('role', 'siswa')->with('classes:id,name')->orderBy('name');

        if ($this->isAdmin()) {
            return $query;
        }
        $classIds = ClassSubject::where('teacher_id', auth()->id())->pluck('class_id')->unique()->all();

        return $query->whereHas('classes', fn ($q) => $q->whereIn('classes.id', $classIds));
    }

    public function index()
    {
        return view('awards.index', [
            'prefix' => $this->isAdmin() ? 'admin' : 'guru',
            'students' => $this->students()->get(['id', 'name', 'nis']),
            'badges' => Badge::where('type', 'manual')->orderBy('name')->get(),
            'recent' => UserBadge::with('user:id,name', 'badge', 'awardedBy:id,name')->whereNotNull('awarded_by')
                ->when(! $this->isAdmin(), fn ($q) => $q->where('awarded_by', auth()->id()))
                ->latest('earned_at')->take(20)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', Rule::in($this->students()->pluck('users.id')->all())],
            'badge_id' => ['required', Rule::exists('badges', 'id')->where('type', 'manual')],
            'note' => ['nullable', 'string', 'max:300'],
        ], ['student_id.in' => 'Pilih siswa dari daftar.']);

        $badge = Badge::findOrFail($data['badge_id']);
        $student = User::findOrFail($data['student_id']);

        if (! Gamification::grant($student->id, $badge, auth()->id(), $data['note'] ?? null)) {
            return back()->with('error', $student->name.' sudah memiliki lencana '.$badge->name.'.');
        }
        Gamification::refresh($student->id);

        return back()->with('success', $student->name.' mendapat lencana '.$badge->name.($badge->xp_reward ? ' (+'.$badge->xp_reward.' XP)' : '').'.');
    }

    public function destroy(UserBadge $userBadge)
    {
        abort_unless($this->isAdmin() || (int) $userBadge->awarded_by === (int) auth()->id(), 403);
        Gamification::revoke($userBadge);

        return back()->with('success', 'Penghargaan dicabut.');
    }
}
