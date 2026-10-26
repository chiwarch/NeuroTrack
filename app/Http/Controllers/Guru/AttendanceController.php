<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\SchoolClass;
use App\Services\AttendanceToken;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    private function authorizeSession(AttendanceSession $session): void
    {
        abort_unless((int) $session->schoolClass->homeroom_teacher_id === (int) auth()->id(), 403);
    }

    private function records(AttendanceSession $session)
    {
        return Attendance::where('class_id', $session->class_id)
            ->whereDate('attendance_date', $session->attendance_date)->get()->keyBy('student_id');
    }

    public function index()
    {
        $classes = auth()->user()->homeroomClasses()->withCount('students')->orderBy('name')->get();
        $sessions = AttendanceSession::whereIn('class_id', $classes->pluck('id'))
            ->whereDate('attendance_date', today())->get()->keyBy('class_id');

        return view('guru.attendance.index', compact('classes', 'sessions'));
    }

    /** Mulai (atau lanjutkan) sesi absensi hari ini untuk kelas yang diwalikan. */
    public function open(SchoolClass $class)
    {
        abort_unless((int) $class->homeroom_teacher_id === (int) auth()->id(), 403);

        $session = AttendanceSession::firstOrCreate(
            ['class_id' => $class->id, 'attendance_date' => today()->toDateString()],
            ['teacher_id' => auth()->id()],
        );

        return redirect()->route('guru.attendance.show', $session);
    }

    public function show(AttendanceSession $session)
    {
        $this->authorizeSession($session);
        $session->load('schoolClass');

        return view('guru.attendance.show', [
            'session' => $session,
            'students' => $session->schoolClass->students()->orderBy('name')->get(),
            'records' => $this->records($session),
        ]);
    }

    /** Dipanggil JS tiap beberapa detik: token QR terbaru + status kehadiran terkini. */
    public function live(AttendanceSession $session)
    {
        $this->authorizeSession($session);

        $token = $session->isOpen() ? AttendanceToken::make($session) : null;

        return response()->json([
            'open' => $session->isOpen(),
            'url' => $token ? route('siswa.attendance.scan', ['t' => $token]) : null,
            'expires_in' => AttendanceToken::secondsLeft(),
            'records' => $this->records($session)->map(fn ($a) => [
                'id' => $a->student_id,
                'status' => $a->status,
                'label' => $a->label(),
                'tone' => $a->tone(),
                'time' => $a->check_in_at?->format('H:i'),
            ])->values(),
        ]);
    }

    /** Ubah status manual (sakit / izin / dispensasi / koreksi). Baris kosong dilewati. */
    public function update(Request $request, AttendanceSession $session)
    {
        $this->authorizeSession($session);

        $data = $request->validate([
            'status' => ['nullable', 'array'],
            'status.*' => ['nullable', 'in:'.implode(',', array_keys(Attendance::LABELS))],
        ]);

        $studentIds = $session->schoolClass->students()->pluck('users.id')->all();
        $records = $this->records($session);

        foreach ($data['status'] ?? [] as $studentId => $status) {
            if (! $status || ! in_array((int) $studentId, $studentIds, true)) {
                continue;
            }
            if (($records[(int) $studentId]->status ?? null) === $status) {
                continue; // tidak berubah
            }
            Attendance::updateOrCreate(
                ['student_id' => $studentId, 'class_id' => $session->class_id, 'attendance_date' => $session->attendance_date->toDateString()],
                ['status' => $status, 'recorded_by' => auth()->id(), 'method' => 'manual', 'attendance_session_id' => $session->id],
            );
        }

        return back()->with('success', 'Absensi disimpan.');
    }

    /** Tutup sesi: siswa yang belum tercatat otomatis Alfa. */
    public function close(AttendanceSession $session)
    {
        $this->authorizeSession($session);

        $recorded = $this->records($session)->keys();
        foreach ($session->schoolClass->students()->whereNotIn('users.id', $recorded)->get() as $student) {
            Attendance::create([
                'student_id' => $student->id, 'class_id' => $session->class_id,
                'recorded_by' => auth()->id(), 'attendance_session_id' => $session->id,
                'attendance_date' => $session->attendance_date->toDateString(),
                'status' => 'absent', 'method' => 'system',
            ]);
        }
        $session->update(['closed_at' => now()]);

        return back()->with('success', 'Absensi ditutup. Siswa yang belum tercatat ditandai Alfa.');
    }

    public function reopen(AttendanceSession $session)
    {
        $this->authorizeSession($session);
        abort_unless($session->attendance_date->isToday(), 403);
        $session->update(['closed_at' => null]);

        return back()->with('success', 'Absensi dibuka kembali.');
    }
}
