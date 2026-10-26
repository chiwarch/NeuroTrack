<section class="section" id="mapel">
    <div class="container">
        <div class="section__head">
            <h2>Mata Pelajaran di <span class="grad-text">NeuroTrack</span></h2>
            <p>Mapel umum untuk semua siswa, dan mapel kejuruan sesuai jurusanmu.</p>
        </div>

        <div class="mapel">
            @foreach ($subjects as $s)
                <div class="mapel__item">
                    <img src="{{ $s->iconUrl() }}" alt="" width="120" height="120" loading="lazy">
                    <strong>{{ $s->name }}</strong>
                    <small>{{ $s->typeLabel() }}{{ $s->major ? ' · '.$s->major->code : '' }}</small>
                </div>
            @endforeach
        </div>

        <aside class="cta">
            <img class="cta__girl" src="{{ asset('images/cta-girl.webp') }}" alt="" width="300" height="300" loading="lazy">
            <div class="cta__copy">
                <h2>Siap Memulai Perjalanan Belajarmu?</h2>
                <p>Masuk dengan akun dari sekolah dan mulai kerjakan kuis pertamamu.</p>
                <div class="cta__btns">
                    <a href="{{ route('login') }}" class="btn btn--white btn--md">Masuk Sekarang <x-icon name="arrow-right" :size="16" /></a>
                    <a href="#kuis" class="btn btn--glass btn--md"><x-icon name="play" :size="12" /> Lihat Kuis</a>
                </div>
            </div>
            <div class="bubble bubble--glass">Masa depan lebih cerah menantimu!</div>
        </aside>
    </div>
</section>
