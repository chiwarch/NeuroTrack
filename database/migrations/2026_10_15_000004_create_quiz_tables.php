<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $t->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $t->string('title');
            $t->text('description')->nullable();
            $t->unsignedSmallInteger('duration_minutes')->default(15);
            $t->unsignedTinyInteger('passing_score')->default(70);
            $t->boolean('show_answers')->default(true);
            $t->boolean('is_published')->default(false);
            $t->timestamps();
        });

        Schema::create('questions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $t->string('type', 20); // pilihan_ganda | isian
            $t->text('body');
            $t->unsignedSmallInteger('points')->default(1);
            $t->unsignedSmallInteger('position')->default(0);
            $t->json('accepted_answers')->nullable(); // untuk isian singkat
            $t->timestamps();
        });

        Schema::create('options', function (Blueprint $t) {
            $t->id();
            $t->foreignId('question_id')->constrained()->cascadeOnDelete();
            $t->string('body');
            $t->boolean('is_correct')->default(false);
        });

        Schema::create('attempts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->timestamp('started_at');
            $t->timestamp('finished_at')->nullable();
            $t->decimal('score', 5, 1)->nullable();
            $t->string('status', 14)->default('berjalan'); // berjalan | selesai | kedaluwarsa
            $t->timestamps();
            $t->index(['user_id', 'quiz_id']);
        });

        Schema::create('attempt_answers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('attempt_id')->constrained()->cascadeOnDelete();
            $t->foreignId('question_id')->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('option_id')->nullable();
            $t->string('text_answer')->nullable();
            $t->boolean('is_correct')->default(false);
            $t->unsignedSmallInteger('points_awarded')->default(0);
            $t->unique(['attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempt_answers');
        Schema::dropIfExists('attempts');
        Schema::dropIfExists('options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('quizzes');
    }
};
