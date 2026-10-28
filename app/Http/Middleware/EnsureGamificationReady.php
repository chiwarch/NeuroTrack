<?php

namespace App\Http\Middleware;

use App\Services\Gamification;
use Closure;
use Illuminate\Http\Request;

/** Tampilkan petunjuk yang jelas (bukan error 500) bila migrasi gamifikasi belum dijalankan. */
class EnsureGamificationReady
{
    public function handle(Request $request, Closure $next)
    {
        if (! Gamification::ready()) {
            return response()->view('gamification.not-ready', [], 503);
        }

        return $next($request);
    }
}
