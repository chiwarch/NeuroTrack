<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * attendances mengikuti neurotrack_phpmyadmin.sql (+ kolom tambahan: attendance_session_id, check_in_at, method,
 * dan status 'dispensation'). attendance_sessions = sesi absensi harian yang membuka QR.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_sessions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $t->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $t->date('attendance_date');
            $t->time('late_after')->default('07:15:00'); // lewat jam ini = terlambat
            $t->timestamp('opened_at')->useCurrent();
            $t->timestamp('closed_at')->nullable();
            $t->timestamps();
            $t->unique(['class_id', 'attendance_date']);
        });

        Schema::create('attendances', function (Blueprint $t) {
            $t->id();
            $t->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $t->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $t->foreignId('attendance_session_id')->nullable()->constrained('attendance_sessions')->nullOnDelete();
            $t->date('attendance_date');
            $t->string('status', 14); // present | late | sick | permission | absent | dispensation
            $t->timestamp('check_in_at')->nullable();
            $t->string('method', 10)->default('manual'); // qr | manual | system
            $t->text('notes')->nullable();
            $t->timestamps();
            $t->unique(['student_id', 'class_id', 'attendance_date'], 'uq_attendance_student_class_date');
            $t->index('attendance_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('attendance_sessions');
    }
};
