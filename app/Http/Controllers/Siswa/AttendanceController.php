<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Services\AttendanceRate;
use App\Services\AttendanceToken;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('siswa.attendance.index', [
            'class' => $user->classes()->first(),
            'all' => AttendanceRate::summary($user),
            'month' => AttendanceRate::summary($user, now()->startOfMonth(), now()->endOfMonth()),
            'history' => Attendance::where('student_id', $user->id)->latest('attendance_date')->paginate(20),
        ]);
    }

    /** Halaman tujuan QR: memeriksa token, lalu meminta siswa menekan tombol konfirmasi. */
    public function scan(Request $request)
    {
        $user = auth()->user();
        $session = null;
        $state = 'invalid';

        $sessionId = AttendanceToken::verify($request->query('t'));
        if ($sessionId && ($session = AttendanceSession::with('schoolClass')->find($sessionId))) {
            $state = $this->stateFor($session, $user);
        }

        if ($state === 'ready') {
            // Bukti pindai milik siswa ini saja (berlaku 90 detik) agar tombol konfirmasi tidak ikut kedaluwarsa.
            session(['att_proof' => ['sid' => $session->id, 'uid' => $user->id, 'until' => time() + 90]]);
        }

        return view('siswa.attendance.scan', compact('state', 'session'));
    }

    public function checkIn(Request $request)
    {
        $user = auth()->user();
        $proof = session('att_proof');

        if (! $proof || (int) $proof['uid'] !== (int) $user->id || $proof['until'] < time()) {
            return redirect()->route('siswa.attendance.index')->with('error', 'Pindai ulang QR dari layar guru ya.');
        }

        $session = AttendanceSession::with('schoolClass')->findOrFail($proof['sid']);
        $state = $this->stateFor($session, $user);
        if ($state !== 'ready') {
            return view('siswa.attendance.scan', compact('state', 'session'));
        }

        $status = now()->greaterThan($session->lateCutoff()) ? 'late' : 'present';
        Attendance::updateOrCreate(
            ['student_id' => $user->id, 'class_id' => $session->class_id, 'attendance_date' => $session->attendance_date->toDateString()],
            ['status' => $status, 'recorded_by' => $session->teacher_id, 'attendance_session_id' => $session->id,
             'check_in_at' => now(), 'method' => 'qr'],
        );
        session()->forget('att_proof');

        return redirect()->route('siswa.attendance.index')
            ->with('success', $status === 'late' ? 'Absen tercatat: Terlambat.' : 'Absen tercatat: Hadir. Semangat belajarnya!');
    }

    /** ready | closed | not_member | done */
    private function stateFor(AttendanceSession $session, $user): string
    {
        if (! $session->isOpen()) {
            return 'closed';
        }
        if (! $session->schoolClass->students()->where('users.id', $user->id)->exists()) {
            return 'not_member';
        }
        $existing = Attendance::where('student_id', $user->id)->where('class_id', $session->class_id)
            ->whereDate('attendance_date', $session->attendance_date)->first();

        // Alfa otomatis dari sistem boleh diganti lewat QR; catatan manual guru tidak.
        return ($existing && $existing->method !== 'system') ? 'done' : 'ready';
    }
}
