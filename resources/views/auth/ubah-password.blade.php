@extends('layouts.dashboard')

@section('judul_halaman', 'Ubah Password')

@section('konten')
<form class="card" method="post" action="{{ route('password.update') }}">
    @csrf

    <div class="form-grid">
        <div>
            <label>Password Lama</label>
            <input type="password" name="kata_sandi_lama" required autofocus>
        </div>

        <div>
            <label>Password Baru</label>
            <input type="password" name="kata_sandi_baru" required>
        </div>

        <div>
            <label>Konfirmasi Password Baru</label>
            <input type="password" name="kata_sandi_baru_confirmation" required>
        </div>
    </div>

    <p class="muted">Gunakan minimal 6 karakter dan simpan password baru dengan aman.</p>

    <button class="btn">Simpan Password</button>
</form>
@endsection
