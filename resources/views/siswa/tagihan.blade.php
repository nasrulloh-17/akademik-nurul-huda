@extends('layouts.dashboard')

@section('judul_halaman', 'Tagihan')

@section('konten')
<table>
    <tr>
        <th>Nama Tagihan</th>
        <th>Periode</th>
        <th>Jumlah</th>
        <th>Terbayar</th>
        <th>Sisa</th>
        <th>Jatuh Tempo</th>
        <th>Status</th>
    </tr>

    @forelse($tagihan as $t)
        @php
            $sisa = max(0, (float) $t->jumlah - (float) $t->total_bayar);
        @endphp
        <tr>
            <td>{{ $t->nama_tagihan }}</td>
            <td>{{ $t->periode ?? '-' }}</td>
            <td>Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($sisa, 0, ',', '.') }}</td>
            <td>{{ $t->jatuh_tempo }}</td>
            <td>{{ ucwords($t->status) }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="7">Belum ada tagihan.</td>
        </tr>
    @endforelse
</table>
@endsection
