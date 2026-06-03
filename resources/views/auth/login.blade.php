@extends('layouts.app')

@section('judul', $judul)

@section('body')
<div style="min-height:100vh;display:grid;place-items:center;background:linear-gradient(135deg,#e8fffb,#f7fffd)">
    <form
        method="post"
        class="card"
        style="width:min(430px,92vw)"
        action="{{ $jenis === 'admin' ? route('admin.login.proses') : ($jenis === 'guru' ? route('guru.login.proses') : route('siswa.login.proses')) }}"
    >
        @csrf

        <h1 style="margin-top:0;color:var(--hijau)">{{ $judul }}</h1>
        <p class="muted">Yayasan Nurul Huda Munjuk</p>

        @if($errors->any())
            <div class="alert">{{ $errors->first() }}</div>
        @endif

        <label>{{ $label }}</label>
        <input name="identitas" value="{{ old('identitas') }}" required autofocus>

        <div style="height:12px"></div>

        <label>Password</label>
        <input type="password" name="kata_sandi" required>

        <div style="display:flex;gap:10px;margin-top:18px">
            <button class="btn" style="flex:1">Masuk</button>
            <a class="btn alt" href="{{ route('beranda') }}">Beranda</a>
        </div>
    </form>
</div>
@endsection
