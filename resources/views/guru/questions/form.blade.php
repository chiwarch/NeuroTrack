@extends('layouts.panel')
@section('title', $question->exists ? 'Ubah Soal' : 'Tambah Soal')

@section('content')
    @php
        $opts = array_pad(array_values(old('options', $question->options->pluck('body')->all())), 5, '');
        $correct = old('correct', $question->options->search(fn ($o) => $o->is_correct));
        $accepted = old('accepted', implode("\n", $question->accepted_answers ?? []));
    @endphp

    <a href="{{ route('guru.quizzes.edit', $quiz) }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-mute hover:text-brand-600"><x-icon name="arrow-left" :size="16" /> {{ $quiz->title }}</a>
    <h1 class="t-page-title mb-6">{{ $question->exists ? 'Ubah Soal' : 'Tambah Soal' }}</h1>

    <form method="POST" action="{{ $question->exists ? route('guru.questions.update', [$quiz, $question]) : route('guru.questions.store', $quiz) }}" class="t-card max-w-3xl space-y-5">
        @csrf
        @if ($question->exists) @method('PUT') @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="q-type" class="t-label">Jenis soal</label>
                <select id="q-type" name="type" class="t-input">
                    @foreach (\App\Models\Question::TYPES as $k => $label)
                        <option value="{{ $k }}" @selected(old('type', $question->type) === $k)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="points" class="t-label">Poin</label>
                <input id="points" name="points" type="number" min="1" max="100" value="{{ old('points', $question->points) }}" class="t-input" required>
                @error('points') <p class="t-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="body" class="t-label">Teks soal</label>
            <textarea id="body" name="body" rows="4" class="t-input" required maxlength="2000">{{ old('body', $question->body) }}</textarea>
            @error('body') <p class="t-error">{{ $message }}</p> @enderror
        </div>

        <div data-section="pilihan_ganda">
            <p class="t-label">Pilihan jawaban</p>
            <div class="space-y-2">
                @foreach ($opts as $i => $text)
                    <div class="flex items-center gap-3">
                        <input type="radio" name="correct" value="{{ $i }}" class="size-4 accent-brand-500" aria-label="Jawaban benar {{ chr(65 + $i) }}" @checked((string) $correct === (string) $i)>
                        <span class="w-5 text-sm font-bold text-mute">{{ chr(65 + $i) }}</span>
                        <input type="text" name="options[{{ $i }}]" value="{{ $text }}" maxlength="255" class="t-input" placeholder="{{ $i < 2 ? 'Wajib diisi' : 'Opsional' }}">
                    </div>
                @endforeach
            </div>
            <p class="t-hint">Isi minimal 2 pilihan, lalu pilih bulatan di kiri untuk menandai jawaban yang benar.</p>
            @error('options') <p class="t-error">{{ $message }}</p> @enderror
            @error('correct') <p class="t-error">{{ $message }}</p> @enderror
        </div>

        <div data-section="isian" class="hidden">
            <label for="accepted" class="t-label">Jawaban yang diterima (satu per baris)</label>
            <textarea id="accepted" name="accepted" rows="4" class="t-input" placeholder="mis.&#10;children">{{ $accepted }}</textarea>
            <p class="t-hint">Huruf besar/kecil dan spasi berlebih diabaikan. Tulis semua variasi yang dianggap benar.</p>
            @error('accepted') <p class="t-error">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3">
            <button type="submit" class="t-btn t-btn-primary">Simpan Soal</button>
            <a href="{{ route('guru.quizzes.edit', $quiz) }}" class="t-btn t-btn-line">Batal</a>
        </div>
    </form>
@endsection
