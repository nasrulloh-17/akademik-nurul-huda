<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SiswaController extends Controller
{
    private function siswa()
    {
        abort_unless(session('jenis_pengguna') === 'siswa', 403);
        return DB::table('siswa')->where('pengguna_id', session('pengguna_id'))->first();
    }

    public function dashboard()
    {
        $siswa = $this->siswa();
        return view('siswa.dashboard', [
            'siswa' => $siswa,
            'totalNilai' => DB::table('nilai')->where('siswa_id', $siswa->id)->count(),
            'totalTagihan' => DB::table('tagihan')->where('siswa_id', $siswa->id)->where('status', 'belum lunas')->sum('jumlah'),
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

    public function raport()
    {
        return view('siswa.raport', $this->dataRaport());
    }

    public function cetakRaport()
    {
        return view('siswa.cetak-raport', $this->dataRaport());
    }

    private function dataRaport(): array
    {
        $siswa = $this->siswa();
        $nilai = DB::table('nilai')
            ->join('mata_pelajaran', 'mata_pelajaran.id', '=', 'nilai.mata_pelajaran_id')
            ->leftJoin('guru', 'guru.id', '=', 'mata_pelajaran.guru_id')
            ->leftJoin('tahun_ajaran', 'tahun_ajaran.id', '=', 'nilai.tahun_ajaran_id')
            ->where('nilai.siswa_id', $siswa->id)
            ->select(
                'nilai.*',
                'mata_pelajaran.nama_mata_pelajaran',
                'guru.nama_guru',
                'tahun_ajaran.nama_tahun_ajaran'
            )
            ->orderByDesc('tahun_ajaran.id')
            ->orderBy('mata_pelajaran.nama_mata_pelajaran')
            ->get();
        $kegiatanTambahan = DB::table('nilai_kegiatan_tambahan')
            ->leftJoin('tahun_ajaran', 'tahun_ajaran.id', '=', 'nilai_kegiatan_tambahan.tahun_ajaran_id')
            ->where('nilai_kegiatan_tambahan.siswa_id', $siswa->id)
            ->select('nilai_kegiatan_tambahan.*', 'tahun_ajaran.nama_tahun_ajaran')
            ->orderByDesc('tahun_ajaran.id')
            ->orderBy('kategori')
            ->orderBy('kegiatan')
            ->get();
        $nilaiPerTahun = $nilai->groupBy(fn ($item) => $item->nama_tahun_ajaran ?? 'Tanpa Tahun Ajaran');
        $kegiatanPerTahun = $kegiatanTambahan
            ->groupBy(fn ($item) => $item->nama_tahun_ajaran ?? 'Tanpa Tahun Ajaran')
            ->map(fn ($items) => $items->groupBy('kategori'));

        return [
            'siswa' => DB::table('siswa')->leftJoin('kelas', 'kelas.id', '=', 'siswa.kelas_id')->select('siswa.*', 'kelas.nama_kelas')->where('siswa.id', $siswa->id)->first(),
            'nilai' => $nilai,
            'nilaiPerTahun' => $nilaiPerTahun,
            'kegiatanTambahan' => $kegiatanTambahan,
            'kegiatanPerTahun' => $kegiatanPerTahun,
            'tahunRaport' => $nilaiPerTahun->keys()->merge($kegiatanPerTahun->keys())->unique(),
            'catatan' => DB::table('catatan_walikelas')->where('siswa_id', $siswa->id)->latest()->get(),
        ];
    }

    public function tagihan()
    {
        $siswa = $this->siswa();
        return view('siswa.tagihan', ['tagihan' => DB::table('tagihan')->where('siswa_id', $siswa->id)->latest()->get()]);
    }
}
