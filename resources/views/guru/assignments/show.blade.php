@extends('layouts.panel')
@section('title', $assignment->title)

@section('content')
    <a href="{{ route('guru.assignments.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-mute hover:text-brand-600"><x-icon name="arrow-left" :size="16" /> Tugas</a>
    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="t-page-title">{{ $assignment->title }}</h1>
            <p class="mt-1 text-sm text-mute">{{ $assignment->classSubject->schoolClass->name }} · {{ $assignment->classSubject->subject->name }} · nilai maks {{ $assignment->max_score + 0 }} · {{ $assignment->deadline ? 'tenggat '.$assignment->deadline->translatedFormat('d M Y, H:i') : 'tanpa tenggat' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('guru.assignments.edit', $assignment) }}" class="t-btn t-btn-line t-btn-sm"><x-icon name="edit" :size="14" /> Ubah</a>
            @if ($submissions->isEmpty())
                <form method="POST" action="{{ route('guru.assignments.destroy', $assignment) }}" onsubmit="return confirm('Hapus tugas ini?')">@csrf @method('DELETE')<button class="t-btn t-btn-danger t-btn-sm" aria-label="Hapus"><x-icon name="trash" :size="14" /></button></form>
            @endif
        </div>
    </div>
    @if ($assignment->description)
        <div class="t-card mb-6 whitespace-pre-line text-sm">{{ $assignment->description }}</div>
    @endif

    <form method="POST" action="{{ route('guru.assignments.grade', $assignment) }}">
        @csrf
        @error('scores.*') <p class="t-error mb-2">{{ $message }}</p> @enderror
        <div class="t-card overflow-x-auto p-0">
            <table class="w-full">
                <thead class="border-b border-slate-100"><tr><th class="t-th">Siswa</th><th class="t-th">Pengumpulan</th><th class="t-th">Nilai</th><th class="t-th">Umpan balik</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($students as $st)
                        @php $sub = $submissions->get($st->id); @endphp
                        <tr class="align-top">
                            <td class="t-td"><p class="font-bold">{{ $st->name }}</p><p class="text-xs text-mute">{{ $st->nis }}</p></td>
                            @if ($sub)
                                <td class="t-td">
                                    <p class="text-xs text-mute">{{ $sub->submitted_at->translatedFormat('d M, H:i') }}
                                        @if ($sub->isLate()) <span class="t-badge bg-amber-100 text-amber-700">Terlambat</span> @endif</p>
                                    @if ($sub->file_path)<a href="{{ route('guru.assignments.file', $sub) }}" class="mt-1 inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:underline">{{ $sub->file_name }}</a>@endif
                                    @if ($sub->answer_text)<details class="mt-1 text-sm"><summary class="cursor-pointer font-semibold text-mute">Lihat jawaban teks</summary><p class="mt-1 whitespace-pre-line">{{ $sub->answer_text }}</p></details>@endif
                                </td>
                                <td class="t-td"><input type="number" name="scores[{{ $sub->id }}]" min="0" max="{{ $assignment->max_score }}" step="0.5" value="{{ old("scores.{$sub->id}", $sub->score !== null ? $sub->score + 0 : null) }}" class="t-input !w-24 !py-1.5" aria-label="Nilai {{ $st->name }}"></td>
                                <td class="t-td"><input name="feedback[{{ $sub->id }}]" value="{{ old("feedback.{$sub->id}", $sub->feedback) }}" maxlength="1000" class="t-input !py-1.5" aria-label="Umpan balik {{ $st->name }}"></td>
                            @else
                                <td class="t-td text-mute" colspan="3">Belum mengumpulkan</td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="4" class="t-td text-center text-mute">Kelas ini belum punya siswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($submissions->isNotEmpty())
            <div class="mt-4 flex items-center justify-between gap-3">
                <p class="text-sm text-mute">{{ $submissions->count() }} dari {{ $students->count() }} siswa sudah mengumpulkan. Nilai yang disimpan langsung masuk ke hard skill siswa.</p>
                <button class="t-btn t-btn-primary">Simpan nilai</button>
            </div>
        @endif
    </form>
@endsection
