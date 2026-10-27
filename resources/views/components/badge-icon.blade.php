@props(['badge', 'earned' => true, 'size' => 56])
@php $cls = $earned ? $badge->toneClasses() : 'bg-slate-100 text-slate-300'; @endphp
<span {{ $attributes->merge(['class' => 'grid shrink-0 place-items-center rounded-2xl '.$cls]) }} style="width: {{ $size }}px; height: {{ $size }}px" aria-hidden="true">
    <x-icon :name="$badge->icon ?: 'award'" :size="(int) ($size * 0.5)" />
</span>
