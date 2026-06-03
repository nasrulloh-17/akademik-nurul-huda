@extends('layouts.app')

@section('body')
<style>
    .nav {
        position: fixed;
        z-index: 5;
        left: 0;
        right: 0;
        top: 0;
        padding: 18px 7vw;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: transparent;
        border-bottom: 1px solid transparent;
        transition: background .25s ease, border-color .25s ease, box-shadow .25s ease;
    }

    .nav.scrolled {
        background: linear-gradient(135deg, #007979, #0fbea8);
        border-bottom-color: rgba(255, 255, 255, .18);
        box-shadow: 0 12px 30px rgba(0, 71, 76, .2);
    }

    .hero {
        padding: 110px 7vw 50px;
        background: url('{{ asset('images/bg-utama.png') }}') center/cover no-repeat;
        min-height: 620px;
    }

    .hero-grid {
        display: grid;
        grid-template-columns: 1.15fr .85fr;
        gap: 28px;
        align-items: center;
    }

    .hero h1 {
        font-size: clamp(34px, 5vw, 62px);
        line-height: 1.05;
        margin: 0;
        color: var(--putih);
    }

    .hero-copy {
        font-size: 19px;
        color: #ffffff;
        max-width: 620px;
    }

    .slider {
        height: 390px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 26px 70px rgba(4, 120, 105, .18);
        background: linear-gradient(135deg, var(--toska), var(--hijau));
        position: relative;
    }

    .slider-track {
        display: flex;
        height: 100%;
        transition: transform .55s ease;
    }

    .slide {
        min-width: 100%;
        height: 100%;
        display: grid;
        place-items: end start;
        padding: 34px;
        color: white;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .slide:before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(0deg, rgba(0, 0, 0, .52), rgba(0, 0, 0, .05));
    }

    .slide div {
        position: relative;
        max-width: 85%;
    }

    .slider-btn {
        position: absolute;
        top: 50%;
        z-index: 2;
        transform: translateY(-50%);
        width: 42px;
        height: 42px;
        border: 0;
        border-radius: 999px;
        background: rgba(255, 255, 255, .9);
        color: var(--hijau);
        font-size: 24px;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .16);
    }

    .slider-btn:hover {
        background: white;
    }

    .slider-btn.prev {
        left: 14px;
    }

    .slider-btn.next {
        right: 14px;
    }

    .slider-dots {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 16px;
        z-index: 2;
        display: flex;
        justify-content: center;
        gap: 8px;
    }

    .slider-dot {
        width: 10px;
        height: 10px;
        border: 0;
        border-radius: 999px;
        background: rgba(255, 255, 255, .52);
        cursor: pointer;
    }

    .slider-dot.active {
        width: 26px;
        background: white;
    }

    .section {
        padding: 54px 7vw;
        text-align: center;
    }

    .cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 18px;
    }

    .news-carousel {
        position: relative;
        max-width: 980px;
        margin: 0 auto;
    }

    .news-window {
        overflow: hidden;
    }

    .news-track {
        display: flex;
        gap: 18px;
        transition: transform .45s ease;
    }

    .clean-item {
        padding: 8px 12px;
    }

    .news-item {
        flex: 0 0 calc((100% - 18px) / 2);
    }

    .news-link {
        display: block;
        color: inherit;
        transition: color .2s ease, transform .2s ease;
    }

    .news-link:hover {
        color: var(--hijau);
        transform: translateY(-3px);
    }

    .clean-item p {
        margin-left: auto;
        margin-right: auto;
        max-width: 620px;
    }

    .news-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 8px;
        background: var(--muda);
    }

    .news-controls {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 20px;
    }

    .news-control {
        width: 42px;
        height: 42px;
        border: 0;
        border-radius: 999px;
        background: linear-gradient(135deg, #007979, #0fbea8);
        color: white;
        font-size: 22px;
        font-weight: 800;
        cursor: pointer;
    }

    .news-control:disabled {
        opacity: .45;
        cursor: not-allowed;
    }

    .login-menu {
        position: relative;
    }

    .login-menu:hover .drop {
        display: block;
    }

    .drop {
        display: none;
        position: absolute;
        right: 0;
        top: 42px;
        background: white;
        border: 1px solid var(--garis);
        border-radius: 8px;
        box-shadow: 0 18px 45px rgba(0, 0, 0, .12);
        min-width: 170px;
    }

    .drop a {
        display: block;
        padding: 12px 14px;
    }

    @media (max-width: 850px) {
        .hero-grid {
            grid-template-columns: 1fr;
        }

        .hero {
            padding-top: 96px;
        }

        .news-item {
            flex-basis: 100%;
        }
    }
</style>

<nav class="nav" data-main-nav>
    <div style="color:var(--putih)">Yayasan Nurul Huda Munjuk</div>

    <div class="login-menu">
        <button class="btn utama">Login</button>

        <div class="drop">
            <a href="{{ route('guru.login') }}">Login Guru</a>
            <a href="{{ route('siswa.login') }}">Login Siswa</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="hero-grid">
        <div>
        <p class="hero-copy">Selamat Datang di</p>    
        <h1>Yayasan Nurul Huda Munjuk</h1>
            <p class="hero-copy">Portal utama pondok terpadu 
                <br>untuk mempermudah akses informasi dan akademik
                <br>Menuju digitalisasi pondok pesatren.</p>
            <a class="btn utama" href="#berita">Lihat Berita Sekolah</a>
        </div>

        <div class="slider" data-slider>
            <div class="slider-track" data-slider-track>
                @forelse($slider as $item)
                    <div class="slide" style="{{ $item->gambar ? 'background-image:url('.asset($item->gambar).')' : '' }}">
                        <div>
                            <h2>{{ $item->judul }}</h2>
                            <p>{{ $item->deskripsi }}</p>
                        </div>
                    </div>
                @empty
                    <div class="slide">
                        <div>
                            <h2>Selamat Datang</h2>
                            <p>Slider dapat diperbarui oleh admin.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <button class="slider-btn prev" type="button" data-slider-prev aria-label="Slide sebelumnya">&lsaquo;</button>
            <button class="slider-btn next" type="button" data-slider-next aria-label="Slide berikutnya">&rsaquo;</button>
            <div class="slider-dots" data-slider-dots></div>
        </div>
    </div>
</section>

<section class="section" id="berita">
    <h2>Berita Seputar Sekolah</h2>

    <div class="news-carousel" data-news-carousel>
        <div class="news-window">
            <div class="news-track" data-news-track>
                @forelse($berita as $item)
                    <article class="clean-item news-item">
                        <a class="news-link" href="{{ route('berita.detail', $item->id) }}">
                            @if($item->foto_kegiatan)
                                <img class="news-img" src="{{ asset($item->foto_kegiatan) }}" alt="{{ $item->judul }}">
                            @endif

                            <h3>{{ $item->judul }}</h3>
                            <p class="muted">{{ $item->tanggal_berita }}</p>
                            <p>{{ \Illuminate\Support\Str::limit($item->isi, 150) }}</p>
                            <strong>Baca selengkapnya</strong>
                        </a>
                    </article>
                @empty
                    <div class="clean-item news-item">Belum ada berita.</div>
                @endforelse
            </div>
        </div>

        @if($berita->count() > 2)
            <div class="news-controls">
                <button class="news-control" type="button" data-news-prev aria-label="Berita sebelumnya">&lsaquo;</button>
                <button class="news-control" type="button" data-news-next aria-label="Berita berikutnya">&rsaquo;</button>
            </div>
        @endif
    </div>
</section>

<section class="section" style="background:var(--muda)">
    <h2>Informasi Sekolah</h2>

    <div class="cards">
        @forelse($informasi as $item)
            <div class="clean-item">
                <h3>{{ $item->judul }}</h3>
                <p>{{ $item->isi }}</p>
                <strong>{{ $item->kontak }}</strong>
            </div>
        @empty
            <div class="clean-item">Informasi akan tampil setelah diisi admin.</div>
        @endforelse
    </div>
</section>

<section class="section">
    <h2>Kontak</h2>

    <div class="clean-item">
        <p>Munjuk, Labuhan Maringgai</p>
        <p>Email: admin@ppnurulhuda.or.id</p>
    </div>
</section>

<script>
    const mainNav = document.querySelector('[data-main-nav]');

    if (mainNav) {
        const updateNavbar = () => {
            mainNav.classList.toggle('scrolled', window.scrollY > 40);
        };

        updateNavbar();
        window.addEventListener('scroll', updateNavbar, { passive: true });
    }

    document.querySelectorAll('[data-slider]').forEach((slider) => {
        const track = slider.querySelector('[data-slider-track]');
        const slides = Array.from(track.children);
        const dotsWrap = slider.querySelector('[data-slider-dots]');
        let index = 0;
        let timer = null;

        const showSlide = (nextIndex) => {
            index = (nextIndex + slides.length) % slides.length;
            track.style.transform = `translateX(-${index * 100}%)`;
            dotsWrap.querySelectorAll('.slider-dot').forEach((dot, dotIndex) => {
                dot.classList.toggle('active', dotIndex === index);
            });
        };

        const startAutoSlide = () => {
            clearInterval(timer);
            timer = setInterval(() => showSlide(index + 1), 4500);
        };

        slides.forEach((_, slideIndex) => {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'slider-dot';
            dot.setAttribute('aria-label', `Tampilkan slide ${slideIndex + 1}`);
            dot.addEventListener('click', () => {
                showSlide(slideIndex);
                startAutoSlide();
            });
            dotsWrap.appendChild(dot);
        });

        slider.querySelector('[data-slider-prev]').addEventListener('click', () => {
            showSlide(index - 1);
            startAutoSlide();
        });

        slider.querySelector('[data-slider-next]').addEventListener('click', () => {
            showSlide(index + 1);
            startAutoSlide();
        });

        if (slides.length <= 1) {
            slider.querySelectorAll('.slider-btn, .slider-dots').forEach((control) => control.style.display = 'none');
            return;
        }

        showSlide(0);
        startAutoSlide();
    });

    document.querySelectorAll('[data-news-carousel]').forEach((carousel) => {
        const track = carousel.querySelector('[data-news-track]');
        const items = Array.from(track.children);
        const prev = carousel.querySelector('[data-news-prev]');
        const next = carousel.querySelector('[data-news-next]');
        let index = 0;

        if (! prev || ! next || items.length <= 2) {
            return;
        }

        const visibleItems = () => window.matchMedia('(max-width: 850px)').matches ? 1 : 2;

        const updateNews = () => {
            const itemWidth = items[0].getBoundingClientRect().width;
            const gap = parseFloat(getComputedStyle(track).gap) || 0;
            const maxIndex = Math.max(0, items.length - visibleItems());

            index = Math.min(index, maxIndex);
            track.style.transform = `translateX(-${index * (itemWidth + gap)}px)`;
            prev.disabled = index === 0;
            next.disabled = index === maxIndex;
        };

        prev.addEventListener('click', () => {
            index -= 1;
            updateNews();
        });

        next.addEventListener('click', () => {
            index += 1;
            updateNews();
        });

        window.addEventListener('resize', updateNews);
        updateNews();
    });
</script>
@endsection
