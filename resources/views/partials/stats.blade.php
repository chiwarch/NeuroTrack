{{-- Dua radar + grafik batang. Butuh: $hard, $soft (lihat App\Services\SkillStats). --}}
@php
    $panels = [
        ['Hard Skill', 'Dari nilai kuis tiap mata pelajaran (0–100).', $hard, 100, '#6d3df0', 'bg-brand-500'],
        ['Soft Skill', 'Penilaian guru, skala 1–5 (rata-rata semua guru).', $soft, \App\Services\SkillStats::SOFT_MAX, '#0ea5a4', 'bg-teal-500'],
    ];
@endphp
<div class="grid gap-6 lg:grid-cols-2">
    @foreach ($panels as [$title, $sub, $rows, $max, $hex, $bar])
        @php
            $withData = collect($rows)->whereNotNull('value');
            $top = $withData->count() >= 2 ? $withData->sortByDesc('value')->first() : null;
            $low = $withData->count() >= 2 ? $withData->sortBy('value')->first() : null;
        @endphp
        <section class="t-card">
            <h2 class="text-lg font-extrabold">{{ $title }}</h2>
            <p class="text-xs text-mute">{{ $sub }}</p>

            @if (count($rows) >= 3)
                <div class="mt-4"><x-radar :labels="array_column($rows, 'label')" :values="array_map(fn ($r) => $r['value'] ?? 0, $rows)" :max="$max" :color="$hex" /></div>
            @elseif (count($rows) === 0)
                <p class="mt-4 text-sm text-mute">Belum ada data untuk ditampilkan.</p>
            @else
                <p class="mt-4 text-xs text-mute">Radar tampil mulai dari 3 sumbu; lihat grafik batang di bawah.</p>
            @endif

            @if ($top && $low && $top['label'] !== $low['label'])
                <div class="mt-3 grid gap-2 text-xs sm:grid-cols-2">
                    <p class="rounded-xl bg-emerald-50 px-3 py-2 font-semibold text-emerald-700">Terkuat: {{ $top['label'] }}</p>
                    <p class="rounded-xl bg-amber-50 px-3 py-2 font-semibold text-amber-700">Perlu ditingkatkan: {{ $low['label'] }}</p>
                </div>
            @endif

            <div class="mt-5 border-t border-slate-100 pt-5"><x-skill-bars :items="$rows" :max="$max" :color="$bar" /></div>
        </section>
    @endforeach
</div>
