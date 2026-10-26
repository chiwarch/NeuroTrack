@props(['labels', 'values', 'max' => 100, 'color' => '#6d3df0', 'rings' => 5, 'unit' => ''])
@php
    // Radar murni SVG (tanpa JS/library). Sumbu pertama di atas, searah jarum jam.
    $n = max(count($labels), 3);
    $cx = 180; $cy = 150; $R = 100;
    $pt = function (int $i, float $r) use ($n, $cx, $cy) {
        $a = -M_PI / 2 + 2 * M_PI * $i / $n;
        return [round($cx + $r * cos($a), 1), round($cy + $r * sin($a), 1)];
    };
    $poly = fn (float $r) => collect(range(0, $n - 1))->map(fn ($i) => implode(',', $pt($i, $r)))->implode(' ');
    $shape = collect($labels)->keys()->map(function ($i) use ($values, $max, $R, $pt) {
        $v = (float) ($values[$i] ?? 0);
        return implode(',', $pt($i, $R * max(0, min(1, $v / $max))));
    })->implode(' ');
    $gid = 'rg'.substr(md5(json_encode($labels).$color), 0, 8);
@endphp
<svg viewBox="0 0 360 310" class="mx-auto block h-auto w-full max-w-md" role="img" aria-label="Radar chart">
    <defs>
        <radialGradient id="{{ $gid }}" cx="50%" cy="50%" r="50%">
            <stop offset="0%" stop-color="{{ $color }}" stop-opacity=".15"/>
            <stop offset="100%" stop-color="{{ $color }}" stop-opacity=".55"/>
        </radialGradient>
    </defs>

    @for ($l = 1; $l <= $rings; $l++)
        <polygon points="{{ $poly($R * $l / $rings) }}" fill="{{ $l % 2 ? '#f8fafc' : '#ffffff' }}" stroke="#e2e8f0" stroke-width="1"/>
    @endfor
    @for ($i = 0; $i < $n; $i++)
        @php [$x, $y] = $pt($i, $R); @endphp
        <line x1="{{ $cx }}" y1="{{ $cy }}" x2="{{ $x }}" y2="{{ $y }}" stroke="#e2e8f0" stroke-width="1"/>
    @endfor

    <polygon points="{{ $shape }}" fill="url(#{{ $gid }})" stroke="{{ $color }}" stroke-width="2.5" stroke-linejoin="round"/>
    @foreach ($labels as $i => $label)
        @php [$px, $py] = explode(',', explode(' ', $shape)[$i]); @endphp
        <circle cx="{{ $px }}" cy="{{ $py }}" r="4" fill="#fff" stroke="{{ $color }}" stroke-width="2.5"/>
    @endforeach

    @foreach ($labels as $i => $label)
        @php
            [$lx, $ly] = $pt($i, $R + 16);
            $anchor = $lx < $cx - 6 ? 'end' : ($lx > $cx + 6 ? 'start' : 'middle');
            $lines = explode("\n", wordwrap($label, 12, "\n", true));
            $dy = $ly > $cy + 6 ? 8 : ($ly < $cy - 6 ? -6 - 11 * (count($lines) - 1) : -4 * (count($lines) - 1));
        @endphp
        <text x="{{ $lx }}" y="{{ $ly + $dy }}" text-anchor="{{ $anchor }}" font-size="11" font-weight="700" fill="#1b1550">
            @foreach ($lines as $k => $line)<tspan x="{{ $lx }}" dy="{{ $k === 0 ? 0 : 12 }}">{{ $line }}</tspan>@endforeach
        </text>
    @endforeach
</svg>
