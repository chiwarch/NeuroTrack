<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Mengikuti neurotrack_phpmyadmin.sql: classes + class_memberships. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $t) {
            $t->id();
            $t->string('name', 100);
            $t->string('academic_year', 20);
            $t->foreignId('homeroom_teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });

        Schema::create('class_memberships', function (Blueprint $t) {
            $t->id();
            $t->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $t->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $t->timestamp('joined_at')->useCurrent();
            $t->timestamp('left_at')->nullable();
            $t->timestamps();
            $t->index(['student_id', 'joined_at', 'left_at'], 'idx_memberships_student_dates');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_memberships');
        Schema::dropIfExists('classes');
    }
};
