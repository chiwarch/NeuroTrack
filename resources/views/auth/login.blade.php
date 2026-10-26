<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — NeuroTrack</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-brand-50 via-pink-50 to-orange-50 font-sans text-ink antialiased">
<div class="mx-auto grid min-h-screen max-w-5xl items-center gap-10 p-6 lg:grid-cols-2">
    <div class="hidden lg:block">
        <a href="{{ route('home') }}" class="logo mb-6"><img src="{{ asset('images/grad-cap.webp') }}" alt="" width="40" height="40"><span>Neuro<b>Track</b></span></a>
        <h1 class="text-4xl font-extrabold leading-tight tracking-tight">Belajar lebih seru,<br><span class="grad-text">satu kuis sekali waktu.</span></h1>
        <p class="mt-4 max-w-sm text-mute">Masuk untuk mengerjakan kuis, memantau progres, dan mengelola kelasmu.</p>
        <img src="{{ asset('images/hero.webp') }}" alt="" class="mt-6 w-80" width="320" height="265">
    </div>

    <div class="t-card mx-auto w-full max-w-md p-8 shadow-xl shadow-brand-500/10">
        <a href="{{ route('home') }}" class="logo mb-6 lg:hidden"><img src="{{ asset('images/grad-cap.webp') }}" alt="" width="32" height="32"><span>Neuro<b>Track</b></span></a>
        <h2 class="text-2xl font-extrabold">Masuk</h2>
        <p class="mt-1 text-sm text-mute">Gunakan email atau NIS dan kata sandi dari sekolah.</p>

        <form method="POST" action="{{ route('login.attempt') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="identifier" class="t-label">Email atau NIS</label>
                <input id="identifier" name="identifier" type="text" value="{{ old('identifier') }}" required autofocus autocomplete="username" class="t-input">
                @error('identifier') <p class="t-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password" class="t-label">Kata sandi</label>
                <input id="password" name="password" type="password" required autocomplete="current-password" class="t-input">
                @error('password') <p class="t-error">{{ $message }}</p> @enderror
            </div>
            <label class="flex items-center gap-2 text-sm text-mute"><input type="checkbox" name="remember" class="size-4 rounded border-slate-300"> Ingat saya</label>
            <button type="submit" class="t-btn t-btn-primary w-full py-3">Masuk</button>
        </form>

        @if (app()->environment('local'))
            <div class="mt-6 rounded-xl bg-slate-50 p-3 text-xs text-mute">
                <p class="mb-1 font-bold text-ink">Akun contoh (lokal, kata sandi: password)</p>
                <p>admin@neurotrack.test · guru@neurotrack.test · siswa@neurotrack.test (atau NIS 10001)</p>
            </div>
        @endif
    </div>
</div>
</body>
</html>
