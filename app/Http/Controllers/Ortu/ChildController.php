<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Submission;
use App\Models\User;
use App\Services\AttendanceRate;
use App\Services\SkillStats;

/** Perkembangan satu anak: hanya baca (absensi, radar kemampuan, tugas). */
class ChildController extends Controller
{
    public function show(User $student)
    {
        abort_unless(auth()->user()->children()->where('users.id', $student->id)->exists(), 403);

        $class = $student->classes()->first();
        $assignments = $class
            ? Assignment::with('classSubject.subject:id,name')
                ->whereHas('classSubject', fn ($q) => $q->where('class_id', $class->id))
                ->orderByRaw('deadline is null')->orderBy('deadline')->get()
            : collect();

        return view('ortu.child', [
            'student' => $student->load('major:id,code'),
            'class' => $class,
            'all' => AttendanceRate::summary($student),
            'month' => AttendanceRate::summary($student, now()->startOfMonth(), now()->endOfMonth()),
            'history' => Attendance::where('student_id', $student->id)->latest('attendance_date')->take(10)->get(),
            'hard' => SkillStats::hardSkills($student),
            'soft' => SkillStats::softSkills($student),
            'assignments' => $assignments,
            'submissions' => Submission::where('student_id', $student->id)
                ->whereIn('assignment_id', $assignments->pluck('id'))->get()->keyBy('assignment_id'),
        ]);
    }
}
