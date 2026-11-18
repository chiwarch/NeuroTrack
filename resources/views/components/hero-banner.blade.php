{{-- resources/views/components/hero-banner.blade.php
     Pemakaian: <x-hero-banner href="{{ route('login') }}" />
     Butuh font Sora + Inter (lihat catatan di bawah file ini). --}}
@props(['href' => '#', 'cta' => 'Mulai Sekarang'])

<section {{ $attributes->merge(['class' => 'relative isolate overflow-hidden bg-[#f7f9fc] font-sans text-[#0b1f4d]']) }}>
    {{-- lingkaran tosca tipis di pojok kanan atas --}}
    <div aria-hidden="true"
         class="absolute -right-24 -top-32 -z-10 h-[420px] w-[420px] rounded-full bg-[#19d3c5]/10 lg:-right-24 lg:-top-32 lg:h-[460px] lg:w-[460px]"></div>

    <div class="mx-auto flex max-w-[1920px] flex-col gap-12 px-6 py-10 sm:px-10 lg:min-h-[450px] lg:flex-row lg:items-center lg:justify-between lg:gap-8 lg:px-16 lg:py-12 xl:px-24 2xl:px-32">

        {{-- Kolom teks --}}
        <div class="lg:max-w-[54%]">
            <div class="mb-8 flex items-center gap-3 text-xl font-extrabold lg:mb-8">
                <span aria-hidden="true"
                      class="h-8 w-8 bg-[#1b4fd8] [clip-path:polygon(50%_0,100%_25%,100%_75%,50%_100%,0_75%,0_25%)]"></span>
                NeuroTrack
            </div>

            <h1 class="text-[2.5rem] font-extrabold leading-[1.05] tracking-tight sm:text-6xl lg:text-[52px] xl:text-[60px] 2xl:text-[66px]">
                Hadir, belajar,<br>
                <span class="text-[#1b4fd8] shadow-[inset_0_-0.18em_0_rgba(255,201,51,0.6)]">naik level.</span>
            </h1>

            <p class="mt-5 max-w-[620px] text-base leading-relaxed sm:text-xl lg:mt-5 lg:text-lg xl:text-xl">
                Absensi QR anti titip absen, radar skill hard &amp; soft skill, serta XP dan badge, semuanya dalam satu platform untuk SMK.
            </p>

            <ul class="mt-6 flex flex-wrap gap-3 text-sm font-semibold sm:text-base lg:text-[15px]">
                <li class="rounded-full border-2 border-[#c3cee3] bg-white px-4 py-2">Absensi QR</li>
                <li class="rounded-full border-2 border-[#c3cee3] bg-white px-4 py-2">Radar Skill</li>
                <li class="rounded-full border-2 border-[#c3cee3] bg-white px-4 py-2">XP &amp; Badge</li>
            </ul>

            <a href="{{ $href }}"
               class="mt-8 inline-flex h-14 w-full items-center justify-center rounded-2xl bg-[#0b1f4d] px-8 text-lg font-extrabold text-white transition hover:bg-[#1b4fd8] focus-visible:outline focus-visible:outline-4 focus-visible:outline-offset-4 focus-visible:outline-[#1b4fd8] sm:w-auto lg:absolute lg:bottom-10 lg:right-16 lg:mt-0 lg:h-12 xl:right-24 2xl:right-32">
                {{ $cta }}
            </a>
        </div>

        {{-- Radar chart + kartu XP --}}
        <div class="relative mx-auto w-full max-w-[300px] sm:max-w-[380px] lg:mx-0 lg:mr-28 lg:max-w-[300px] xl:max-w-[320px] 2xl:mr-32 2xl:max-w-[330px]">
            <svg viewBox="0 0 400 400" class="h-auto w-full" role="img" aria-label="Contoh radar chart skill siswa">
                <g fill="none" stroke="#c3cee3" stroke-width="2">
                    <polygon points="200,30 347.2,115 347.2,285 200,370 52.8,285 52.8,115"/>
                    <polygon points="200,88 297,144 297,256 200,312 103,256 103,144"/>
                    <polygon points="200,144 248.5,172 248.5,228 200,256 151.5,228 151.5,172"/>
                    <path d="M200 200V30M200 200L347.2 115M200 200L347.2 285M200 200V370M200 200L52.8 285M200 200L52.8 115"/>
                </g>
                <polygon points="200,47 303,140.5 325.1,272.3 200,302 82.2,268 60.1,119.3"
                         fill="rgba(25,211,197,0.28)" stroke="#0b1f4d" stroke-width="5" stroke-linejoin="round"/>
                <g fill="#0b1f4d">
                    <circle cx="200" cy="47" r="8"/><circle cx="303" cy="140.5" r="8"/>
                    <circle cx="325.1" cy="272.3" r="8"/><circle cx="200" cy="302" r="8"/>
                    <circle cx="82.2" cy="268" r="8"/><circle cx="60.1" cy="119.3" r="8"/>
                </g>
            </svg>

            <div class="absolute -right-3 top-2 rounded-2xl border-2 border-[#c3cee3] bg-white px-4 py-2 text-base font-extrabold sm:-right-12 sm:text-xl lg:-right-20 lg:top-6 lg:text-base">
                +120 XP
            </div>
            <div class="absolute -right-3 top-16 rounded-2xl border-2 border-[#c3cee3] bg-white px-4 py-2 text-base font-extrabold sm:-right-12 sm:top-20 sm:text-xl lg:-right-20 lg:top-[4.5rem] lg:text-base">
                Juara Kelas
            </div>
        </div>
    </div>
</section>

{{--
CATATAN PEMASANGAN
1) Font: tambahkan di <head> layout (atau pasang lewat npm: @fontsource/sora & @fontsource/inter):
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Sora:wght@700;800&display=swap" rel="stylesheet">
2) Tailwind v3.x: pastikan file ini ikut dipindai di tailwind.config.js ->
   content: ['./resources/**/*.blade.php', ...]
   (Tailwind v4 memindai otomatis.)
3) Pasang di halaman landing:
   <x-hero-banner href="{{ route('login') }}" />
--}}