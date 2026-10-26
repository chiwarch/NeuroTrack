<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('role', 12)->default('siswa')->index(); // admin | guru | siswa
            $t->string('nis', 20)->nullable()->unique();
            $t->foreignId('major_id')->nullable()->constrained()->nullOnDelete();
            $t->unsignedTinyInteger('grade_level')->nullable(); // 10, 11, 12
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->dropConstrainedForeignId('major_id');
            $t->dropColumn(['role', 'nis', 'grade_level']);
        });
    }
};
