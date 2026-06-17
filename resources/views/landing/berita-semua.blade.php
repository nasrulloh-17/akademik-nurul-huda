@extends('layouts.app')

@section('judul', 'Semua Berita Yayasan Nurul Huda Munjuk')

@section('body')
<style>
    .news-nav {
        padding: 18px 7vw;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        background: linear-gradient(135deg, #007979, #0fbea8);
        color: white;
    }

    .news-page {
        padding: 34px 7vw 48px;
    }

    .news-heading {
        max-width: 920px;
        margin: 0 auto 22px;
        text-align: center;
    }

    .news-heading h1 {
        margin: 0 0 8px;
        color: var(--gelap);
        font-size: clamp(30px, 4vw, 46px);
        line-height: 1.12;
    }

    .all-news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
        max-width: 1120px;
        margin: 0 auto;
    }

    .all-news-card {
        overflow: hidden;
        border: 1px solid var(--garis);
        border-radius: 10px;
        background: white;
        box-shadow: 0 14px 34px rgba(0, 71, 76, .08);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .all-news-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 40px rgba(0, 71, 76, .13);
    }

    .all-news-img {
        width: 100%;
        height: 170px;
        object-fit: cover;
        display: block;
        background: var(--muda);
    }

    .all-news-body {
        padding: 12px 14px 14px;
    }

    .all-news-body h2 {
        margin: 0 0 6px;
        color: var(--gelap);
        font-size: 18px;
        line-height: 1.28;
    }

    .all-news-body p {
        margin: 0 0 8px;
        color: #66827f;
        line-height: 1.45;
    }

    .pagination-wrap {
        display: flex;
        justify-content: center;
        gap: 10px;
        max-width: 1120px;
        margin: 24px auto 0;
        flex-wrap: wrap;
    }

    .pagination-status {
        width: 100%;
        margin: 0 0 4px;
        color: #66827f;
        font-size: 13px;
        text-align: center;
    }

    @media (max-width: 700px) {
        .news-nav {
            align-items: flex-start;
            flex-direction: column;
            padding: 14px 5vw;
        }

        .news-page {
            padding: 24px 5vw 36px;
        }

        .all-news-grid {
            gap: 12px;
        }
    }
</style>

<nav class="news-nav">
    <strong>Yayasan Nurul Huda Munjuk</strong>
    <a class="btn alt" href="{{ route('beranda') }}#berita">Kembali ke Halaman Utama</a>
</nav>

<main class="news-page">
    <header class="news-heading">
        <h1>Semua Berita</h1>
        <p class="muted">Kumpulan berita dan kegiatan Yayasan Nurul Huda Munjuk.</p>
    </header>

    @if($berita->count())
        <div class="all-news-grid">
            @foreach($berita as $item)
                <article class="all-news-card">
                    <a href="{{ route('berita.detail', $item->id) }}">
                        @if($item->foto_kegiatan)
                            <img class="all-news-img" src="{{ asset($item->foto_kegiatan) }}" alt="{{ $item->judul }}">
                        @endif

                        <div class="all-news-body">
                            <p class="muted">{{ $item->tanggal_berita }}</p>
                            <h2>{{ $item->judul }}</h2>
                            <p>{{ \Illuminate\Support\Str::limit($item->isi, 120) }}</p>
                            <strong>Baca selengkapnya</strong>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>

        @if($berita->hasPages())
            <div class="pagination-wrap">
                <p class="pagination-status">
                    Halaman {{ $berita->currentPage() }} dari {{ $berita->lastPage() }}
                </p>

                @if($berita->onFirstPage())
                    <span class="btn alt" style="opacity:.45;pointer-events:none">Sebelumnya</span>
                @else
                    <a class="btn alt" href="{{ $berita->previousPageUrl() }}">Sebelumnya</a>
                @endif

                @if($berita->hasMorePages())
                    <a class="btn utama" href="{{ $berita->nextPageUrl() }}">Berikutnya</a>
                @else
                    <span class="btn utama" style="opacity:.45;pointer-events:none">Berikutnya</span>
                @endif
            </div>
        @endif
    @else
        <div class="news-heading">
            <p>Belum ada berita yang ditambahkan.</p>
        </div>
    @endif
</main>
@endsection
