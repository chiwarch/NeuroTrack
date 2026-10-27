<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Announcement extends Model
{
    public const TARGETS = [
        'all' => 'Seluruh sekolah',
        'students' => 'Semua siswa',
        'teachers' => 'Semua guru',
        'parents' => 'Semua orang tua',
        'major' => 'Jurusan tertentu',
        'class' => 'Kelas tertentu',
    ];

    protected $fillable = ['title', 'content', 'target_type', 'published_at'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'announcement_classes', 'announcement_id', 'class_id');
    }

    public function majors(): BelongsToMany
    {
        return $this->belongsToMany(Major::class, 'announcement_majors', 'announcement_id', 'major_id');
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /** Pengumuman terbit yang ditujukan kepada $user (admin tidak lewat sini: admin melihat semuanya). */
    public function scopeVisibleTo(Builder $q, User $user): Builder
    {
        $q->published()->where(function (Builder $w) use ($user) {
            $w->where('target_type', 'all');
            $majorIds = [];

            if ($user->role === 'siswa') {
                $majorIds = array_filter([$user->major_id]);
                $w->orWhere('target_type', 'students');
                $classId = $user->classes()->value('classes.id');
                if ($classId) {
                    $w->orWhere(fn (Builder $c) => $c->where('target_type', 'class')
                        ->whereHas('classes', fn ($k) => $k->where('classes.id', $classId)));
                }
            } elseif ($user->role === 'orang_tua') {
                $w->orWhere('target_type', 'parents');
                $majorIds = $user->children()->pluck('users.major_id')->filter()->unique()->values()->all();
                // Pengumuman kelas ikut terlihat bila salah satu anaknya ada di kelas itu.
                $classIds = \Illuminate\Support\Facades\DB::table('class_memberships')->whereNull('left_at')
                    ->whereIn('student_id', $user->children()->pluck('users.id'))->pluck('class_id')->unique()->all();
                if ($classIds) {
                    $w->orWhere(fn (Builder $c) => $c->where('target_type', 'class')
                        ->whereHas('classes', fn ($k) => $k->whereIn('classes.id', $classIds)));
                }
            } elseif ($user->role === 'guru') {
                $w->orWhere('target_type', 'teachers');
                $majorIds = self::teacherMajorIds($user);
                $classIds = self::teacherClassIds($user);
                if ($classIds) {
                    $w->orWhere(fn (Builder $c) => $c->where('target_type', 'class')
                        ->whereHas('classes', fn ($k) => $k->whereIn('classes.id', $classIds)));
                }
            }

            // Pengumuman per jurusan: siswa jurusan itu, orang tuanya, dan guru yang mengajar jurusan itu.
            if ($majorIds) {
                $w->orWhere(fn (Builder $m) => $m->where('target_type', 'major')
                    ->whereHas('majors', fn ($k) => $k->whereIn('majors.id', $majorIds)));
            }
        });

        return $q;
    }

    /** Jurusan yang diajar guru: dari mapel kejuruannya dan dari jurusan siswa di kelas yang ia ajar/walikan. */
    public static function teacherMajorIds(User $guru): array
    {
        $fromSubjects = $guru->subjects()->whereNotNull('subjects.major_id')->pluck('subjects.major_id');
        $fromStudents = User::where('role', 'siswa')->whereNotNull('major_id')
            ->whereHas('classes', fn ($q) => $q->whereIn('classes.id', self::teacherClassIds($guru)))->pluck('major_id');

        return $fromSubjects->merge($fromStudents)->unique()->values()->all();
    }

    /** Kelas yang diajar atau diwalikan guru. */
    public static function teacherClassIds(User $guru): array
    {
        return ClassSubject::where('teacher_id', $guru->id)->pluck('class_id')
            ->merge(SchoolClass::where('homeroom_teacher_id', $guru->id)->pluck('id'))
            ->unique()->values()->all();
    }

    public function status(): string
    {
        if ($this->published_at === null) {
            return 'draft';
        }

        return $this->published_at->isFuture() ? 'scheduled' : 'published';
    }

    public function targetLabel(): string
    {
        if ($this->target_type === 'class') {
            return $this->classes->pluck('name')->sort()->join(', ') ?: 'Kelas tertentu';
        }

        if ($this->target_type === 'major') {
            return 'Jurusan '.($this->majors->pluck('code')->sort()->join(', ') ?: 'tertentu');
        }

        return self::TARGETS[$this->target_type] ?? $this->target_type;
    }
}
