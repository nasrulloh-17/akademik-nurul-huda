@extends('layouts.dashboard')

@section('judul_halaman', 'Riwayat Pembayaran')

@section('konten')
<form class="card" method="get" action="{{ route('guru.keuangan.riwayat') }}">
    <div class="form-grid">
        <input name="cari" value="{{ $filterCari }}" placeholder="Cari siswa, NIS, atau tagihan">
        <button class="btn">Cari</button>
        <a class="btn alt" href="{{ route('guru.keuangan.riwayat') }}">Reset</a>
    </div>
</form>

<div class="card" style="overflow-x:auto">
    <table>
        <tr>
            <th>Tanggal</th>
            <th>Siswa</th>
            <th>Tagihan</th>
            <th>Jumlah Bayar</th>
            <th>Metode</th>
            <th>Petugas</th>
            <th>Status</th>
            <th>Bukti</th>
            <th>Aksi</th>
        </tr>

        @forelse($pembayaran as $item)
            <tr>
                <td>{{ $item->tanggal_bayar }}</td>
                <td><strong>{{ $item->nama_siswa }}</strong><br><span class="muted">{{ $item->nis }}</span></td>
                <td>{{ $item->nama_tagihan }} {{ $item->periode ? '('.$item->periode.')' : '' }}</td>
                <td>Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                <td>{{ $item->metode_bayar }}</td>
                <td>{{ $item->nama_petugas ?? '-' }}</td>
                <td>{{ ucwords($item->status) }}</td>
                <td>
                    @if($item->bukti_pembayaran)
                        <a href="{{ asset($item->bukti_pembayaran) }}" target="_blank" rel="noopener">Lihat</a>
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($item->status === 'valid')
                        <details>
                            <summary class="btn danger" style="display:inline-block">Batalkan</summary>
                            <form method="post" action="{{ route('guru.keuangan.riwayat.batal', $item->id) }}" style="margin-top:10px;min-width:260px">
                                @csrf
                                <input name="alasan_pembatalan" placeholder="Alasan pembatalan" required>
                                <p><button class="btn danger">Konfirmasi Batal</button></p>
                            </form>
                        </details>
                    @else
                        {{ $item->alasan_pembatalan ?? '-' }}
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9">Belum ada riwayat pembayaran.</td>
            </tr>
        @endforelse
    </table>
</div>
@endsection
