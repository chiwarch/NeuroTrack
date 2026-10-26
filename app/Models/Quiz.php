<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = ['subject_id', 'title', 'description', 'duration_minutes', 'passing_score', 'show_answers', 'is_published'];

    protected function casts(): array
    {
        return ['show_answers' => 'boolean', 'is_published' => 'boolean'];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('position')->orderBy('id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }

    /** Kuis terbit yang boleh dilihat siswa: mapel umum + mapel jurusannya. */
    public function scopeAvailableFor(Builder $q, User $user): Builder
    {
        return $q->where('is_published', true)->whereHas('subject', function (Builder $s) use ($user) {
            $s->where('type', 'umum')->orWhere('major_id', $user->major_id);
        });
    }

    public function totalPoints(): int
    {
        return (int) $this->questions()->sum('points');
    }
}
