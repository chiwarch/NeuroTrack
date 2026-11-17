@props(['items', 'max' => 100, 'color' => 'bg-brand-500', 'type' => 'hard'])
<div class="space-y-3">
    @foreach ($items as $it)
        @php
            $pct = $it['value'] === null ? 0 : max(0, min(100, $it['value'] / $max * 100));
            $key = \Illuminate\Support\Str::slug($it['label']);
        @endphp
        <div>
            <div class="mb-1 flex items-baseline justify-between gap-3">
                <p class="text-sm font-bold" data-testid="radar-axis-label-{{ $key }}">{{ $it['label'] }}</p>
                <p class="shrink-0 text-sm font-extrabold"
                   @if($type === 'soft')
                       data-testid="softradar-axis-value-{{ $key }}"
                   @else
                       data-testid="radar-axis-value-{{ $key }}"
                   @endif>
                    {{ $it['value'] === null ? 'belum dinilai' : number_format($it['value'], 1, ',', '.') }}
                </p>
            </div>
            <div class="h-2.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full {{ $color }}" style="width: {{ $pct }}%"></div></div>
            <p class="mt-1 text-xs text-mute">{{ $it['note'] }}</p>
        </div>
    @endforeach
</div>
