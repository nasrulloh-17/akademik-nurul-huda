@extends('layouts.dashboard')

@section('judul_halaman', 'Administrasi')

@section('konten')
<style>
    .administrasi-table input {
        min-width: 120px;
    }

    .administrasi-total {
        font-weight: 800;
        white-space: nowrap;
    }

    @media (max-width: 640px) {
        .administrasi-hide-mobile {
            display: none;
        }

        .administrasi-table {
            min-width: 680px;
        }

        .administrasi-table input {
            min-width: 96px;
        }
    }
</style>

<form class="card" method="post" action="{{ route('guru.administrasi.simpan') }}">
    @csrf

    <h3>Tagihan Siswa</h3>
    <p class="muted">
        Isi nominal tagihan untuk setiap siswa. Kolom total dihitung otomatis dari SPP dan Makan, Kelengkapan Sekolah, dan Lainnya.
    </p>

    <div style="overflow-x:auto">
        <table class="administrasi-table">
            <tr>
                <th class="administrasi-hide-mobile">NIS</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>SPP dan Makan</th>
                <th>Kelengkapan Sekolah</th>
                <th>Lainnya</th>
                <th>Total</th>
            </tr>

            @forelse($siswa as $murid)
                @php
                    $sppMakan = $tagihan[$murid->id.'|SPP dan Makan']->jumlah ?? 0;
                    $kelengkapan = $tagihan[$murid->id.'|Kelengkapan Sekolah']->jumlah ?? 0;
                    $lainnya = $tagihan[$murid->id.'|Lainnya']->jumlah ?? 0;
                @endphp

                <tr data-administrasi-row>
                    <td class="administrasi-hide-mobile">{{ $murid->nis }}</td>
                    <td><strong>{{ $murid->nama_siswa }}</strong></td>
                    <td>{{ $murid->nama_kelas ?? '-' }}</td>
                    <td>
                        <input
                            type="number"
                            min="0"
                            step="1000"
                            name="tagihan[{{ $murid->id }}][spp_makan]"
                            value="{{ (float) $sppMakan }}"
                            data-tagihan-input
                        >
                    </td>
                    <td>
                        <input
                            type="number"
                            min="0"
                            step="1000"
                            name="tagihan[{{ $murid->id }}][kelengkapan]"
                            value="{{ (float) $kelengkapan }}"
                            data-tagihan-input
                        >
                    </td>
                    <td>
                        <input
                            type="number"
                            min="0"
                            step="1000"
                            name="tagihan[{{ $murid->id }}][lainnya]"
                            value="{{ (float) $lainnya }}"
                            data-tagihan-input
                        >
                    </td>
                    <td class="administrasi-total" data-tagihan-total></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Belum ada siswa aktif.</td>
                </tr>
            @endforelse
        </table>
    </div>

    @if($siswa->isNotEmpty())
        <p>
            <button class="btn">Simpan Tagihan</button>
        </p>
    @endif
</form>

<script>
    document.querySelectorAll('[data-administrasi-row]').forEach((row) => {
        const inputs = Array.from(row.querySelectorAll('[data-tagihan-input]'));
        const total = row.querySelector('[data-tagihan-total]');
        const rupiah = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        });

        const hitungTotal = () => {
            const jumlah = inputs.reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);
            total.textContent = rupiah.format(jumlah);
        };

        inputs.forEach((input) => input.addEventListener('input', hitungTotal));
        hitungTotal();
    });
</script>
@endsection
