@extends('layouts.dashboard')

@section('judul_halaman', 'Tagihan Siswa')

@section('konten')
<form class="card" method="post" action="{{ route('guru.keuangan.tagihan.simpan') }}">
    @csrf

    <h3>Buat Tagihan</h3>
    <p class="muted">Pilih satu siswa atau satu kelas. Jika kelas dipilih, tagihan dibuat untuk semua siswa aktif di kelas tersebut.</p>

    <div class="form-grid">
        <select name="jenis_tagihan_id" required>
            <option value="">Pilih jenis tagihan</option>
            @foreach($jenisTagihan as $jenis)
                <option value="{{ $jenis->id }}">{{ $jenis->nama_tagihan }}</option>
            @endforeach
        </select>

        <select name="siswa_id">
            <option value="">Pilih siswa tertentu</option>
            @foreach($siswa as $murid)
                <option value="{{ $murid->id }}">{{ $murid->nama_siswa }} - {{ $murid->nama_kelas ?? 'Tanpa kelas' }}</option>
            @endforeach
        </select>

        <select name="kelas_id">
            <option value="">Atau pilih kelas</option>
            @foreach($kelas as $item)
                <option value="{{ $item->id }}">{{ $item->nama_kelas }}</option>
            @endforeach
        </select>

        <input name="periode" placeholder="Periode, misal Juli 2026">
        <input type="number" name="jumlah" min="0" step="1000" placeholder="Nominal tagihan" required>
        <input type="date" name="jatuh_tempo">
    </div>

    <p>
        <textarea name="keterangan" placeholder="Keterangan tagihan"></textarea>
    </p>

    <button class="btn">Simpan Tagihan</button>
</form>

<form class="card" method="get" action="{{ route('guru.keuangan.tagihan') }}">
    <div class="form-grid">
        <input name="cari" value="{{ $filterCari }}" placeholder="Cari siswa, NIS, NISN, atau tagihan">
        <select name="kelas_id">
            <option value="">Semua kelas</option>
            @foreach($kelas as $item)
                <option value="{{ $item->id }}" @selected((string) $filterKelasId === (string) $item->id)>{{ $item->nama_kelas }}</option>
            @endforeach
        </select>
        <button class="btn">Filter</button>
        <a class="btn alt" href="{{ route('guru.keuangan.tagihan') }}">Reset</a>
    </div>
</form>

<div class="card" style="overflow-x:auto">
    <table>
        <tr>
            <th>Siswa</th>
            <th>Kelas</th>
            <th>Tagihan</th>
            <th>Periode</th>
            <th>Jumlah</th>
            <th>Terbayar</th>
            <th>Sisa</th>
            <th>Status</th>
            <th>Jatuh Tempo</th>
        </tr>

        @forelse($tagihan as $item)
            @php
                $sisa = max(0, (float) $item->jumlah - (float) $item->total_bayar);
            @endphp
            <tr>
                <td><strong>{{ $item->nama_siswa }}</strong><br><span class="muted">{{ $item->nis }}</span></td>
                <td>{{ $item->nama_kelas ?? '-' }}</td>
                <td>{{ $item->nama_tagihan }}</td>
                <td>{{ $item->periode ?? '-' }}</td>
                <td>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                <td>{{ ucwords($item->status) }}</td>
                <td>{{ $item->jatuh_tempo ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9">Belum ada tagihan.</td>
            </tr>
        @endforelse
    </table>
</div>
@endsection
