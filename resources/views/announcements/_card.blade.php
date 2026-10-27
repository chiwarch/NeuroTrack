{{-- Satu kartu pengumuman. Butuh: $a (Announcement) --}}
<article class="t-card">
    <div class="flex flex-wrap items-center gap-2 text-xs text-mute">
        <span class="t-badge bg-brand-100 text-brand-600">{{ $a->targetLabel() }}</span>
        <span>{{ $a->creator?->name }} · {{ $a->published_at?->translatedFormat('d M Y, H:i') }}</span>
    </div>
    <h3 class="mt-2 text-base font-extrabold">{{ $a->title }}</h3>
    <p class="mt-1 whitespace-pre-line text-sm">{{ $a->content }}</p>
</article>
