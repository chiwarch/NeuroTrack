<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Akses Ditolak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-ink grid place-items-center p-6">
    <div data-testid="forbidden" class="t-card max-w-md text-center p-8">
        <h1 class="text-3xl font-extrabold text-rose-600">403</h1>
        <p class="mt-2 text-lg font-bold">Akses Ditolak</p>
        <p class="mt-1 text-sm text-mute">{{ $exception?->getMessage() ?: 'Kamu tidak memiliki izin untuk mengakses halaman ini.' }}</p>
        <a href="{{ url('/') }}" class="t-btn t-btn-primary mt-6 inline-block">Kembali</a>
    </div>
</body>
</html>
