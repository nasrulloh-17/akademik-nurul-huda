@extends('layouts.dashboard')

@section('judul_halaman', 'Input Pembayaran')

@section('konten')
<form class="card" method="get" action="{{ route('guru.keuangan.pembayaran') }}">
    <div class="form-grid">
        <select name="siswa_id" required>
            <option value="">Pilih siswa</option>
            @foreach($siswa as $murid)
                <option value="{{ $murid->id }}" @selected((int) $siswaTerpilih === (int) $murid->id)>
                    {{ $murid->nama_siswa }} - {{ $murid->nis }}
                </option>
            @endforeach
        </select>
        <button class="btn">Lihat Tagihan</button>
    </div>
</form>

@if($siswaTerpilih)
    <div class="card" style="overflow-x:auto">
        <table>
            <tr>
                <th>Tagihan</th>
                <th>Periode</th>
                <th>Jumlah</th>
                <th>Terbayar</th>
                <th>Sisa</th>
                <th>Bayar</th>
            </tr>

            @forelse($tagihan as $item)
                @php
                    $sisa = max(0, (float) $item->jumlah - (float) $item->total_bayar);
                @endphp
                <tr>
                    <td>{{ $item->nama_tagihan }}</td>
                    <td>{{ $item->periode ?? '-' }}</td>
                    <td>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                    <td>
                        <details>
                            <summary class="btn alt" style="display:inline-block">Input Bayar</summary>

                            <form method="post" enctype="multipart/form-data" action="{{ route('guru.keuangan.pembayaran.simpan') }}" style="margin-top:12px;min-width:320px">
                                @csrf
                                <input type="hidden" name="tagihan_id" value="{{ $item->id }}">

                                <div class="form-grid">
                                    <input type="date" name="tanggal_bayar" value="{{ now()->toDateString() }}" required>
                                    <input type="number" name="jumlah_bayar" min="1" step="1000" value="{{ $sisa }}" required>
                                    <select name="metode_bayar" required>
                                        <option value="tunai">Tunai</option>
                                        <option value="transfer">Transfer</option>
                                        <option value="QRIS">QRIS</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                    <input type="file" name="bukti_pembayaran" accept="image/*,application/pdf">
                                </div>

                                <p>
                                    <textarea name="keterangan" placeholder="Keterangan pembayaran"></textarea>
                                </p>

                                <button class="btn">Simpan Pembayaran</button>
                            </form>
                        </details>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Siswa ini tidak memiliki tagihan belum lunas.</td>
                </tr>
            @endforelse
        </table>
    </div>
@endif
@endsection
