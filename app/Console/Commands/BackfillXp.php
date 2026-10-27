<?php

namespace App\Console\Commands;

use App\Models\Attempt;
use App\Models\Attendance;
use App\Services\Gamification;
use Illuminate\Console\Command;

/** Hitung XP & lencana dari absensi dan kuis yang SUDAH ada. Aman diulang (XP tidak pernah ganda). */
class BackfillXp extends Command
{
    protected $signature = 'xp:backfill';

    protected $description = 'Hitung ulang XP, level, dan lencana dari data absensi dan kuis yang sudah ada';

    public function handle(): int
    {
        $n = 0;
        Attendance::whereIn('status', array_keys(Gamification::ATTENDANCE_XP))->orderBy('id')->each(function ($a) use (&$n) {
            Gamification::syncAttendance($a);
            $n++;
        });

        Attempt::with('quiz:id,title')->where('status', 'selesai')->get()->unique(fn ($a) => $a->user_id.'-'.$a->quiz_id)
            ->each(function ($a) use (&$n) {
                Gamification::syncQuiz($a);
                $n++;
            });

        $this->info("Selesai. {$n} catatan diproses.");

        return self::SUCCESS;
    }
}
