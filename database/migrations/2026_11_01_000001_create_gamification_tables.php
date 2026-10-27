<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mengikuti neurotrack_phpmyadmin.sql: levels, xp_transactions, badges, user_badges, users.current_level_id.
 * Kolom tambahan: badges.type/code/xp_reward/tone, xp_transactions.reference (anti-ganda), user_badges.awarded_by/note.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $t) {
            $t->id();
            $t->string('name', 100)->unique();
            $t->unsignedInteger('min_xp')->default(0)->unique();
        });

        Schema::create('badges', function (Blueprint $t) {
            $t->id();
            $t->string('name', 150)->unique();
            $t->text('description')->nullable();
            $t->string('icon', 255)->nullable();                // nama ikon (star, award, flame, ...)
            $t->unsignedInteger('required_xp')->default(0);     // dipakai bila type = xp
            $t->string('type', 10)->default('xp');              // xp | auto | manual
            $t->string('code', 30)->nullable()->unique();       // pengenal untuk lencana otomatis
            $t->unsignedInteger('xp_reward')->default(0);       // bonus XP saat lencana didapat
            $t->string('tone', 12)->default('brand');
            $t->timestamps();
        });

        Schema::create('xp_transactions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->integer('amount');
            $t->string('source', 100);
            $t->text('description')->nullable();
            $t->string('reference', 100)->nullable();           // mis. attendance:2026-11-02, quiz:5, badge:3
            $t->timestamp('created_at')->useCurrent();
            $t->unique(['user_id', 'reference'], 'uq_xp_user_reference');
        });

        Schema::create('user_badges', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('badge_id')->constrained('badges')->cascadeOnDelete();
            $t->timestamp('earned_at')->useCurrent();
            $t->foreignId('awarded_by')->nullable()->constrained('users')->nullOnDelete();
            $t->text('note')->nullable();
            $t->unique(['user_id', 'badge_id'], 'uq_user_badge');
        });

        Schema::table('users', function (Blueprint $t) {
            $t->foreignId('current_level_id')->nullable()->constrained('levels')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->dropConstrainedForeignId('current_level_id'));
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('xp_transactions');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('levels');
    }
};
