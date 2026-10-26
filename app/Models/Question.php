<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    public const TYPES = ['pilihan_ganda' => 'Pilihan ganda', 'isian' => 'Isian singkat'];

    protected $fillable = ['quiz_id', 'type', 'body', 'points', 'position', 'accepted_answers'];

    protected function casts(): array
    {
        return ['accepted_answers' => 'array'];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class)->orderBy('id');
    }

    /** Cocokkan jawaban isian: abaikan huruf besar/kecil dan spasi berlebih. */
    public static function normalize(string $text): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/u', ' ', $text)));
    }

    public function matchesText(?string $text): bool
    {
        if ($text === null || trim($text) === '') {
            return false;
        }
        $given = self::normalize($text);

        return collect($this->accepted_answers ?? [])->contains(fn ($a) => self::normalize((string) $a) === $given);
    }
}
