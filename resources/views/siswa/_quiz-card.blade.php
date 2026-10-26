<a href="{{ route('siswa.quizzes.show', $quiz) }}" class="t-card group flex flex-col gap-3 transition hover:-translate-y-1 hover:shadow-lg">
    <div class="flex items-start gap-3">
        <img src="{{ $quiz->subject->iconUrl() }}" alt="" class="size-14 shrink-0 object-contain" width="56" height="56" loading="lazy">
        <div class="min-w-0">
            <span class="t-badge {{ $quiz->subject->type === 'kejuruan' ? 'bg-orange-100 text-orange-700' : 'bg-brand-50 text-brand-600' }}">{{ $quiz->subject->name }}</span>
            <h3 class="mt-1 line-clamp-2 font-extrabold leading-snug group-hover:text-brand-600">{{ $quiz->title }}</h3>
        </div>
    </div>
    <div class="mt-auto flex items-center gap-4 text-xs font-semibold text-mute">
        <span class="inline-flex items-center gap-1"><x-icon name="check-square" :size="14" /> {{ $quiz->questions_count }} soal</span>
        <span class="inline-flex items-center gap-1"><x-icon name="clock" :size="14" /> {{ $quiz->duration_minutes }} menit</span>
        @if (isset($best))
            <span class="t-badge ml-auto {{ $best >= $quiz->passing_score ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">Nilai {{ $best + 0 }}</span>
        @endif
    </div>
</a>
