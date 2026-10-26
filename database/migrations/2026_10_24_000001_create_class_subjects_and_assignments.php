<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mengikuti neurotrack_phpmyadmin.sql: class_subjects, assignments, submissions.
 * Tambahan: submissions.file_name & answer_text. Hapus assignments dibuat RESTRICT (bukan cascade)
 * agar melepas mapel dari kelas tidak diam-diam menghapus tugas dan nilai.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_subjects', function (Blueprint $t) {
            $t->id();
            $t->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $t->foreignId('subject_id')->constrained('subjects')->restrictOnDelete();
            $t->foreignId('teacher_id')->constrained('users')->restrictOnDelete();
            $t->timestamps();
            $t->unique(['class_id', 'subject_id', 'teacher_id'], 'uq_class_subject_teacher');
        });

        Schema::create('assignments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('class_subject_id')->constrained('class_subjects')->restrictOnDelete();
            $t->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $t->string('title', 200);
            $t->text('description')->nullable();
            $t->dateTime('deadline')->nullable();
            $t->decimal('max_score', 5, 2)->default(100);
            $t->timestamps();
        });

        Schema::create('submissions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $t->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $t->string('file_path', 500)->nullable();
            $t->string('file_name', 255)->nullable();
            $t->text('answer_text')->nullable();
            $t->timestamp('submitted_at')->nullable();
            $t->decimal('score', 5, 2)->nullable();
            $t->text('feedback')->nullable();
            $t->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->unique(['assignment_id', 'student_id'], 'uq_submission_assignment_student');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('class_subjects');
    }
};
