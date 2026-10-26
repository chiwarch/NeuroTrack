<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkillAssessmentScore extends Model
{
    protected $fillable = ['assessment_id', 'skill_id', 'score', 'evidence'];

    protected function casts(): array
    {
        return ['score' => 'float'];
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}
