<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    protected $fillable = ['class_subject_id', 'created_by', 'title', 'description', 'deadline', 'max_score'];

    protected function casts(): array
    {
        return ['deadline' => 'datetime', 'max_score' => 'float'];
    }

    public function classSubject(): BelongsTo
    {
        return $this->belongsTo(ClassSubject::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function isPastDeadline(): bool
    {
        return $this->deadline !== null && $this->deadline->isPast();
    }
}
