@extends('layouts.panel')
@section('title', 'Peringkat Kelas')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="t-page-title">Peringkat Kelas</h1>
            <p class="mt-1 text-sm text-mute">Berdasarkan total XP. Peringkat yang sama berarti XP-nya sama.</p>
        </div>
        @if ($classes->count() > 1)
            <form method="GET">
                <label for="kelas" class="sr-only">Kelas</label>
                <select id="kelas" name="kelas" class="t-input !w-auto" onchange="this.form.submit()">
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}" @selected($class && $class->id === $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    @if (! $class)
        <div class="t-card text-center text-sm text-mute">Belum ada kelas yang bisa ditampilkan.</div>
    @else
        @php $podium = $rows->where('rank', '<=', 3)->take(3); $tones = [1 => 'bg-amber-100 text-amber-600', 2 => 'bg-slate-100 text-slate-500', 3 => 'bg-orange-100 text-orange-600']; @endphp
        @if ($rows->isNotEmpty())
            <div class="mb-6 grid gap-4 sm:grid-cols-3">
                @foreach ($podium as $p)
                    <div class="t-card text-center {{ $p->id === $meId ? 'ring-2 ring-brand-500' : '' }}">
                        <span class="mx-auto grid size-12 place-items-center rounded-2xl text-xl font-extrabold {{ $tones[$p->rank] }}">{{ $p->rank }}</span>
                        <p class="mt-3 truncate font-extrabold">{{ $p->name }}</p>
                        <p class="text-xs text-mute">{{ $p->level_name }} · {{ $p->badge_total }} lencana</p>
                        <p class="mt-2 text-2xl font-extrabold">{{ number_format($p->xp, 0, ',', '.') }}<span class="text-xs font-bold text-mute"> XP</span></p>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="t-card overflow-x-auto p-0">
            <table class="w-full">
                <thead class="border-b border-slate-100"><tr><th class="t-th w-16">#</th><th class="t-th">Siswa</th><th class="t-th">Level</th><th class="t-th">Lencana</th><th class="t-th text-right">XP</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rows as $r)
                        <tr class="{{ $r->id === $meId ? 'bg-brand-50' : '' }}">
                            <td class="t-td font-extrabold">{{ $r->rank }}</td>
                            <td class="t-td font-bold">{{ $r->name }}@if ($r->id === $meId) <span class="t-badge bg-brand-100 text-brand-600">Kamu</span>@endif</td>
                            <td class="t-td">{{ $r->level_name }}</td>
                            <td class="t-td">{{ $r->badge_total }}</td>
                            <td class="t-td text-right font-extrabold">{{ number_format($r->xp, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="t-td text-center text-mute">Kelas ini belum punya siswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
@endsection
