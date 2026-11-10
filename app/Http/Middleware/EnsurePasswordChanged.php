<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** Pengguna dengan kata sandi sementara wajib menggantinya dulu sebelum memakai aplikasi. */
class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->must_change_password && ! $request->routeIs('profile.*', 'logout')) {
            return redirect()->route('profile.edit')->with('error', 'Demi keamanan, ganti kata sandi sementaramu dulu.');
        }

        return $next($request);
    }
}
