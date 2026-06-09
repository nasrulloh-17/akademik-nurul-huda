@extends('layouts.dashboard')

@section('judul_halaman', 'Admin')

@section('konten')
<form class="card" method="post" action="{{ route('admin.admin-user.simpan') }}">
    @csrf

    <div class="form-grid">
        <input name="nama" placeholder="Nama Admin" required>
        <input name="identitas" placeholder="Username Admin" required>
        <input name="kata_sandi" type="password" placeholder="Password" required>
    </div>

    <p><button class="btn">Tambah Admin</button></p>
</form>

<div class="card">
    <table>
        <tr>
            <th>Nama</th>
            <th>Username</th>
            <th>Ubah Password</th>
            <th>Hapus</th>
        </tr>

        @foreach($admin as $item)
            <tr>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->identitas }}</td>
                <td>
                    <form method="post" action="{{ route('admin.admin-user.password', $item->id) }}">
                        @csrf
                        <div class="form-grid">
                            <input name="kata_sandi" type="password" placeholder="Password baru" required>
                            <button class="btn">Simpan</button>
                        </div>
                    </form>
                </td>
                <td>
                    <form method="post" action="{{ route('admin.admin-user.hapus', $item->id) }}">
                        @csrf
                        <button class="btn danger" onclick="return confirm('Hapus admin ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
</div>
@endsection
