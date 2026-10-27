<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public const ROLES = ['admin' => 'Admin', 'guru' => 'Guru', 'siswa' => 'Siswa', 'orang_tua' => 'Orang Tua'];

    protected $fillable = ['name', 'email', 'password', 'role', 'nis', 'major_id', 'grade_level'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed', 'email_verified_at' => 'datetime'];
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isGuru(): bool { return $this->role === 'guru'; }
    public function isSiswa(): bool { return $this->role === 'siswa'; }
    public function isOrangTua(): bool { return $this->role === 'orang_tua'; }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    /** Mapel yang diampu (khusus guru). */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher', 'user_id', 'subject_id');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'teacher_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }

    /** Anak yang terhubung (khusus orang tua), via parent_student. */
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_student', 'parent_id', 'student_id')->withPivot('relationship');
    }

    /** Orang tua yang terhubung (khusus siswa). */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_student', 'student_id', 'parent_id')->withPivot('relationship');
    }

    /** Kelas aktif siswa (via class_memberships). */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_memberships', 'student_id', 'class_id')->wherePivotNull('left_at');
    }

    /** Kelas yang diwalikan (khusus guru). */
    public function homeroomClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'homeroom_teacher_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function initials(): string
    {
        return collect(explode(' ', $this->name))->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    }
}
