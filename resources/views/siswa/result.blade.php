@extends('layouts.panel')
@section('title', 'Hasil Kuis')

@section('content')
    @php
        $expired = $attempt->status === 'kedaluwarsa';
        $correct = $answers->where('is_correct', true)->count();
    @endphp

    <a href="{{ route('siswa.quizzes.show', $attempt->quiz) }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-mute hover:text-brand-600"><x-icon name="arrow-left" :size="16" /> {{ $attempt->quiz->title }}</a>

    <div class="t-card flex flex-wrap items-center gap-6">
        @if ($expired)
            <div class="grid size-28 place-items-center rounded-full bg-slate-100 text-sm font-bold text-slate-500">Waktu habis</div>
            <div>
                <h1 class="text-xl font-extrabold">Waktu pengerjaan habis</h1>
                <p class="mt-1 text-sm text-mute">Jawaban tidak sempat dikumpulkan. Kamu bisa mencoba lagi kapan saja.</p>
            </div>
        @else
            <div class="ring" style="--p: {{ $attempt->score }}; width: 7rem; height: 7rem"><span>{{ $attempt->score + 0 }}</span></div>
            <div>
                <h1 class="text-xl font-extrabold">{{ $attempt->passed() ? 'Hebat, kamu lulus! 🎉' : 'Belum mencapai nilai lulus' }}</h1>
                <p class="mt-1 text-sm text-mute">{{ $correct }} dari {{ $questions->count() }} soal benar · Nilai lulus {{ $attempt->quiz->passing_score }}</p>
                @unless ($attempt->passed()) <p class="mt-1 text-sm text-mute">Tidak apa-apa, pelajari lagi dan coba kembali. Nilai terbaikmu yang tersimpan.</p> @endunless
            </div>
        @endif
        <form method="POST" action="{{ route('siswa.quizzes.start', $attempt->quiz) }}" class="ml-auto">
            @csrf <button class="t-btn t-btn-soft" type="submit">Coba Lagi</button>
        </form>
    </div>

    @if (! $expired)
        @if ($attempt->quiz->show_answers)
            <h2 class="mt-8 mb-3 text-lg font-extrabold">Pembahasan</h2>
            <div class="space-y-4">
                @foreach ($questions as $i => $q)
                    @php $ans = $answers->get($q->id); $ok = $ans?->is_correct; @endphp
                    <div class="t-card {{ $ok ? '!ring-emerald-200' : '!ring-rose-200' }}">
                        <div class="flex gap-3">
                            <span class="grid size-8 shrink-0 place-items-center rounded-full text-sm font-extrabold {{ $ok ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{{ $i + 1 }}</span>
                            <div class="min-w-0 flex-1 text-sm">
                                <p class="whitespace-pre-line font-semibold">{{ $q->body }}</p>

                                @if ($q->type === 'pilihan_ganda')
                                    <ul class="mt-3 space-y-1.5">
                                        @foreach ($q->options as $o)
                                            @php $picked = $ans && $ans->option_id === $o->id; @endphp
                                            <li class="flex items-center gap-2 rounded-lg px-3 py-2 {{ $o->is_correct ? 'bg-emerald-50 font-semibold text-emerald-800' : ($picked ? 'bg-rose-50 text-rose-800' : 'text-mute') }}">
                                                <span>{{ $o->body }}</span>
                                                @if ($picked) <span class="t-badge ml-auto bg-white">Jawabanmu</span> @elseif ($o->is_correct) <span class="t-badge ml-auto bg-white">Kunci</span> @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                    @unless ($ans && $ans->option_id) <p class="mt-2 text-xs text-mute">Kamu tidak menjawab soal ini.</p> @endunless
                                @else
                                    <p class="mt-3">Jawabanmu: <strong>{{ $ans?->text_answer ?: '(kosong)' }}</strong></p>
                                    @unless ($ok) <p class="mt-1 text-emerald-700">Jawaban yang diterima: {{ implode(' / ', $q->accepted_answers ?? []) }}</p> @endunless
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="mt-6 text-sm text-mute">Guru tidak menampilkan pembahasan untuk kuis ini.</p>
        @endif
    @endif
@endsection
