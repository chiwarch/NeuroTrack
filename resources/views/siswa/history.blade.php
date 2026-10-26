@extends('layouts.panel')
@section('title', 'Riwayat')

@section('content')
    <h1 class="t-page-title mb-6">Riwayat Pengerjaan</h1>

    @if ($attempts->isEmpty())
        <div class="t-card text-center text-sm text-mute">Kamu belum mengerjakan kuis apa pun.</div>
    @else
        <div class="t-card overflow-x-auto p-0">
            <table class="w-full">
                <thead class="border-b border-slate-100"><tr><th class="t-th">Kuis</th><th class="t-th">Selesai</th><th class="t-th">Hasil</th><th class="t-th"></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($attempts as $a)
                        <tr>
                            <td class="t-td font-bold">{{ $a->quiz->title }}<span class="block text-xs font-medium text-mute">{{ $a->quiz->subject->name }}</span></td>
                            <td class="t-td text-mute">{{ $a->finished_at->translatedFormat('d M Y, H:i') }}</td>
                            <td class="t-td">
                                @if ($a->status === 'kedaluwarsa') <span class="t-badge bg-slate-100 text-slate-600">Waktu habis</span>
                                @else <span class="t-badge {{ $a->passed() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">Nilai {{ $a->score + 0 }}</span> @endif
                            </td>
                            <td class="t-td text-right"><a href="{{ route('siswa.attempts.result', $a) }}" class="text-sm font-bold text-brand-600 hover:underline">Lihat</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $attempts->links() }}</div>
    @endif
@endsection
