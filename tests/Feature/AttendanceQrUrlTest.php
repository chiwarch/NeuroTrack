<?php

namespace Tests\Feature;

use App\Models\AttendanceSession;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceQrUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_qr_uses_the_configured_application_url(): void
    {
        config(['app.url' => 'http://192.168.1.20:8000']);

        $teacher = User::create([
            'name' => 'Guru Test',
            'email' => 'guru@example.test',
            'password' => 'password',
            'role' => 'guru',
        ]);
        $schoolClass = SchoolClass::create([
            'name' => 'X RPL 1',
            'academic_year' => '2026/2027',
            'homeroom_teacher_id' => $teacher->id,
        ]);
        $session = AttendanceSession::create([
            'class_id' => $schoolClass->id,
            'teacher_id' => $teacher->id,
            'attendance_date' => today()->toDateString(),
        ]);

        $response = $this->actingAs($teacher)->getJson(route('guru.attendance.live', $session));

        $response->assertOk();
        $this->assertStringStartsWith('http://192.168.1.20:8000/siswa/absen?t=', $response->json('url'));
    }
}
