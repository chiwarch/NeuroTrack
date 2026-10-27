{{-- Lencana yang sudah didapat. Opsional: $gUser. --}}
@php $owned = \App\Models\UserBadge::with('badge')->where('user_id', ($gUser ?? auth()->user())->id)->latest('earned_at')->get(); @endphp
@if ($owned->isEmpty())
    <p class="text-sm text-mute">Belum ada lencana.</p>
@else
    <div class="flex flex-wrap gap-3">
        @foreach ($owned as $o)
            <div class="flex items-center gap-3 rounded-2xl bg-slate-50 py-2 pl-2 pr-4" title="{{ $o->badge->description }}">
                <x-badge-icon :badge="$o->badge" :size="40" />
                <span class="text-sm font-bold">{{ $o->badge->name }}</span>
            </div>
        @endforeach
    </div>
@endif
