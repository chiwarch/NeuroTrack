<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    protected $fillable = ['assignment_id', 'student_id', 'file_path', 'file_name', 'answer_text', 'submitted_at', 'score', 'feedback', 'graded_by'];

    protected function casts(): array
    {
        return ['submitted_at' => 'datetime', 'score' => 'float'];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function isGraded(): bool
    {
        return $this->score !== null;
    }

    public function isLate(): bool
    {
        $deadline = $this->assignment->deadline;

        return $deadline !== null && $this->submitted_at !== null && $this->submitted_at->greaterThan($deadline);
    }

    /** Nilai dalam skala 0–100 (untuk hard skill). */
    public function percent(): ?float
    {
        $max = $this->assignment->max_score;

        return ($this->score === null || $max <= 0) ? null : min(100, $this->score / $max * 100);
    }
}
