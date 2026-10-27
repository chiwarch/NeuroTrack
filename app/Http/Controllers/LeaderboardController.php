<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\SchoolClass;
use App\Services\Gamification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Papan peringkat per kelas. Kelas yang bisa dilihat bergantung peran: siswa (kelasnya), orang tua (kelas anak), guru (kelas yang diajar/diwalikan), admin (semua). */
class LeaderboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = auth()->user();

        $classes = match ($user->role) {
            'siswa' => $user->classes()->get(),
            'orang_tua' => SchoolClass::whereIn('id', DB::table('class_memberships')->whereNull('left_at')
                ->whereIn('student_id', $user->children()->pluck('users.id'))->pluck('class_id'))->orderBy('name')->get(),
            'guru' => SchoolClass::whereIn('id', Announcement::teacherClassIds($user))->orderBy('name')->get(),
            default => SchoolClass::orderBy('name')->get(),
        };

        $class = $classes->firstWhere('id', (int) $request->query('kelas')) ?? $classes->first();

        return view('gamification.leaderboard', [
            'classes' => $classes,
            'class' => $class,
            'rows' => $class ? Gamification::leaderboard($class) : collect(),
            'meId' => $user->id,
        ]);
    }
}
