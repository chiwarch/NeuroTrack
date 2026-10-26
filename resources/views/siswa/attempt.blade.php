@extends('layouts.panel')
@section('title', $attempt->quiz->title)

@section('content')
    <form id="quiz-form" method="POST" action="{{ route('siswa.attempts.submit', $attempt) }}" data-remaining="{{ $remaining }}">
        @csrf

        <div class="sticky top-14 z-10 -mx-1 mb-6 flex items-center justify-between gap-4 rounded-2xl bg-white/95 px-5 py-3 shadow-sm ring-1 ring-slate-100 backdrop-blur lg:top-4">
            <div class="min-w-0">
                <p class="truncate font-extrabold">{{ $attempt->quiz->title }}</p>
                <p class="text-xs text-mute">{{ $attempt->quiz->subject->name }} · {{ $questions->count() }} soal</p>
            </div>
            <div class="flex items-center gap-2 text-lg font-extrabold tabular-nums"><x-icon name="clock" :size="18" /> <span id="timer">--:--</span></div>
        </div>

        <div class="space-y-5">
            @foreach ($questions as $i => $q)
                <fieldset class="t-card">
                    <legend class="sr-only">Soal {{ $i + 1 }}</legend>
                    <div class="flex gap-3">
                        <span class="grid size-8 shrink-0 place-items-center rounded-full bg-brand-50 text-sm font-extrabold text-brand-600">{{ $i + 1 }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="whitespace-pre-line font-semibold">{{ $q->body }}</p>
                            <p class="mt-1 text-xs text-mute">{{ $q->points }} poin</p>

                            @if ($q->type === 'pilihan_ganda')
                                <div class="mt-4 space-y-2">
                                    @foreach ($q->options as $o)
                                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm transition hover:bg-brand-50 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                                            <input type="radio" name="answers[{{ $q->id }}]" value="{{ $o->id }}" class="size-4 accent-brand-500">
                                            <span>{{ $o->body }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <input type="text" name="answers[{{ $q->id }}]" maxlength="255" autocomplete="off" placeholder="Ketik jawabanmu" class="t-input mt-4 max-w-md">
                            @endif
                        </div>
                    </div>
                </fieldset>
            @endforeach
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="t-btn t-btn-primary px-8 py-3" onclick="return confirm('Kumpulkan jawaban sekarang? Jawaban tidak bisa diubah setelah dikumpulkan.')">Kumpulkan Jawaban</button>
        </div>
    </form>
@endsection
