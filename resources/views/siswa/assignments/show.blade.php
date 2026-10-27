@extends('layouts.panel')
@section('title', $assignment->title)

@section('content')
    <a href="{{ route('siswa.assignments.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-mute hover:text-brand-600"><x-icon name="arrow-left" :size="16" /> Tugas</a>
    <div class="mb-6">
        <h1 class="t-page-title">{{ $assignment->title }}</h1>
        <p class="mt-1 text-sm text-mute">{{ $assignment->classSubject->subject->name }} · {{ $assignment->classSubject->teacher->name }} · nilai maks {{ $assignment->max_score + 0 }}</p>
        <p class="mt-1 text-sm font-semibold {{ $assignment->isPastDeadline() ? 'text-rose-600' : 'text-ink' }}">{{ $assignment->deadline ? 'Tenggat '.$assignment->deadline->translatedFormat('l, d F Y, H:i') : 'Tanpa tenggat' }}</p>
    </div>

    @if ($assignment->description)
        <div class="t-card mb-6 whitespace-pre-line text-sm">{{ $assignment->description }}</div>
    @endif

    @if ($submission && $submission->isGraded())
        <div class="t-card mb-6">
            <p class="text-sm font-semibold text-mute">Nilaimu</p>
            <p class="text-3xl font-extrabold">{{ $submission->score + 0 }}<span class="text-base font-bold text-mute"> / {{ $assignment->max_score + 0 }}</span></p>
            @if ($submission->feedback)<p class="mt-2 whitespace-pre-line text-sm">{{ $submission->feedback }}</p>@endif
        </div>
    @endif

    <div class="t-card max-w-2xl">
        <h2 class="mb-1 text-lg font-extrabold">{{ $submission ? 'Pengumpulanmu' : 'Kumpulkan tugas' }}</h2>
        @if ($submission)
            <p class="text-sm text-mute">Dikumpulkan {{ $submission->submitted_at->translatedFormat('d M Y, H:i') }}
                @if ($submission->isLate()) <span class="t-badge bg-amber-100 text-amber-700">Terlambat</span> @endif</p>
            @if ($submission->file_path)<a href="{{ route('siswa.assignments.file', $assignment) }}" class="mt-2 inline-block text-sm font-semibold text-brand-600 hover:underline">{{ $submission->file_name }}</a>@endif
            @if ($submission->answer_text)<p class="mt-2 whitespace-pre-line rounded-xl bg-slate-50 p-3 text-sm">{{ $submission->answer_text }}</p>@endif
        @endif

        @if (! ($submission && $submission->isGraded()))
            <form method="POST" action="{{ route('siswa.assignments.submit', $assignment) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                @if ($assignment->isPastDeadline())<p class="rounded-xl bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700">Tenggat sudah lewat. Tugas tetap bisa dikumpulkan tetapi ditandai terlambat.</p>@endif
                <div>
                    <label for="file" class="t-label">Berkas {{ $submission ? '(pilih lagi untuk mengganti)' : '' }}</label>
                    <input id="file" type="file" name="file" class="t-input">
                    <p class="t-hint">PDF, Word, PowerPoint, Excel, ZIP, teks, atau gambar. Maksimal 10 MB.</p>
                    @error('file') <p class="t-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="answer_text" class="t-label">Jawaban teks (opsional)</label>
                    <textarea id="answer_text" name="answer_text" rows="4" class="t-input" maxlength="5000">{{ old('answer_text', $submission?->answer_text) }}</textarea>
                    @error('answer_text') <p class="t-error">{{ $message }}</p> @enderror
                </div>
                <button class="t-btn t-btn-primary">{{ $submission ? 'Kumpulkan ulang' : 'Kumpulkan' }}</button>
            </form>
        @endif
    </div>
@endsection
