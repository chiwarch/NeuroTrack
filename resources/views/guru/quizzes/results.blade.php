@extends('layouts.panel')
@section('title', 'Hasil Kuis')

@section('content')
    <a href="{{ route('guru.quizzes.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-mute hover:text-brand-600"><x-icon name="arrow-left" :size="16" /> Kuis Saya</a>
    <h1 class="t-page-title">Hasil: {{ $quiz->title }}</h1>
    <p class="mt-1 mb-6 text-sm text-mute">{{ $quiz->subject->name }} · nilai lulus {{ $quiz->passing_score }}. Nilai yang ditampilkan adalah nilai terbaik tiap siswa, diurutkan menurut nama.</p>

    @if ($rows->isEmpty())
        <div class="t-card text-center text-sm text-mute">Belum ada siswa yang menyelesaikan kuis ini.</div>
    @else
        <div class="mb-4 grid max-w-md grid-cols-2 gap-4">
            <div class="t-card p-4"><p class="text-2xl font-extrabold">{{ $rows->count() }}</p><p class="text-xs font-semibold text-mute">Siswa mengerjakan</p></div>
            <div class="t-card p-4"><p class="text-2xl font-extrabold">{{ round($rows->avg('best'), 1) }}</p><p class="text-xs font-semibold text-mute">Rata-rata nilai terbaik</p></div>
        </div>
        <div class="t-card overflow-x-auto p-0">
            <table class="w-full">
                <thead class="border-b border-slate-100"><tr><th class="t-th">Siswa</th><th class="t-th">NIS</th><th class="t-th">Percobaan</th><th class="t-th">Terakhir</th><th class="t-th">Nilai terbaik</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($rows as $r)
                        <tr>
                            <td class="t-td font-bold">{{ $r['user']->name }}</td>
                            <td class="t-td text-mute">{{ $r['user']->nis }}</td>
                            <td class="t-td">{{ $r['tries'] }}×</td>
                            <td class="t-td text-mute">{{ $r['last']->translatedFormat('d M Y, H:i') }}</td>
                            <td class="t-td"><span class="t-badge {{ $r['best'] >= $quiz->passing_score ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $r['best'] + 0 }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
