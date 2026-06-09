<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SiswaController extends Controller
{
    private function tahunAjaranAktif()
    {
        $tahunAjaran = DB::table('tahun_ajaran')->where('aktif', true)->first();

        if ($tahunAjaran) {
            return $tahunAjaran;
        }

        $id = DB::table('tahun_ajaran')->insertGetId([
            'nama_tahun_ajaran' => now()->month >= 7 ? now()->year.'/'.now()->addYear()->year : now()->subYear()->year.'/'.now()->year,
            'semester' => now()->month >= 7 ? 'ganjil' : 'genap',
            'aktif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('tahun_ajaran')->where('id', $id)->first();
    }

    private function siswa()
    {
        abort_unless(session('jenis_pengguna') === 'siswa', 403);
        return DB::table('siswa')->where('pengguna_id', session('pengguna_id'))->first();
    }

    public function dashboard()
    {
        $siswa = $this->siswa();
        $tagihan = DB::table('tagihan')
            ->where('siswa_id', $siswa->id)
            ->whereIn('nama_tagihan', ['SPP dan Makan', 'Kelengkapan Sekolah', 'Lainnya'])
            ->get()
            ->keyBy('nama_tagihan');

        return view('siswa.dashboard', [
            'siswa' => $siswa,
            'totalNilai' => DB::table('nilai')->where('siswa_id', $siswa->id)->count(),
            'totalTagihan' => DB::table('tagihan')->where('siswa_id', $siswa->id)->where('status', 'belum lunas')->sum('jumlah'),
            'tagihanAdministrasi' => $tagihan,
            'catatanWaliKelas' => DB::table('catatan_walikelas')
                ->where('siswa_id', $siswa->id)
                ->where('tahun_ajaran_id', $this->tahunAjaranAktif()->id)
                ->latest()
                ->first(),
        ]);
    }

    public function biodata()
    {
        $siswa = $this->siswa();
        $kelas = DB::table('kelas')->where('id', $siswa->kelas_id)->first();
        return view('siswa.biodata', compact('siswa', 'kelas'));
    }

    public function simpanBiodata(Request $request)
    {
        $siswa = $this->siswa();
        $data = $request->validate([
            'jenis_kelamin' => 'nullable',
            'tempat_lahir' => 'nullable',
            'tanggal_lahir' => 'nullable|date',
            'telepon' => 'nullable',
            'alamat' => 'nullable',
            'foto_profil' => 'nullable|image',
        ]);

        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');
            $namaFile = Str::uuid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/siswa'), $namaFile);
            $data['foto_profil'] = "uploads/siswa/$namaFile";
        }

        $data['updated_at'] = now();
        DB::table('siswa')->where('id', $siswa->id)->update($data);
        return back()->with('sukses', 'Biodata berhasil diperbarui.');
    }

    public function raport(Request $request)
    {
        return view('siswa.raport', $this->dataRaport($request));
    }

    public function cetakRaport(Request $request)
    {
        return view('siswa.cetak-raport', $this->dataRaport($request));
    }

    private function dataRaport(Request $request): array
    {
        $siswa = $this->siswa();
        $tahunAjaran = $this->tahunAjaranAktif();
        $daftarTahunAjaran = DB::table('tahun_ajaran')->orderByDesc('id')->get();
        $filterTahunAjaranId = $request->integer('tahun_ajaran_id');
        $tahunAjaranFilter = $filterTahunAjaranId
            ? $daftarTahunAjaran->firstWhere('id', $filterTahunAjaranId)
            : null;
        $tahunAjaranPeringkat = $tahunAjaranFilter ?: $tahunAjaran;
        $nilai = DB::table('nilai')
            ->join('mata_pelajaran', 'mata_pelajaran.id', '=', 'nilai.mata_pelajaran_id')
            ->leftJoin('guru', 'guru.id', '=', 'mata_pelajaran.guru_id')
            ->leftJoin('tahun_ajaran', 'tahun_ajaran.id', '=', 'nilai.tahun_ajaran_id')
            ->where('nilai.siswa_id', $siswa->id)
            ->when($tahunAjaranFilter, fn ($query) => $query->where('nilai.tahun_ajaran_id', $tahunAjaranFilter->id))
            ->select(
                'nilai.*',
                'mata_pelajaran.nama_mata_pelajaran',
                'mata_pelajaran.kkm',
                'guru.nama_guru',
                'tahun_ajaran.nama_tahun_ajaran',
                'tahun_ajaran.semester'
            )
            ->orderByDesc('tahun_ajaran.id')
            ->orderBy('mata_pelajaran.nama_mata_pelajaran')
            ->get();
        $kegiatanTambahan = DB::table('nilai_kegiatan_tambahan')
            ->leftJoin('tahun_ajaran', 'tahun_ajaran.id', '=', 'nilai_kegiatan_tambahan.tahun_ajaran_id')
            ->where('nilai_kegiatan_tambahan.siswa_id', $siswa->id)
            ->when($tahunAjaranFilter, fn ($query) => $query->where('nilai_kegiatan_tambahan.tahun_ajaran_id', $tahunAjaranFilter->id))
            ->select('nilai_kegiatan_tambahan.*', 'tahun_ajaran.nama_tahun_ajaran', 'tahun_ajaran.semester')
            ->orderByDesc('tahun_ajaran.id')
            ->orderBy('kategori')
            ->orderBy('kegiatan')
            ->get();
        $labelPeriode = fn ($item) => trim(($item->nama_tahun_ajaran ?? 'Tanpa Tahun Ajaran').' - '.ucfirst($item->semester ?? 'ganjil'));
        $nilaiPerTahun = $nilai->groupBy($labelPeriode);
        $kegiatanPerTahun = $kegiatanTambahan
            ->groupBy($labelPeriode)
            ->map(fn ($items) => $items->groupBy('kategori'));
        $catatan = DB::table('catatan_walikelas')
            ->leftJoin('tahun_ajaran', 'tahun_ajaran.id', '=', 'catatan_walikelas.tahun_ajaran_id')
            ->where('catatan_walikelas.siswa_id', $siswa->id)
            ->when($tahunAjaranFilter, fn ($query) => $query->where('catatan_walikelas.tahun_ajaran_id', $tahunAjaranFilter->id))
            ->select('catatan_walikelas.*', 'tahun_ajaran.nama_tahun_ajaran', 'tahun_ajaran.semester')
            ->orderByDesc('tahun_ajaran.id')
            ->latest('catatan_walikelas.updated_at')
            ->get();
        $catatanPerTahun = $catatan->groupBy($labelPeriode);
        $peringkatKelas = DB::table('siswa')
            ->leftJoin('nilai', function ($join) use ($tahunAjaranPeringkat) {
                $join->on('nilai.siswa_id', '=', 'siswa.id')
                    ->where('nilai.tahun_ajaran_id', $tahunAjaranPeringkat->id);
            })
            ->where('siswa.kelas_id', $siswa->kelas_id)
            ->where('siswa.status', 'aktif')
            ->select(
                'siswa.id',
                DB::raw('AVG((nilai.nilai_tugas * 0.3) + (nilai.nilai_uts * 0.3) + (nilai.nilai_uas * 0.4)) as rata_rata_raport')
            )
            ->groupBy('siswa.id')
            ->get()
            ->sortBy([
                ['rata_rata_raport', 'desc'],
                ['id', 'asc'],
            ])
            ->values();
        $peringkat = $peringkatKelas->search(fn ($item) => (int) $item->id === (int) $siswa->id);
        $dataPeringkat = $peringkat === false ? null : $peringkatKelas[$peringkat];

        return [
            'siswa' => DB::table('siswa')->leftJoin('kelas', 'kelas.id', '=', 'siswa.kelas_id')->select('siswa.*', 'kelas.nama_kelas')->where('siswa.id', $siswa->id)->first(),
            'tahunAjaranAktif' => $tahunAjaran,
            'daftarTahunAjaran' => $daftarTahunAjaran,
            'tahunAjaranFilter' => $tahunAjaranFilter,
            'nilai' => $nilai,
            'nilaiPerTahun' => $nilaiPerTahun,
            'kegiatanTambahan' => $kegiatanTambahan,
            'kegiatanPerTahun' => $kegiatanPerTahun,
            'catatanPerTahun' => $catatanPerTahun,
            'tahunRaport' => $nilaiPerTahun->keys()->merge($kegiatanPerTahun->keys())->merge($catatanPerTahun->keys())->unique(),
            'peringkat' => $dataPeringkat && $dataPeringkat->rata_rata_raport !== null ? $peringkat + 1 : null,
            'jumlahSiswaKelas' => $peringkatKelas->count(),
            'catatan' => $catatan,
        ];
    }

    public function tagihan()
    {
        $siswa = $this->siswa();
        return view('siswa.tagihan', ['tagihan' => DB::table('tagihan')->where('siswa_id', $siswa->id)->latest()->get()]);
    }
}
