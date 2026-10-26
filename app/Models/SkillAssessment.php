<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkillAssessment extends Model
{
    protected $fillable = ['student_id', 'assessed_by', 'source_type', 'period_start', 'period_end', 'assessed_at', 'notes'];

    protected function casts(): array
    {
        return ['period_start' => 'date', 'period_end' => 'date', 'assessed_at' => 'datetime'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(SkillAssessmentScore::class, 'assessment_id');
    }
}
