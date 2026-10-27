<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Tambahan di luar SQL acuan: sasaran pengumuman per jurusan (target_type = 'major'). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcement_majors', function (Blueprint $t) {
            $t->id();
            $t->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
            $t->foreignId('major_id')->constrained('majors')->cascadeOnDelete();
            $t->unique(['announcement_id', 'major_id'], 'uq_announcement_major');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_majors');
    }
};
