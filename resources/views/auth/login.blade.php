@extends('layouts.app')

@section('judul', $judul)

@section('body')
<style>
    .login-page {
        min-height: 100vh;
        display: grid;
        place-items: center;
        padding: 36px 7vw;
        background:
            linear-gradient(135deg, rgba(0, 121, 121, .88), rgba(15, 190, 168, .74)),
            url('{{ asset('images/bg-utama.png') }}') center/cover no-repeat;
    }

    .login-shell {
        width: min(980px, 100%);
        display: grid;
        grid-template-columns: 1fr 420px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, .22);
        border-radius: 8px;
        background: rgba(255, 255, 255, .13);
        box-shadow: 0 30px 80px rgba(0, 71, 76, .28);
        backdrop-filter: blur(14px);
    }

    .login-info {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 42px;
        padding: 46px;
        color: white;
    }

    .login-brand {
        font-size: 15px;
        font-weight: 700;
        letter-spacing: .3px;
    }

    .login-info h1 {
        max-width: 540px;
        margin: 0 0 16px;
        font-size: clamp(34px, 5vw, 54px);
        line-height: 1.05;
    }

    .login-info p {
        max-width: 520px;
        margin: 0;
        color: rgba(255, 255, 255, .86);
        font-size: 17px;
        line-height: 1.7;
    }

    .login-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .login-badge {
        padding: 8px 12px;
        border: 1px solid rgba(255, 255, 255, .28);
        border-radius: 999px;
        background: rgba(255, 255, 255, .12);
        color: white;
        font-size: 13px;
        font-weight: 700;
    }

    .login-card {
        margin: 12px;
        padding: 34px;
        border: 1px solid rgba(216, 243, 239, .9);
        border-radius: 8px;
        background: rgba(255, 255, 255, .96);
        box-shadow: 0 18px 45px rgba(0, 71, 76, .16);
    }

    .login-card h2 {
        margin: 0;
        color: var(--hijau);
        font-size: 30px;
    }

    .login-card .muted {
        margin: 8px 0 26px;
    }

    .login-field {
        margin-bottom: 16px;
    }

    .login-field label {
        display: block;
        margin-bottom: 8px;
        color: var(--gelap);
        font-size: 14px;
        font-weight: 700;
    }

    .login-field input {
        height: 46px;
        border-color: #cdeee8;
        background: #f8fffd;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .login-field input:focus {
        outline: 0;
        border-color: var(--toska);
        box-shadow: 0 0 0 4px rgba(15, 190, 168, .16);
    }

    .login-actions {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 10px;
        margin-top: 22px;
    }

    .login-actions .btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        min-height: 46px;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .login-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 26px rgba(0, 71, 76, .18);
    }

    .login-footnote {
        margin-top: 22px;
        color: #66827f;
        font-size: 13px;
        line-height: 1.6;
    }

    @media (max-width: 820px) {
        .login-page {
            padding: 22px;
            align-items: start;
        }

        .login-shell {
            grid-template-columns: 1fr;
        }

        .login-info {
            padding: 30px;
            gap: 26px;
        }

        .login-info h1 {
            font-size: 34px;
        }

        .login-card {
            margin: 0 12px 12px;
            padding: 26px;
        }

        .login-actions {
            grid-template-columns: 1fr;
        }
    }
</style>

@php
    $aksiLogin = $jenis === 'admin'
        ? route('admin.login.proses')
        : ($jenis === 'guru' ? route('guru.login.proses') : route('siswa.login.proses'));
@endphp

<main class="login-page">
    <div class="login-shell">
        <section class="login-info">
            <div class="login-brand">Yayasan Nurul Huda Munjuk</div>

            <div>
                <h1>Portal Akademik Terpadu</h1>
                <p>
                    Masuk ke sistem untuk mengelola data akademik, nilai, informasi,
                    dan layanan santri secara lebih rapi dan terpusat.
                </p>
            </div>

            <div class="login-badges">
                <span class="login-badge">Admin</span>
                <span class="login-badge">Guru</span>
                <span class="login-badge">Siswa</span>
            </div>
        </section>

        <form method="post" class="login-card" action="{{ $aksiLogin }}">
            @csrf

            <h2>{{ $judul }}</h2>
            <p class="muted">Silakan masuk menggunakan akun yang sudah terdaftar.</p>

            @if($errors->any())
                <div class="alert">{{ $errors->first() }}</div>
            @endif

            <div class="login-field">
                <label for="identitas">{{ $label }}</label>
                <input
                    id="identitas"
                    name="identitas"
                    value="{{ old('identitas') }}"
                    autocomplete="username"
                    required
                    autofocus
                >
            </div>

            <div class="login-field">
                <label for="kata_sandi">Password</label>
                <input
                    id="kata_sandi"
                    type="password"
                    name="kata_sandi"
                    autocomplete="current-password"
                    required
                >
            </div>

            <div class="login-actions">
                <button class="btn" type="submit">Masuk</button>
                <a class="btn alt" href="{{ route('beranda') }}">Beranda</a>
            </div>

            <p class="login-footnote">
                Pastikan memilih halaman login sesuai peran akun agar akses dashboard tepat.
            </p>
        </form>
    </div>
</main>
@endsection
