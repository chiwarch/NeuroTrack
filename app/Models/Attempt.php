<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attempt extends Model
{
    protected $fillable = ['quiz_id', 'user_id', 'started_at', 'finished_at', 'score', 'status'];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'finished_at' => 'datetime', 'score' => 'float'];
    }

    protected static function booted(): void
    {
        static::saved(function (Attempt $a) {
            if ($a->status === 'selesai') {
                rescue(fn () => \App\Services\Gamification::syncQuiz($a));
            }
        });
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class);
    }

    public function deadline(): Carbon
    {
        return $this->started_at->copy()->addMinutes($this->quiz->duration_minutes);
    }

    public function isFinished(): bool
    {
        return $this->status !== 'berjalan';
    }

    public function passed(): bool
    {
        return $this->status === 'selesai' && $this->score >= $this->quiz->passing_score;
    }
}
