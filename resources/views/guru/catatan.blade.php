@extends('layouts.dashboard')

@section('judul_halaman', 'Catatan Walikelas')

@section('konten')
<form class="card" method="post" action="{{ route('guru.catatan.simpan') }}">
    @csrf

    <div class="form-grid">
        <select name="siswa_id" required>
            <option value="">Pilih Siswa</option>
            @foreach($siswa as $murid)
                <option value="{{ $murid->id }}">{{ $murid->nama_siswa }} - {{ $murid->nama_kelas }}</option>
            @endforeach
        </select>

        <input name="nama_tagihan" placeholder="Nama Tagihan">
        <input name="jumlah" placeholder="Jumlah Tagihan">
        <input type="date" name="jatuh_tempo">
    </div>

    <p>
        <textarea name="catatan" placeholder="Catatan untuk siswa"></textarea>
    </p>

    <button class="btn">Simpan Catatan / Tagihan</button>
</form>

<table>
    <tr>
        <th>Siswa</th>
        <th>Kelas</th>
        <th>Catatan Terakhir</th>
        <th>Tagihan</th>
    </tr>

    @foreach($siswa as $murid)
        <tr>
            <td>{{ $murid->nama_siswa }}</td>
            <td>{{ $murid->nama_kelas }}</td>
            <td>{{ optional(($catatan[$murid->id] ?? collect())->first())->catatan }}</td>
            <td>
                @foreach(($tagihan[$murid->id] ?? []) as $t)
                    {{ $t->nama_tagihan }}: Rp {{ number_format($t->jumlah, 0, ',', '.') }} ({{ $t->status }})<br>
                @endforeach
            </td>
        </tr>
    @endforeach
</table>
@endsection
