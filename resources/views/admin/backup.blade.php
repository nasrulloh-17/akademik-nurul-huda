@extends('layouts.dashboard')

@section('judul_halaman', 'Backup')

@section('konten')
<div class="card">
    <h3>Backup Database</h3>
    <p class="muted">
        Unduh file SQL ini secara rutin, terutama sebelum update hosting atau sebelum mengubah struktur database.
    </p>
    <a class="btn" href="{{ route('admin.backup.sql') }}">Download Backup SQL</a>
</div>

<div class="card">
    <h3>Folder Upload yang Perlu Dibackup</h3>
    <p class="muted">
        File gambar tidak tersimpan di database. Backup folder berikut melalui File Manager cPanel.
    </p>

    <table>
        <tr>
            <th>Folder</th>
        </tr>

        @foreach($folderUpload as $folder)
            <tr>
                <td>public/{{ $folder }}</td>
            </tr>
        @endforeach
    </table>
</div>

<div class="card">
    <h3>Tabel Database</h3>
    <table>
        <tr>
            <th>No</th>
            <th>Nama Tabel</th>
        </tr>

        @foreach($tabel as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ array_values((array) $row)[0] }}</td>
            </tr>
        @endforeach
    </table>
</div>
@endsection
