@extends('layouts.app')

@section('judul', $berita->judul)

@section('body')
<style>
    .detail-nav {
        padding: 18px 7vw;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, #007979, #0fbea8);
        color: white;
    }

    .detail-page {
        padding: 54px 7vw;
    }

    .detail-wrap {
        max-width: 920px;
        margin: 0 auto;
    }

    .detail-img {
        width: 100%;
        max-height: 430px;
        object-fit: cover;
        border-radius: 8px;
        margin: 22px 0;
        background: var(--muda);
    }

    .detail-title {
        margin: 18px 0 8px;
        color: var(--gelap);
        font-size: clamp(30px, 4vw, 48px);
        line-height: 1.15;
    }

    .detail-content {
        font-size: 18px;
        line-height: 1.8;
        white-space: pre-line;
    }

    .related {
        margin-top: 48px;
        padding-top: 28px;
        border-top: 1px solid var(--garis);
    }

    .related-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px;
    }

    .related-link {
        display: block;
        padding: 14px 0;
        color: var(--gelap);
    }

    .related-link:hover {
        color: var(--hijau);
    }

    @media (max-width: 700px) {
        .detail-nav {
            display: block;
        }

        .detail-nav .btn {
            margin-top: 12px;
        }
    }
</style>

<nav class="detail-nav">
    <strong>Yayasan Nurul Huda Munjuk</strong>
    <a class="btn alt" href="{{ route('beranda') }}#berita">Kembali ke Berita</a>
</nav>

<main class="detail-page">
    <article class="detail-wrap">
        <p class="muted">{{ $berita->tanggal_berita }}</p>
        <h1 class="detail-title">{{ $berita->judul }}</h1>

        @if($berita->foto_kegiatan)
            <img class="detail-img" src="{{ asset($berita->foto_kegiatan) }}" alt="{{ $berita->judul }}">
        @endif

        <div class="detail-content">{{ $berita->isi }}</div>

        @if($beritaLainnya->isNotEmpty())
            <section class="related">
                <h2>Berita Lainnya</h2>

                <div class="related-list">
                    @foreach($beritaLainnya as $item)
                        <a class="related-link" href="{{ route('berita.detail', $item->id) }}">
                            <strong>{{ $item->judul }}</strong>
                            <p class="muted">{{ $item->tanggal_berita }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </article>
</main>
@endsection
