<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruController extends Controller
{
    private function guru()
    {
        abort_unless(session('jenis_pengguna') === 'guru', 403);
        return DB::table('guru')->where('pengguna_id', session('pengguna_id'))->first();
    }

    public function dashboard()
    {
        $guru = $this->guru();
        return view('guru.dashboard', [
            'guru' => $guru,
            'mapel' => DB::table('mata_pelajaran')->where('guru_id', $guru->id)->get(),
            'roles' => DB::table('guru_role')->where('guru_id', $guru->id)->pluck('role')->toArray(),
        ]);
    }

    public function nilai(?int $mapel = null)
    {
        $guru = $this->guru();
        $mapelGuru = DB::table('mata_pelajaran')->where('guru_id', $guru->id)->get();
        $aktif = $mapel ? $mapelGuru->firstWhere('id', $mapel) : $mapelGuru->first();
        $siswa = DB::table('siswa')->leftJoin('kelas', 'kelas.id', '=', 'siswa.kelas_id')->select('siswa.*', 'kelas.nama_kelas')->orderBy('nama_siswa')->get();
        $nilai = $aktif ? DB::table('nilai')->where('mata_pelajaran_id', $aktif->id)->get()->keyBy('siswa_id') : collect();

        return view('guru.nilai', compact('mapelGuru', 'aktif', 'siswa', 'nilai'));
    }

    public function simpanNilai(Request $request, int $mapel)
    {
        $guru = $this->guru();
        abort_unless(DB::table('mata_pelajaran')->where('id', $mapel)->where('guru_id', $guru->id)->exists(), 403);

        foreach ($request->input('nilai', []) as $siswaId => $isi) {
            DB::table('nilai')->updateOrInsert(
                ['siswa_id' => $siswaId, 'mata_pelajaran_id' => $mapel],
                [
                    'nilai_tugas' => $isi['nilai_tugas'] ?? 0,
                    'nilai_uts' => $isi['nilai_uts'] ?? 0,
                    'nilai_uas' => $isi['nilai_uas'] ?? 0,
                    'catatan_guru' => $isi['catatan_guru'] ?? null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        return back()->with('sukses', 'Nilai berhasil diperbarui.');
    }

    public function catatan()
    {
        $guru = $this->guru();
        abort_unless(DB::table('guru_role')->where('guru_id', $guru->id)->where('role', 'wali kelas')->exists(), 403);

        return view('guru.catatan', [
            'siswa' => DB::table('siswa')->leftJoin('kelas', 'kelas.id', '=', 'siswa.kelas_id')->select('siswa.*', 'kelas.nama_kelas')->orderBy('nama_siswa')->get(),
            'catatan' => DB::table('catatan_walikelas')->where('guru_id', $guru->id)->latest()->get()->groupBy('siswa_id'),
            'tagihan' => DB::table('tagihan')->latest()->get()->groupBy('siswa_id'),
        ]);
    }

    public function simpanCatatan(Request $request)
    {
        $guru = $this->guru();
        abort_unless(DB::table('guru_role')->where('guru_id', $guru->id)->where('role', 'wali kelas')->exists(), 403);
        $data = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'catatan' => 'nullable',
            'nama_tagihan' => 'nullable',
            'jumlah' => 'nullable|numeric',
            'jatuh_tempo' => 'nullable|date',
        ]);

        if (! empty($data['catatan'])) {
            DB::table('catatan_walikelas')->insert([
                'siswa_id' => $data['siswa_id'],
                'guru_id' => $guru->id,
                'catatan' => $data['catatan'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (! empty($data['nama_tagihan']) && isset($data['jumlah'])) {
            DB::table('tagihan')->insert([
                'siswa_id' => $data['siswa_id'],
                'nama_tagihan' => $data['nama_tagihan'],
                'jumlah' => $data['jumlah'],
                'jatuh_tempo' => $data['jatuh_tempo'] ?? null,
                'status' => 'belum lunas',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('sukses', 'Catatan atau tagihan berhasil disimpan.');
    }

    public function dataSiswa()
    {
        $this->guru();
        return view('guru.data-siswa', [
            'kelas' => DB::table('kelas')->orderBy('nama_kelas')->get(),
            'siswa' => DB::table('siswa')->orderBy('nama_siswa')->get()->groupBy('kelas_id'),
        ]);
    }
}
