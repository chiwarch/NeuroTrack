@extends('layouts.panel')
@section('title', 'Profil')

@section('content')
    <div class="mb-6">
        <h1 class="t-page-title">Profil</h1>
        @if ($user->must_change_password)
            <p class="mt-2 rounded-xl bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700">Kata sandimu masih sementara. Ganti dengan kata sandi baru milikmu sendiri sebelum memakai aplikasi.</p>
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="t-card space-y-3">
            <h2 class="text-lg font-extrabold">Akun</h2>
            <dl class="space-y-2 text-sm">
                <div><dt class="text-xs font-bold uppercase tracking-wide text-mute">Nama</dt><dd class="font-semibold">{{ $user->name }}</dd></div>
                <div><dt class="text-xs font-bold uppercase tracking-wide text-mute">Email</dt><dd class="font-semibold">{{ $user->email }}</dd></div>
                @if ($user->nis)<div><dt class="text-xs font-bold uppercase tracking-wide text-mute">NIS</dt><dd class="font-semibold">{{ $user->nis }}</dd></div>@endif
                <div><dt class="text-xs font-bold uppercase tracking-wide text-mute">Peran</dt><dd class="font-semibold">{{ \App\Models\User::ROLES[$user->role] ?? $user->role }}</dd></div>
                @if ($user->major)<div><dt class="text-xs font-bold uppercase tracking-wide text-mute">Jurusan</dt><dd class="font-semibold">{{ $user->major->name }} · kelas {{ $user->grade_level }}</dd></div>@endif
            </dl>
            <p class="text-xs text-mute">Untuk mengubah nama, email, atau data lain, hubungi admin sekolah.</p>
        </section>

        <section class="t-card">
            <h2 class="mb-4 text-lg font-extrabold">Ganti kata sandi</h2>
            <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="current_password" class="t-label">Kata sandi saat ini</label>
                    <x-password-input id="current_password" name="current_password" required autocomplete="current-password" />
                    @error('current_password') <p class="t-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="new_password" class="t-label">Kata sandi baru</label>
                    <x-password-input id="new_password" name="password" required autocomplete="new-password" />
                    <p class="t-hint">Minimal 8 karakter, mengandung huruf dan angka.</p>
                    @error('password') <p class="t-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="t-label">Ulangi kata sandi baru</label>
                    <x-password-input id="password_confirmation" name="password_confirmation" required autocomplete="new-password" />
                </div>
                <button class="t-btn t-btn-primary">Simpan kata sandi</button>
                <p class="text-xs text-mute">Setelah diganti, perangkat lain yang sedang masuk dengan akunmu otomatis keluar.</p>
            </form>
        </section>
    </div>
@endsection
