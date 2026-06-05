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

    .nav-menu {
        display: flex;
        align-items: center;
        gap: 22px;
        color: var(--putih);
        font-weight: 600;
    }

    .nav-menu a {
        position: relative;
        padding: 6px 0;
        opacity: .92;
        transition: opacity .2s ease, transform .2s ease;
    }

    .nav-menu a::after {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 2px;
        border-radius: 999px;
        background: var(--putih);
        transform: scaleX(0);
        transform-origin: center;
        transition: transform .2s ease;
    }

    .nav-menu a:hover {
        opacity: 1;
        transform: translateY(-2px);
    }

    .nav-menu a:hover::after {
        transform: scaleX(1);
    }

    .nav-actions {
        display: flex;
        align-items: center;
        gap: 22px;
        margin-left: auto;
    }

    .nav .btn,
    .hero .btn,
    .slider-btn,
    .news-control {
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease, color .2s ease;
    }

    .nav .btn:hover,
    .hero .btn:hover,
    .news-control:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 12px 26px rgba(0, 71, 76, .22);
    }

    .slider-btn:hover {
        transform: translateY(-50%) scale(1.06);
        box-shadow: 0 12px 26px rgba(0, 71, 76, .22);
    }

    .hero {
        padding: 110px 7vw 50px;
        background: url('{{ asset('images/bg-utama.png') }}') center/cover no-repeat;
        min-height: 620px;
    }

    .hero-grid {
        display: grid;
        grid-template-columns: .95fr 1.15fr;
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
        font-size: 17px;
        color: #ffffff;
        max-width: 620px;
    }

    .hero-2 {
        font-size: 22px;
        color: #ffffff;
        max-width: 620px;
    }

    .slider {
        width: min(100%, 500px);
        aspect-ratio: 1 / 1;
        justify-self: end;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 26px 70px rgba(4, 120, 105, 0);
        background: rgba(255, 255, 255, 0);
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
        display: block;
    }

    .slide-link,
    .slide-frame {
        display: grid;
        width: 100%;
        height: 100%;
        place-items: center;
    }

    .slide-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
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

    .section-jenjang {
        padding-top: 24px;
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

    .jenjang-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
        max-width: 1080px;
        margin: 28px auto 0;
    }

    .jenjang-card {
        display: grid;
        justify-items: center;
        gap: 10px;
        padding: 16px 16px 24px;
        border: 1px solid var(--garis);
        border-radius: 10px;
        background: #ffffff;
        overflow: hidden;
        box-shadow: 0 14px 34px rgba(0, 71, 76, .08);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .jenjang-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 42px rgba(0, 71, 76, .13);
    }

    .jenjang-logo {
        display: grid;
        width: 100%;
        min-height: 150px;
        place-items: center;
        border: 1px dashed rgba(0, 121, 121, .36);
        border-radius: 10px;
        background: linear-gradient(135deg, rgba(0, 121, 121, .1), rgba(15, 190, 168, .16));
        color: var(--hijau);
        font-weight: 900;
        text-align: center;
    }

    .jenjang-logo-img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 10px;
        display: block;
    }

    .jenjang-card h3 {
        margin: 4px 0 0;
        color: var(--hijau);
        font-size: 16px;
    }

    .jenjang-card h4 {
        margin: 0;
        color: var(--gelap);
        font-size: 20px;
    }

    .jenjang-card p {
        max-width: 280px;
        margin: 0;
        color: #66827f;
    }

    .bantuan {
        padding: 54px 7vw;
        text-align: center;
    }

    .bantuan-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 28px;
        max-width: 1080px;
        margin: 0 auto;
    }

    .bantuan-item {
        display: grid;
        justify-items: center;
        gap: 10px;
        padding: 10px;
    }

    .bantuan-item img {
        width: 72px;
        height: 72px;
        object-fit: contain;
    }

    .bantuan-item h4 {
        margin: 8px 0 0;
        color: var(--gelap);
    }

    .bantuan-item p {
        margin: 0;
        max-width: 320px;
    }

    .map-frame {
        width: 100%;
        max-width: 340px;
        aspect-ratio: 4 / 1;
        overflow: hidden;
        border: 1px solid var(--garis);
        border-radius: 10px;
        box-shadow: 0 14px 34px rgba(0, 71, 76, .12);
    }

    .map-frame iframe {
        width: 100%;
        height: 100%;
        border: 0;
        display: block;
    }

    .map-link {
        color: var(--hijau);
        font-weight: 700;
    }

    .landing-footer {
        padding: 15px 7vw 15px;
        text-align: center;
        background: linear-gradient(135deg, #007979, #0fbea8);
    }

    .landing-footer p {
        margin: 0;
        color: #ffffff;
        font-size: 15px;
        line-height: 32px;
    }

    .landing-footer a {
        color: #ffffff;
        font-weight: 800;
    }

    .login-menu {
        position: relative;
        padding-bottom: 12px;
        margin-bottom: -12px;
    }

    .login-menu:hover .drop,
    .login-menu:focus-within .drop {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0);
    }

    .drop {
        position: absolute;
        right: 0;
        top: calc(100% + 6px);
        z-index: 10;
        background: white;
        border: 1px solid var(--garis);
        border-radius: 8px;
        box-shadow: 0 18px 45px rgba(0, 0, 0, .12);
        min-width: 170px;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(8px);
        transition: opacity .18s ease, transform .18s ease, visibility .18s ease;
    }

    .drop::before {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        top: -14px;
        height: 14px;
    }

    .drop a {
        display: block;
        padding: 12px 14px;
    }

    @media (max-width: 980px) {
        .jenjang-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 850px) {
        .nav {
            flex-wrap: wrap;
            gap: 14px;
        }

        .nav-menu {
            gap: 16px;
            font-size: 14px;
        }

        .nav-actions {
            width: 100%;
            justify-content: flex-end;
            gap: 14px;
        }

        .hero-grid {
            grid-template-columns: 1fr;
        }

        .slider {
            justify-self: center;
            width: min(100%, 520px);
        }

        .hero {
            padding-top: 96px;
        }

        .hero-copy {
            font-size: 14px;

        }

        .news-item {
            flex-basis: 100%;
        }

        .jenjang-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<nav class="nav" data-main-nav>
    <div style="color:var(--putih)">Yayasan Nurul Huda Munjuk</div>

    <div class="nav-actions">
        <div class="nav-menu">
            <a href="#pendaftaran">Pendaftaran</a>
            <a href="#berita">Berita</a>
            <a href="#kontak">Kontak</a>
        </div>

        <div class="login-menu">
            <button class="btn utama">Login</button>

            <div class="drop">
                <a href="{{ route('guru.login') }}">Sebagai Guru</a>
                <a href="{{ route('siswa.login') }}">Sebagai Siswa</a>
            </div>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="hero-grid">
        <div>
        <p class="hero-2">Selamat Datang di</p>    
        <h1>Yayasan Nurul Huda Munjuk</h1>
            <p class="hero-copy">Portal utama pondok terpadu 
                <br>untuk mempermudah akses informasi dan akademik.</p>
            <a class="btn utama" href="#berita">Lihat Berita Seputar Kami</a>
        </div>

        <div class="slider" data-slider>
            <div class="slider-track" data-slider-track>
                @forelse($slider as $item)
                    <div class="slide">
                        @if($item->link)
                            <a class="slide-link" href="{{ $item->link }}">
                                @if($item->gambar)
                                    <img class="slide-image" src="{{ asset($item->gambar) }}" alt="Slider Yayasan Nurul Huda Munjuk">
                                @endif
                            </a>
                        @else
                            <div class="slide-frame">
                                @if($item->gambar)
                                    <img class="slide-image" src="{{ asset($item->gambar) }}" alt="Slider Yayasan Nurul Huda Munjuk">
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="slide"></div>
                @endforelse
            </div>

            <button class="slider-btn prev" type="button" data-slider-prev aria-label="Slide sebelumnya">&lsaquo;</button>
            <button class="slider-btn next" type="button" data-slider-next aria-label="Slide berikutnya">&rsaquo;</button>
            <div class="slider-dots" data-slider-dots></div>
        </div>
    </div>
</section>

<section class="section" id="berita">
    <h2>Berita Seputar Yayasan</h2>

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

<section class="section section-jenjang" id="jenjang">
    <h2>Jenjang Pendidikan</h2>

    <div class="jenjang-grid">
        <div class="jenjang-card">
            <img class="jenjang-logo-img" src="{{ asset('images/medsos.svg') }}" alt="Logo PAUD Nurul Huda">
            <h3>PAUD</h3>
            <h4>PAUD Nurul Huda</h4>
            <p>Pendidikan usia dini dengan suasana belajar yang aman, ceria, dan islami.</p>
        </div>

        <div class="jenjang-card">
            {{-- Ganti placeholder ini dengan: <img class="jenjang-logo-img" src="{{ asset('images/logo-tk.png') }}" alt="Logo TK Nurul Huda"> --}}
            <div class="jenjang-logo">Gambar Jenjang</div>
            <h3>TK</h3>
            <h4>TK Nurul Huda</h4>
            <p>Membentuk dasar karakter, kemandirian, dan kesiapan anak memasuki sekolah dasar.</p>
        </div>

        <div class="jenjang-card">
            {{-- Ganti placeholder ini dengan: <img class="jenjang-logo-img" src="{{ asset('images/logo-mi.png') }}" alt="Logo MI Nurul Huda"> --}}
            <div class="jenjang-logo">Gambar Jenjang</div>
            <h3>MI</h3>
            <h4>MI Nurul Huda</h4>
            <p>Pendidikan dasar yang memadukan ilmu umum, akhlak, dan pembiasaan ibadah.</p>
        </div>

        <div class="jenjang-card">
            {{-- Ganti placeholder ini dengan: <img class="jenjang-logo-img" src="{{ asset('images/logo-smp.png') }}" alt="Logo SMP Nurul Huda"> --}}
            <div class="jenjang-logo">Gambar Jenjang</div>
            <h3>SMP</h3>
            <h4>SMP Nurul Huda</h4>
            <p>Menguatkan kemampuan akademik, kedisiplinan, dan karakter santri remaja.</p>
        </div>

        <div class="jenjang-card">
            {{-- Ganti placeholder ini dengan: <img class="jenjang-logo-img" src="{{ asset('images/logo-ma.png') }}" alt="Logo MA Nurul Huda"> --}}
            <div class="jenjang-logo">Gambar Jenjang</div>
            <h3>MA</h3>
            <h4>MA Nurul Huda</h4>
            <p>Mempersiapkan peserta didik melanjutkan pendidikan tinggi dan berperan di masyarakat.</p>
        </div>

        <div class="jenjang-card">
            {{-- Ganti placeholder ini dengan: <img class="jenjang-logo-img" src="{{ asset('images/logo-ponpes.png') }}" alt="Logo Pondok Pesantren Nurul Huda"> --}}
            <div class="jenjang-logo">Gambar Jenjang</div>
            <h3>Ponpes</h3>
            <h4>Pondok Pesantren Nurul Huda</h4>
            <p>Pembinaan keagamaan, adab, dan kehidupan santri dalam lingkungan pesantren.</p>
        </div>
    </div>
</section>

<section class="section" id="pendaftaran" style="background:var(--muda)">
    <h2>Informasi Yayasan</h2>

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

<section class="bantuan" id="kontak">
    <div class="bantuan-grid">
        <div class="bantuan-item">
            <img src="{{ asset ('images/telepon.svg') }}" alt="Kontak Pengurus" >
            <h4>Kontak Pengurus</h4>
            <p>Cara paling cepat dan efektif untuk mendapatkan informasi.</p>
            <p>
            <b>Telepon:</b><br>
                <a href="tel:+6285378743857" title="Ketuk untuk menelepon 0853 7874 3857">
                    0853 7874 3857
                </a>
            </p>
            <p>
            <b>Whatsapp:</b><br>
                <a href="https://wa.me/6285378743857" title="Hubungi Whatsapp 0853 7874 3857">
                    0853 7874 3857
                </a>
            </p>
        </div>

        <div class="bantuan-item">
            <img src="{{ asset ('images/medsos.svg') }}" alt="Informasi dan Kontak">
            <h4>Sosial Media</h4>
            <p>Ikuti kami di media sosial untuk informasi terbaru.</p>
            <p>
                <b>Instagram</b><br>
                <a href="https://www.instagram.com/ppnurulhuda/" target="_blank" title="Kunjungi Instagram PP Nurul Huda">
                    ppnurulhuda
                </a>
            </p>
            <p>
                <b>Facebook:</b><br>
                <a href="https://www.facebook.com/ppnurulhuda/" target="_blank" title="Kunjungi Facebook PP Nurul Huda">
                    ppnurulhuda
                </a>
            </p>
        </div>

        <div class="bantuan-item">
            <img src="{{ asset ('images/alamat.svg') }}" alt="Alamat">
            <h4>Alamat</h4>
            <p>Jl. Lintas Pantai Timur Sumatera, Dusun Munjuk, Desa Labuhan Maringgai, Kec. Labuhan Maringgai, Lampung Timur</p>
            <div class="map-frame">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d557.3828191851405!2d105.79083697865478!3d-5.329083475650464!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e40594d9ab79577%3A0x457c5cf87479166a!2sPondok%20Pesantren%20Nurul%20Huda!5e0!3m2!1sid!2sid!4v1780667032683!5m2!1sid!2sid"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Peta lokasi Pondok Pesantren Nurul Huda Munjuk"
                ></iframe>
            </div>
            <a class="map-link" href="https://maps.app.goo.gl/kTYGw8LsVemhpzKZA" target="_blank" rel="noopener">
                Buka Google Maps
            </a>
        </div>
    </div>
</section>

<footer class="landing-footer">
    <p>
        Pondok Pesantren <a href="{{ route('beranda') }}">Nurul Huda Munjuk</a>
    </p>
</footer>

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
