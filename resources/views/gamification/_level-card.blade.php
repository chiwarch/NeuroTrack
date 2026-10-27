{{-- Kartu level & XP. Opsional: $gUser (default pengguna yang masuk). --}}
@php $info = \App\Services\Gamification::levelInfo($gUser ?? auth()->user()); @endphp
@if ($info['level'])
    <div class="t-card flex flex-wrap items-center gap-5">
        <span class="grid size-16 shrink-0 place-items-center rounded-2xl bg-brand-100 text-brand-600"><x-icon name="award" :size="30" /></span>
        <div class="min-w-48 flex-1">
            <p class="text-xs font-bold uppercase tracking-wide text-mute">Level</p>
            <p class="text-xl font-extrabold leading-tight">{{ $info['level']->name }}</p>
            <div class="mt-2 h-2.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-brand-500" style="width: {{ $info['pct'] }}%"></div></div>
            <p class="mt-1 text-xs text-mute">{{ $info['next'] ? $info['toNext'].' XP lagi menuju '.$info['next']->name : 'Level tertinggi tercapai' }}</p>
        </div>
        <div class="text-right">
            <p class="text-3xl font-extrabold leading-none">{{ number_format($info['xp'], 0, ',', '.') }}</p>
            <p class="mt-1 text-xs font-semibold text-mute">Total XP</p>
        </div>
    </div>
@endif
