<?php

namespace App\Services;

use App\Models\Attempt;
use App\Models\Attendance;
use App\Models\Badge;
use App\Models\Level;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\XpTransaction;

/**
 * XP, level, dan lencana.
 *  - Hadir tepat waktu +10 XP, terlambat +5 XP (sakit/izin/alfa/dispensasi 0). Dikoreksi guru → XP ikut menyesuaikan.
 *  - Kuis: XP = 20% dari nilai TERBAIK tiap kuis (0–20 XP). Mengulang kuis hanya menaikkan XP bila nilainya naik.
 *  - Setiap XP punya `reference` unik per siswa sehingga tidak pernah terhitung dua kali.
 *  - Lencana: dari total XP (type xp), pencapaian otomatis (auto), atau diberikan guru/admin (manual).
 */
class Gamification
{
    public const ATTENDANCE_XP = ['present' => 10, 'late' => 5];
    public const QUIZ_XP_RATE = 0.2;

    public static function quizXp(float $score): int
    {
        return (int) round(max(0, min(100, $score)) * self::QUIZ_XP_RATE);
    }

    public static function syncAttendance(Attendance $a): void
    {
        $amount = self::ATTENDANCE_XP[$a->status] ?? 0;
        $date = $a->attendance_date->toDateString();
        self::setXp(
            $a->student_id, 'attendance:'.$date, $amount, 'Absensi',
            $amount ? ($a->status === 'late' ? 'Hadir (terlambat) ' : 'Hadir tepat waktu ').$a->attendance_date->translatedFormat('d M Y') : null,
        );
        self::afterChange($a->student_id);
    }

    public static function syncQuiz(Attempt $attempt): void
    {
        $best = (float) Attempt::where('user_id', $attempt->user_id)->where('quiz_id', $attempt->quiz_id)
            ->where('status', 'selesai')->max('score');
        $amount = self::quizXp($best);
        self::setXp($attempt->user_id, 'quiz:'.$attempt->quiz_id, $amount, 'Kuis', $amount ? 'Kuis: '.($attempt->quiz?->title ?? '') : null);
        self::afterChange($attempt->user_id);
    }

    private static function setXp(int $userId, string $reference, int $amount, string $source, ?string $description): void
    {
        if ($amount > 0) {
            XpTransaction::updateOrCreate(
                ['user_id' => $userId, 'reference' => $reference],
                ['amount' => $amount, 'source' => $source, 'description' => $description],
            );
        } else {
            XpTransaction::where('user_id', $userId)->where('reference', $reference)->delete();
        }
    }

    private static function afterChange(int $userId): void
    {
        self::refresh($userId);
        self::checkAuto($userId);
        self::refresh($userId);
    }

    public static function total(int $userId): int
    {
        return (int) XpTransaction::where('user_id', $userId)->sum('amount');
    }

    /** Beri lencana yang total XP-nya sudah cukup, lalu perbarui level. Diulang karena bonus XP lencana bisa membuka lencana lain. */
    public static function refresh(int $userId): void
    {
        for ($i = 0; $i < 6; $i++) {
            $total = self::total($userId);
            $earned = UserBadge::where('user_id', $userId)->pluck('badge_id');
            $new = Badge::where('type', 'xp')->where('required_xp', '>', 0)->where('required_xp', '<=', $total)
                ->whereNotIn('id', $earned)->get();
            if ($new->isEmpty()) {
                break;
            }
            foreach ($new as $badge) {
                self::grant($userId, $badge);
            }
        }

        $level = Level::where('min_xp', '<=', self::total($userId))->orderByDesc('min_xp')->first();
        User::whereKey($userId)->update(['current_level_id' => $level?->id]);
    }

    /** Beri lencana (sekali saja). Mengembalikan true bila baru didapat. */
    public static function grant(int $userId, Badge $badge, ?int $by = null, ?string $note = null): bool
    {
        $owned = UserBadge::firstOrCreate(
            ['user_id' => $userId, 'badge_id' => $badge->id],
            ['earned_at' => now(), 'awarded_by' => $by, 'note' => $note],
        );
        if (! $owned->wasRecentlyCreated) {
            return false;
        }
        if ($badge->xp_reward > 0) {
            XpTransaction::updateOrCreate(
                ['user_id' => $userId, 'reference' => 'badge:'.$badge->id],
                ['amount' => $badge->xp_reward, 'source' => 'Lencana', 'description' => 'Lencana: '.$badge->name],
            );
        }

        return true;
    }

    /** Cabut lencana beserta bonus XP-nya. */
    public static function revoke(UserBadge $owned): void
    {
        XpTransaction::where('user_id', $owned->user_id)->where('reference', 'badge:'.$owned->badge_id)->delete();
        $userId = $owned->user_id;
        $owned->delete();
        self::refresh($userId);
    }

    private static function checkAuto(int $userId): void
    {
        $ids = Badge::where('type', 'auto')->pluck('id', 'code');
        if ($ids->isEmpty()) {
            return;
        }
        $earned = UserBadge::where('user_id', $userId)->pluck('badge_id')->all();
        $give = function (string $code) use ($ids, $earned, $userId): void {
            if (isset($ids[$code]) && ! in_array($ids[$code], $earned, true)) {
                self::grant($userId, Badge::find($ids[$code]));
            }
        };

        $finished = Attempt::where('user_id', $userId)->where('status', 'selesai');
        if ((clone $finished)->where('score', '>=', 100)->exists()) {
            $give('perfect_quiz');
        }
        if ((clone $finished)->distinct()->count('quiz_id') >= 5) {
            $give('quiz_5');
        }

        $recent = Attendance::where('student_id', $userId)->where('status', '!=', 'dispensation')
            ->orderByDesc('attendance_date')->limit(5)->pluck('status');
        if ($recent->count() === 5 && $recent->every(fn ($s) => in_array($s, ['present', 'late'], true))) {
            $give('streak_5');
        }
        if (Attendance::where('student_id', $userId)->where('status', 'present')->count() >= 10) {
            $give('early_bird_10');
        }
    }

    public static function levelInfo(User $user): array
    {
        $xp = self::total($user->id);
        $levels = Level::orderBy('min_xp')->get();
        $current = $levels->last(fn ($l) => $l->min_xp <= $xp);
        $next = $levels->first(fn ($l) => $l->min_xp > $xp);

        return [
            'xp' => $xp,
            'level' => $current,
            'next' => $next,
            'toNext' => $next ? $next->min_xp - $xp : 0,
            'pct' => ($next && $current) ? (int) round(($xp - $current->min_xp) / max(1, $next->min_xp - $current->min_xp) * 100) : 100,
        ];
    }

    /** Peringkat siswa aktif satu kelas berdasarkan total XP (seri diurutkan menurut nama). */
    public static function leaderboard(SchoolClass $class)
    {
        $rows = User::query()
            ->select('users.id', 'users.name', 'users.current_level_id')
            ->selectRaw('COALESCE(SUM(xp_transactions.amount), 0) as xp')
            ->join('class_memberships', 'class_memberships.student_id', '=', 'users.id')
            ->where('class_memberships.class_id', $class->id)->whereNull('class_memberships.left_at')
            ->leftJoin('xp_transactions', 'xp_transactions.user_id', '=', 'users.id')
            ->groupBy('users.id', 'users.name', 'users.current_level_id')
            ->orderByDesc('xp')->orderBy('users.name')->get();

        $levels = Level::all()->keyBy('id');
        $badgeCounts = UserBadge::whereIn('user_id', $rows->pluck('id'))->selectRaw('user_id, count(*) as total')
            ->groupBy('user_id')->pluck('total', 'user_id');

        $rank = 0;
        $previous = null;
        return $rows->map(function ($row, $i) use ($levels, $badgeCounts, &$rank, &$previous) {
            if ($row->xp !== $previous) {
                $rank = $i + 1; // XP sama = peringkat sama
                $previous = $row->xp;
            }
            $row->rank = $rank;
            $row->level_name = $levels[$row->current_level_id]->name ?? '—';
            $row->badge_total = (int) ($badgeCounts[$row->id] ?? 0);

            return $row;
        });
    }
}
