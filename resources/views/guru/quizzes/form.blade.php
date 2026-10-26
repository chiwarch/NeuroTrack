@extends('layouts.panel')
@section('title', $quiz->exists ? 'Kelola Kuis' : 'Buat Kuis')

@section('content')
    <a href="{{ route('guru.quizzes.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-mute hover:text-brand-600"><x-icon name="arrow-left" :size="16" /> Kuis Saya</a>
    <h1 class="t-page-title mb-6">{{ $quiz->exists ? 'Kelola Kuis' : 'Buat Kuis Baru' }}</h1>

    @if ($subjects->isEmpty())
        <div class="t-card text-sm text-mute">Kamu belum ditugaskan ke mata pelajaran mana pun, jadi belum bisa membuat kuis. Hubungi admin.</div>
    @else
        <form method="POST" action="{{ $quiz->exists ? route('guru.quizzes.update', $quiz) : route('guru.quizzes.store') }}" class="t-card max-w-3xl space-y-5">
            @csrf
            @if ($quiz->exists) @method('PUT') @endif

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="subject_id" class="t-label">Mata pelajaran</label>
                    <select id="subject_id" name="subject_id" class="t-input" required>
                        <option value="">— Pilih mapel —</option>
                        @foreach ($subjects as $s)
                            <option value="{{ $s->id }}" @selected((int) old('subject_id', $quiz->subject_id) === $s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                    @error('subject_id') <p class="t-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="title" class="t-label">Judul kuis</label>
                    <input id="title" name="title" value="{{ old('title', $quiz->title) }}" class="t-input" required maxlength="150">
                    @error('title') <p class="t-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="description" class="t-label">Deskripsi (opsional)</label>
                <textarea id="description" name="description" rows="3" class="t-input" maxlength="1000">{{ old('description', $quiz->description) }}</textarea>
                @error('description') <p class="t-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="duration_minutes" class="t-label">Durasi (menit)</label>
                    <input id="duration_minutes" type="number" min="1" max="180" name="duration_minutes" value="{{ old('duration_minutes', $quiz->duration_minutes) }}" class="t-input" required>
                    @error('duration_minutes') <p class="t-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="passing_score" class="t-label">Nilai lulus (0–100)</label>
                    <input id="passing_score" type="number" min="0" max="100" name="passing_score" value="{{ old('passing_score', $quiz->passing_score) }}" class="t-input" required>
                    @error('passing_score') <p class="t-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-2">
                <label class="flex items-center gap-2 text-sm font-semibold"><input type="checkbox" name="show_answers" value="1" class="size-4 accent-brand-500" @checked(old('show_answers', $quiz->show_answers))> Tampilkan pembahasan setelah siswa selesai</label>
                @if ($quiz->exists)
                    <label class="flex items-center gap-2 text-sm font-semibold"><input type="checkbox" name="is_published" value="1" class="size-4 accent-brand-500" @checked(old('is_published', $quiz->is_published))> Terbitkan (siswa bisa melihat dan mengerjakan)</label>
                    @error('is_published') <p class="t-error">{{ $message }}</p> @enderror
                @endif
            </div>

            <div><button type="submit" class="t-btn t-btn-primary">{{ $quiz->exists ? 'Simpan Perubahan' : 'Buat & Lanjut ke Soal' }}</button></div>
        </form>

        @if ($quiz->exists)
            <div class="mt-10 mb-3 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-extrabold">Soal ({{ $quiz->questions->count() }})</h2>
                <a href="{{ route('guru.questions.create', $quiz) }}" class="t-btn t-btn-primary t-btn-sm"><x-icon name="plus" :size="14" /> Tambah Soal</a>
            </div>

            @if ($quiz->questions->isEmpty())
                <div class="t-card max-w-3xl text-sm text-mute">Belum ada soal.</div>
            @else
                <div class="max-w-3xl space-y-3">
                    @foreach ($quiz->questions as $i => $q)
                        <div class="t-card flex items-start gap-3 p-4">
                            <span class="grid size-8 shrink-0 place-items-center rounded-full bg-brand-50 text-sm font-extrabold text-brand-600">{{ $i + 1 }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="line-clamp-2 text-sm font-semibold">{{ $q->body }}</p>
                                <p class="mt-1 text-xs text-mute">{{ \App\Models\Question::TYPES[$q->type] }} · {{ $q->points }} poin</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('guru.questions.edit', [$quiz, $q]) }}" class="t-btn t-btn-line t-btn-sm" aria-label="Ubah soal"><x-icon name="edit" :size="14" /></a>
                                <form method="POST" action="{{ route('guru.questions.destroy', [$quiz, $q]) }}" onsubmit="return confirm('Hapus soal ini?')">
                                    @csrf @method('DELETE')
                                    <button class="t-btn t-btn-danger t-btn-sm" aria-label="Hapus soal"><x-icon name="trash" :size="14" /></button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('guru.quizzes.destroy', $quiz) }}" onsubmit="return confirm('Hapus kuis ini beserta semua soalnya?')" class="mt-10">
                @csrf @method('DELETE')
                <button class="t-btn t-btn-danger t-btn-sm"><x-icon name="trash" :size="14" /> Hapus Kuis</button>
            </form>
        @endif
    @endif
@endsection
