<?php

namespace App\Http\Controllers;

use App\Support\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Auth::check() ? redirect()->route('dashboard') : view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ], [
            'identifier.required' => 'Email atau NIS wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $field = filter_var($data['identifier'], FILTER_VALIDATE_EMAIL) ? 'email' : 'nis';
        $id = Str::lower($data['identifier']);

        // Batasi tebak kata sandi: 5 gagal per akun+perangkat atau 20 gagal per akun (dari mana pun) dalam 15 menit.
        $keyDevice = 'login:'.$id.'|'.$request->ip();
        $keyAccount = 'login-akun:'.$id;
        if (RateLimiter::tooManyAttempts($keyDevice, 5) || RateLimiter::tooManyAttempts($keyAccount, 20)) {
            $wait = max(RateLimiter::availableIn($keyDevice), RateLimiter::availableIn($keyAccount));
            Audit::record('login.locked', 'Terlalu banyak percobaan untuk '.$id);

            return back()->withErrors(['identifier' => 'Terlalu banyak percobaan masuk. Coba lagi dalam '.max(1, (int) ceil($wait / 60)).' menit.'])->onlyInput('identifier');
        }

        if (Auth::attempt([$field => $data['identifier'], 'password' => $data['password']], $request->boolean('remember'))) {
            RateLimiter::clear($keyDevice);
            $request->session()->regenerate();
            Audit::record('login.success', 'Masuk sebagai '.Auth::user()->role);

            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($keyDevice, 900);
        RateLimiter::hit($keyAccount, 900);
        Audit::record('login.failed', 'Gagal masuk untuk '.$id, null);

        return back()->withErrors(['identifier' => 'Email/NIS atau kata sandi salah.'])->onlyInput('identifier');
    }

    public function logout(Request $request): RedirectResponse
    {
        Audit::record('logout');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
