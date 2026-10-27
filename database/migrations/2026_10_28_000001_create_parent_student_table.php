<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Mengikuti neurotrack_phpmyadmin.sql: parent_student (+ kolom relationship: ayah/ibu/wali). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parent_student', function (Blueprint $t) {
            $t->id();
            $t->foreignId('parent_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $t->string('relationship', 20)->nullable();
            $t->timestamps();
            $t->unique(['parent_id', 'student_id'], 'uq_parent_student');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_student');
    }
};
