<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/** Catatan jejak aktivitas penting (masuk/keluar, kata sandi, pengelolaan pengguna, penilaian). Tidak pernah menggagalkan aksi utama. */
class Audit
{
    private static bool $ready = false;

    public static function record(string $event, ?string $description = null, ?int $userId = null): void
    {
        try {
            if (! self::$ready) {
                if (! Schema::hasTable('audit_logs')) {
                    return;
                }
                self::$ready = true;
            }
            $request = request();
            AuditLog::create([
                'user_id' => $userId ?? auth()->id(),
                'event' => $event,
                'description' => $description !== null ? Str::limit($description, 480, '') : null,
                'ip' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
