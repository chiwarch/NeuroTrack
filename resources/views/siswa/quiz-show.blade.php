@extends('layouts.panel')
@section('title', $quiz->title)

@section('content')
    <a href="{{ route('siswa.quizzes.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-mute hover:text-brand-600"><x-icon name="arrow-left" :size="16" /> Katalog Kuis</a>

    <div class="t-card">
        <div class="flex flex-wrap items-start gap-5">
            <img src="{{ $quiz->subject->iconUrl() }}" alt="" class="size-24 object-contain" width="96" height="96">
            <div class="min-w-0 flex-1">
                <span class="t-badge bg-brand-50 text-brand-600">{{ $quiz->subject->name }} · {{ $quiz->subject->typeLabel() }}</span>
                <h1 class="mt-2 text-2xl font-extrabold">{{ $quiz->title }}</h1>
                <p class="mt-1 text-sm text-mute">Guru: {{ $quiz->teacher->name }}</p>
                @if ($quiz->description) <p class="mt-3 max-w-2xl text-sm">{{ $quiz->description }}</p> @endif
            </div>
        </div>

        <dl class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach ([['Jumlah soal', $quiz->questions_count], ['Durasi', $quiz->duration_minutes.' menit'], ['Nilai lulus', $quiz->passing_score], ['Nilai terbaikmu', $best !== null ? $best + 0 : '–']] as [$k, $v])
                <div class="rounded-xl bg-slate-50 p-3"><dt class="text-xs font-semibold text-mute">{{ $k }}</dt><dd class="text-lg font-extrabold">{{ $v }}</dd></div>
            @endforeach
        </dl>

        <form method="POST" action="{{ route('siswa.quizzes.start', $quiz) }}" class="mt-6">
            @csrf
            <button type="submit" class="t-btn t-btn-primary px-6 py-3" @disabled($quiz->questions_count === 0)>
                {{ $running ? 'Lanjutkan Kuis' : 'Mulai Kuis' }} <x-icon name="arrow-right" :size="16" />
            </button>
            <p class="t-hint">Waktu berjalan sejak kamu menekan mulai dan tidak bisa dijeda. Kamu boleh mengulang kuis untuk memperbaiki nilai.</p>
        </form>
    </div>

    @if ($attempts->isNotEmpty())
        <h2 class="mt-8 mb-3 text-lg font-extrabold">Riwayat Pengerjaan</h2>
        <div class="t-card overflow-x-auto p-0">
            <table class="w-full">
                <tbody class="divide-y divide-slate-100">
                    @foreach ($attempts as $a)
                        <tr>
                            <td class="t-td text-mute">{{ $a->finished_at->translatedFormat('d M Y, H:i') }}</td>
                            <td class="t-td">
                                @if ($a->status === 'kedaluwarsa') <span class="t-badge bg-slate-100 text-slate-600">Waktu habis</span>
                                @else <span class="t-badge {{ $a->passed() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">Nilai {{ $a->score + 0 }}</span> @endif
                            </td>
                            <td class="t-td text-right"><a href="{{ route('siswa.attempts.result', $a) }}" class="text-sm font-bold text-brand-600 hover:underline">Lihat hasil</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
