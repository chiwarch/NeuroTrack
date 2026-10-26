<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Mengikuti neurotrack_phpmyadmin.sql: skills, skill_assessments, skill_assessment_scores. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $t) {
            $t->id();
            $t->string('name', 150);
            $t->string('category', 12); // academic | vocational | soft_skill
            $t->text('description')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->unique(['name', 'category']);
        });

        Schema::create('skill_assessments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('assessed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->string('source_type', 16)->default('teacher'); // teacher | quiz | assignment | self_assessment | system
            $t->date('period_start');
            $t->date('period_end');
            $t->timestamp('assessed_at')->useCurrent();
            $t->text('notes')->nullable();
            $t->timestamps();
            $t->index(['student_id', 'source_type', 'period_end']);
        });

        Schema::create('skill_assessment_scores', function (Blueprint $t) {
            $t->id();
            $t->foreignId('assessment_id')->constrained('skill_assessments')->cascadeOnDelete();
            $t->foreignId('skill_id')->constrained('skills')->restrictOnDelete();
            $t->decimal('score', 4, 2); // soft skill dinilai guru: skala 1–5
            $t->text('evidence')->nullable();
            $t->timestamps();
            $t->unique(['assessment_id', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_assessment_scores');
        Schema::dropIfExists('skill_assessments');
        Schema::dropIfExists('skills');
    }
};
