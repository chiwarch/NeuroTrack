<?php

namespace App\Services;

use App\Models\AttendanceSession;

/**
 * Token QR yang berganti tiap STEP detik (stateless, ditandatangani HMAC dengan APP_KEY).
 * Token dianggap sah pada jendela waktu saat ini dan satu jendela sebelumnya (±20–40 detik),
 * jadi tautan/foto QR yang dikirim ke teman cepat kedaluwarsa.
 */
class AttendanceToken
{
    public const STEP = 20;

    public static function make(AttendanceSession $session, ?int $now = null): string
    {
        $window = intdiv($now ?? time(), self::STEP);

        return $session->id.'.'.$window.'.'.self::sign($session->id, $window);
    }

    /** Detik tersisa sebelum token berganti. */
    public static function secondsLeft(?int $now = null): int
    {
        return self::STEP - (($now ?? time()) % self::STEP);
    }

    /** @return int|null id sesi jika token sah, null jika tidak */
    public static function verify(?string $token, ?int $now = null): ?int
    {
        $parts = explode('.', (string) $token);
        if (count($parts) !== 3 || ! ctype_digit($parts[0]) || ! ctype_digit($parts[1])) {
            return null;
        }
        [$id, $window, $sig] = [(int) $parts[0], (int) $parts[1], $parts[2]];

        if (! hash_equals(self::sign($id, $window), $sig)) {
            return null;
        }

        $current = intdiv($now ?? time(), self::STEP);

        return ($window === $current || $window === $current - 1) ? $id : null;
    }

    private static function sign(int $sessionId, int $window): string
    {
        return substr(hash_hmac('sha256', $sessionId.'|'.$window, (string) config('app.key')), 0, 20);
    }
}
