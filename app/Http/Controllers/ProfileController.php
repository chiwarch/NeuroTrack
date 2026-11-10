<?php

namespace App\Http\Controllers;

use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()->load('major:id,code,name')]);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'different:current_password', Password::min(8)->letters()->numbers()],
        ], [
            'current_password.required' => 'Isi kata sandi saat ini.',
            'current_password.current_password' => 'Kata sandi saat ini salah.',
            'password.required' => 'Isi kata sandi baru.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'password.different' => 'Kata sandi baru harus berbeda dari yang lama.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.letters' => 'Kata sandi baru harus mengandung huruf.',
            'password.numbers' => 'Kata sandi baru harus mengandung angka.',
        ]);

        $user->forceFill(['password' => $data['password'], 'must_change_password' => false, 'password_changed_at' => now()])->save();
        Auth::logoutOtherDevices($data['password']); // perangkat lain otomatis keluar
        $request->session()->regenerate();
        Audit::record('password.changed', 'Mengganti kata sandi sendiri');

        return redirect()->route('dashboard')->with('success', 'Kata sandi diperbarui. Perangkat lain otomatis keluar.');
    }
}
