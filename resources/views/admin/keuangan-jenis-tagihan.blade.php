@extends('layouts.dashboard')

@section('judul_halaman', 'Jenis Tagihan')

@section('konten')
<form class="card" method="post" action="{{ route('guru.keuangan.jenis-tagihan.simpan') }}">
    @csrf

    <div class="form-grid">
        <input name="nama_tagihan" placeholder="Nama tagihan, misal SPP" required>
        <input name="keterangan" placeholder="Keterangan">
        <label>
            <input type="checkbox" name="aktif" value="1" checked style="width:auto">
            Aktif
        </label>
    </div>

    <p><button class="btn">Tambah Jenis Tagihan</button></p>
</form>

<div class="card">
    <table>
        <tr>
            <th>Nama Tagihan</th>
            <th>Keterangan</th>
            <th>Status</th>
            <th>Ubah</th>
        </tr>

        @forelse($jenisTagihan as $item)
            <tr>
                <td>{{ $item->nama_tagihan }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
                <td>{{ $item->aktif ? 'Aktif' : 'Nonaktif' }}</td>
                <td>
                    <details>
                        <summary class="btn alt" style="display:inline-block">Ubah</summary>

                        <form method="post" action="{{ route('guru.keuangan.jenis-tagihan.ubah', $item->id) }}" style="margin-top:12px;min-width:320px">
                            @csrf

                            <div class="form-grid">
                                <input name="nama_tagihan" value="{{ $item->nama_tagihan }}" required>
                                <input name="keterangan" value="{{ $item->keterangan }}" placeholder="Keterangan">
                                <label>
                                    <input type="checkbox" name="aktif" value="1" @checked($item->aktif) style="width:auto">
                                    Aktif
                                </label>
                            </div>

                            <p><button class="btn">Simpan Perubahan</button></p>
                        </form>
                    </details>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">Belum ada jenis tagihan.</td>
            </tr>
        @endforelse
    </table>
</div>
@endsection
