<section class="section section--tint" id="progres">
    <div class="container progress">
        <div class="dash" aria-hidden="true">
            <aside class="dash__side">
                <div class="dash__logo"><img src="{{ asset('images/grad-cap.webp') }}" alt="" width="22" height="22"> Neuro<b>Track</b></div>
                @foreach ([['home','Beranda'],['book','Katalog Kuis'],['bar-chart','Progres'],['check-square','Kuis'],['award','Sertifikat'],['settings','Pengaturan']] as [$ic, $label])
                    <span class="dash__item {{ $label === 'Progres' ? 'is-active' : '' }}"><x-icon :name="$ic" :size="14" /> {{ $label }}</span>
                @endforeach
            </aside>
            <div class="dash__main">
                <h4>Progres Belajar</h4>
                <div class="dash__tabs"><span class="is-active">Semua Kuis</span><span>Sedang Berjalan</span><span>Selesai</span></div>

                <div class="dash__row">
                    <div class="ring" style="--p:75"><span>75%</span></div>
                    <div class="dash__text">
                        <strong>Progres Keseluruhan</strong>
                        <small>Kamu luar biasa! Terus lanjutkan perjalanan belajarmu.</small>
                    </div>
                    <ul class="dash__stats">
                        <li><span class="mini tone-green"><x-icon name="book" :size="16" /></span><b>3</b><small>Kuis Lulus</small></li>
                        <li><span class="mini tone-blue"><x-icon name="clock" :size="16" /></span><b>12</b><small>Jam Belajar</small></li>
                        <li><span class="mini tone-orange"><x-icon name="flame" :size="16" /></span><b>7</b><small>Hari Berturut-turut</small></li>
                    </ul>
                </div>

                <strong class="dash__sub">Kuis yang Sedang Dikerjakan</strong>
                <div class="dash__course">
                    <span class="code-badge code-badge--sm">&lt;/&gt;</span>
                    <div><b>Dasar Algoritma</b><div class="bar"><i style="width:75%"></i></div></div>
                    <small>75%</small>
                    <span class="btn btn--primary btn--xs">Lanjutkan</span>
                </div>
            </div>
        </div>

        <div class="progress__copy">
            <span class="pill pill--blue">Pantau Setiap Langkahmu</span>
            <h2>Lihat Progres, Capai <span class="grad-text">Tujuanmu</span></h2>
            <p>Dengan dashboard yang intuitif, kamu bisa memantau perkembangan belajar, melihat nilai terbaik tiap kuis, dan menjaga semangat belajar setiap hari.</p>
            <ul class="points">
                @foreach ($progressPoints as $p)
                    <li><span class="sq tone-{{ $p['tone'] }}"><x-icon :name="$p['icon']" :size="16" :fill="$p['icon'] === 'star'" /></span>{{ $p['text'] }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
