@extends('layouts.app')
@section('body')
<style>
    .nav{position:fixed;z-index:5;left:0;right:0;top:0;padding:18px 7vw;display:flex;justify-content:space-between;align-items:center;background:rgba(255,255,255,.82);backdrop-filter:blur(12px);border-bottom:1px solid var(--garis)}
    .hero{padding:110px 7vw 50px;background:linear-gradient(135deg,#ddfff7,#fff);min-height:620px}.hero-grid{display:grid;grid-template-columns:1.15fr .85fr;gap:28px;align-items:center}.hero h1{font-size:clamp(34px,5vw,62px);line-height:1.05;margin:0;color:var(--gelap)}.slider{height:390px;border-radius:8px;overflow:hidden;box-shadow:0 26px 70px rgba(4,120,105,.18);background:linear-gradient(135deg,var(--toska),var(--hijau));position:relative}.slide{height:100%;display:grid;place-items:end start;padding:34px;color:white;background-size:cover;background-position:center}.slide:before{content:"";position:absolute;inset:0;background:linear-gradient(0deg,rgba(0,0,0,.42),transparent)}.slide div{position:relative}.section{padding:54px 7vw}.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px}.news-img{width:100%;height:180px;object-fit:cover;border-radius:8px;background:var(--muda)}.login-menu{position:relative}.login-menu:hover .drop{display:block}.drop{display:none;position:absolute;right:0;top:42px;background:white;border:1px solid var(--garis);border-radius:8px;box-shadow:0 18px 45px rgba(0,0,0,.12);min-width:170px}.drop a{display:block;padding:12px 14px}
    @media(max-width:850px){.hero-grid{grid-template-columns:1fr}.nav{position:static}.hero{padding-top:32px}}
</style>
<nav class="nav">
    <strong style="color:var(--hijau)">Yayasan Nurul Huda Munjuk</strong>
    <div class="login-menu">
        <button class="btn">Login</button>
        <div class="drop"><a href="{{ route('guru.login') }}">Login Guru</a><a href="{{ route('siswa.login') }}">Login Siswa</a></div>
    </div>
</nav>
<section class="hero">
    <div class="hero-grid">
        <div>
            <h1>Yayasan Nurul Huda Munjuk</h1>
            <p style="font-size:19px;color:#41615e;max-width:620px">Sistem informasi akademik sekolah untuk mengelola berita, informasi, data guru, data siswa, nilai, raport, dan tagihan dalam satu web modern.</p>
            <a class="btn" href="#berita">Lihat Berita Sekolah</a>
        </div>
        <div class="slider">
            @forelse($slider as $item)
                @if($loop->first)
                <div class="slide" style="{{ $item->gambar ? 'background-image:url('.asset($item->gambar).')' : '' }}">
                    <div><h2>{{ $item->judul }}</h2><p>{{ $item->deskripsi }}</p></div>
                </div>
                @endif
            @empty
                <div class="slide"><div><h2>Selamat Datang</h2><p>Slider dapat diperbarui oleh admin.</p></div></div>
            @endforelse
        </div>
    </div>
</section>
<section class="section" id="berita">
    <h2>Berita Seputar Sekolah</h2>
    <div class="cards">
        @forelse($berita as $item)
        <article class="card">
            @if($item->foto_kegiatan)<img class="news-img" src="{{ asset($item->foto_kegiatan) }}" alt="{{ $item->judul }}">@endif
            <h3>{{ $item->judul }}</h3><p class="muted">{{ $item->tanggal_berita }}</p><p>{{ \Illuminate\Support\Str::limit($item->isi, 150) }}</p>
        </article>
        @empty <div class="card">Belum ada berita.</div> @endforelse
    </div>
</section>
<section class="section" style="background:var(--muda)">
    <h2>Informasi Sekolah</h2>
    <div class="cards">@forelse($informasi as $item)<div class="card"><h3>{{ $item->judul }}</h3><p>{{ $item->isi }}</p><strong>{{ $item->kontak }}</strong></div>@empty <div class="card">Informasi akan tampil setelah diisi admin.</div>@endforelse</div>
</section>
<section class="section">
    <h2>Kontak</h2>
    <div class="card"><p>Munjuk, Lombok Timur</p><p>Email: info@nurulhudamunjuk.sch.id</p></div>
</section>
@endsection
