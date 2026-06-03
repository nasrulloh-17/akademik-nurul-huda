@extends('layouts.dashboard')

@section('judul_halaman', 'Tagihan')

@section('konten')
<table>
    <tr>
        <th>Nama Tagihan</th>
        <th>Jumlah</th>
        <th>Jatuh Tempo</th>
        <th>Status</th>
    </tr>

    @forelse($tagihan as $t)
        <tr>
            <td>{{ $t->nama_tagihan }}</td>
            <td>Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
            <td>{{ $t->jatuh_tempo }}</td>
            <td>{{ $t->status }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="4">Belum ada tagihan.</td>
        </tr>
    @endforelse
</table>
@endsection
