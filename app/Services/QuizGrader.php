<?php

namespace App\Services;

use App\Models\Attempt;
use App\Models\AttemptAnswer;
use Illuminate\Support\Facades\DB;

class QuizGrader
{
    /** Toleransi jaringan setelah waktu habis (detik). */
    public const GRACE_SECONDS = 30;

    /** Nilai semua jawaban di SERVER; kunci jawaban tidak pernah dikirim ke browser. */
    public static function grade(Attempt $attempt, array $answers): Attempt
    {
        return DB::transaction(function () use ($attempt, $answers) {
            $attempt = Attempt::with('quiz.questions.options')->lockForUpdate()->findOrFail($attempt->id);
            if ($attempt->isFinished()) {
                return $attempt;
            }

            $earned = 0;
            $total = 0;
            foreach ($attempt->quiz->questions as $q) {
                $total += $q->points;
                $given = $answers[$q->id] ?? null;
                $optionId = null;
                $text = null;
                $correct = false;

                if ($q->type === 'pilihan_ganda') {
                    $opt = is_scalar($given) ? $q->options->firstWhere('id', (int) $given) : null;
                    if ($opt) {
                        $optionId = $opt->id;
                        $correct = $opt->is_correct;
                    }
                } else {
                    $text = is_string($given) ? mb_substr(trim($given), 0, 255) : null;
                    $correct = $q->matchesText($text);
                }

                $pts = $correct ? $q->points : 0;
                $earned += $pts;
                AttemptAnswer::updateOrCreate(
                    ['attempt_id' => $attempt->id, 'question_id' => $q->id],
                    ['option_id' => $optionId, 'text_answer' => $text, 'is_correct' => $correct, 'points_awarded' => $pts]
                );
            }

            $attempt->update([
                'finished_at' => now(),
                'status' => 'selesai',
                'score' => $total > 0 ? round($earned / $total * 100, 1) : 0,
            ]);

            return $attempt;
        });
    }

    public static function expire(Attempt $attempt): Attempt
    {
        if (! $attempt->isFinished()) {
            $attempt->update(['finished_at' => now(), 'status' => 'kedaluwarsa', 'score' => 0]);
        }

        return $attempt;
    }
}
