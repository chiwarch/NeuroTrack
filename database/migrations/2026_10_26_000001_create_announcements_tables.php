<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Mengikuti neurotrack_phpmyadmin.sql: announcements + announcement_classes. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $t->string('title', 200);
            $t->text('content');
            $t->string('target_type', 10)->default('all'); // all | class | students | teachers | parents
            $t->timestamp('published_at')->nullable();     // null = draf, masa depan = terjadwal
            $t->timestamps();
            $t->index(['target_type', 'published_at']);
        });

        Schema::create('announcement_classes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
            $t->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $t->unique(['announcement_id', 'class_id'], 'uq_announcement_class');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_classes');
        Schema::dropIfExists('announcements');
    }
};
