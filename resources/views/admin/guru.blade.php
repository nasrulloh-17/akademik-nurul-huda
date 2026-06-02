@extends('layouts.dashboard')
@section('judul_halaman','Guru')
@section('konten')
<form class="card" method="post" action="{{ route('admin.guru.simpan') }}">@csrf
    <div class="form-grid"><input name="id_guru" placeholder="ID Guru" required><input name="nama_guru" placeholder="Nama Guru" required><input type="password" name="kata_sandi" placeholder="Password" required><input name="telepon" placeholder="Telepon"></div>
    <p><textarea name="alamat" placeholder="Alamat"></textarea></p>
    <p><label><input type="checkbox" name="role[]" value="pengampu mata pelajaran" style="width:auto"> Pengampu Mata Pelajaran</label> <label><input type="checkbox" name="role[]" value="wali kelas" style="width:auto"> Wali Kelas</label> <label><input type="checkbox" name="role[]" value="staff" style="width:auto"> Staff</label></p>
    <button class="btn">Simpan Guru</button>
</form>
<table><tr><th>ID Guru</th><th>Nama Guru</th><th>Role</th><th>Telepon</th><th>Ubah Password</th><th>Aksi</th></tr>
@foreach($guru as $item)<tr><td>{{ $item->id_guru }}</td><td>{{ $item->nama_guru }}</td><td>{{ isset($roles[$item->id]) ? $roles[$item->id]->pluck('role')->join(', ') : '-' }}</td><td>{{ $item->telepon }}</td><td><form method="post" action="{{ route('admin.guru.password',$item->id) }}">@csrf<div style="display:flex;gap:8px"><input type="password" name="kata_sandi" placeholder="Password baru" required><button class="btn">Ubah</button></div></form></td><td><form method="post" action="{{ route('admin.guru.hapus',$item->id) }}">@csrf<button class="btn danger">Hapus</button></form></td></tr>@endforeach
</table>
@endsection
