<section class="section" id="kuis">
    <div class="container">
        <div class="section__head section__head--row">
            <div>
                <h2>Katalog Kuis Terbaru</h2>
                <p>Kuis disusun oleh guru untuk mata pelajaran umum dan kejuruan. Masuk untuk mulai mengerjakan.</p>
            </div>
            <a href="{{ route('login') }}" class="btn btn--outline btn--sm">Lihat Semua Kuis <x-icon name="arrow-right" :size="14" /></a>
        </div>

        @if ($quizzes->isEmpty())
            <p class="muted" style="text-align:center">Belum ada kuis yang diterbitkan.</p>
        @else
            <div class="courses">
                @foreach ($quizzes as $q)
                    @php $theme = ['blue', 'green', 'orange', 'purple'][$loop->index % 4]; @endphp
                    <article class="course">
                        <div class="course__thumb theme-{{ $theme }}">
                            <img src="{{ $q->subject->iconUrl() }}" alt="" loading="lazy" width="200" height="200">
                        </div>
                        <span class="tag tag--{{ $theme }}">{{ $q->subject->name }}</span>
                        <h3>{{ $q->title }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit($q->description ?: 'Uji pemahamanmu lewat kuis ini.', 70) }}</p>
                        <div class="course__meta">
                            <span class="muted"><x-icon name="check-square" :size="14" /> {{ $q->questions_count }} soal</span>
                            <span class="muted"><x-icon name="clock" :size="14" /> {{ $q->duration_minutes }} mnt</span>
                            <a href="{{ route('login') }}" class="round-btn" aria-label="Kerjakan {{ $q->title }}"><x-icon name="arrow-right" :size="16" /></a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
