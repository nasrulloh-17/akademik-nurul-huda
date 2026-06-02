<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    private function jaga()
    {
        abort_unless(session('jenis_pengguna') === 'admin', 403);
    }

    private function unggah(Request $request, string $nama, string $folder): ?string
    {
        if (! $request->hasFile($nama)) {
            return null;
        }

        $file = $request->file($nama);
        $namaFile = Str::uuid().'.'.$file->getClientOriginalExtension();
        $file->move(public_path("uploads/$folder"), $namaFile);

        return "uploads/$folder/$namaFile";
    }

    public function dashboard()
    {
        $this->jaga();

        return view('admin.dashboard', [
            'jumlahGuru' => DB::table('guru')->count(),
            'jumlahSiswa' => DB::table('siswa')->count(),
            'jumlahKelas' => DB::table('kelas')->count(),
            'jumlahMapel' => DB::table('mata_pelajaran')->count(),
        ]);
    }

    public function slider()
    {
        $this->jaga();
        return view('admin.slider', ['slider' => DB::table('slider')->latest()->get()]);
    }

    public function simpanSlider(Request $request)
    {
        $this->jaga();
        $data = $request->validate(['judul' => 'required', 'deskripsi' => 'nullable', 'gambar' => 'nullable|image', 'aktif' => 'nullable']);
        $data['gambar'] = $this->unggah($request, 'gambar', 'slider');
        $data['aktif'] = $request->boolean('aktif');
        $data['created_at'] = now();
        $data['updated_at'] = now();
        DB::table('slider')->insert($data);
        return back()->with('sukses', 'Slider berhasil disimpan.');
    }

    public function hapusSlider(int $id)
    {
        $this->jaga();
        DB::table('slider')->where('id', $id)->delete();
        return back()->with('sukses', 'Slider dihapus.');
    }

    public function berita()
    {
        $this->jaga();
        return view('admin.berita', ['berita' => DB::table('berita')->latest()->get()]);
    }

    public function simpanBerita(Request $request)
    {
        $this->jaga();
        $data = $request->validate(['judul' => 'required', 'isi' => 'required', 'tanggal_berita' => 'nullable|date', 'foto_kegiatan' => 'nullable|image']);
        $data['foto_kegiatan'] = $this->unggah($request, 'foto_kegiatan', 'berita');
        $data['created_at'] = now();
        $data['updated_at'] = now();
        DB::table('berita')->insert($data);
        return back()->with('sukses', 'Berita berhasil disimpan.');
    }

    public function hapusBerita(int $id)
    {
        $this->jaga();
        DB::table('berita')->where('id', $id)->delete();
        return back()->with('sukses', 'Berita dihapus.');
    }

    public function informasi()
    {
        $this->jaga();
        return view('admin.informasi', ['informasi' => DB::table('informasi_sekolah')->latest()->get()]);
    }

    public function simpanInformasi(Request $request)
    {
        $this->jaga();
        $data = $request->validate(['judul' => 'required', 'isi' => 'required', 'kontak' => 'nullable']);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        DB::table('informasi_sekolah')->insert($data);
        return back()->with('sukses', 'Informasi berhasil disimpan.');
    }

    public function hapusInformasi(int $id)
    {
        $this->jaga();
        DB::table('informasi_sekolah')->where('id', $id)->delete();
        return back()->with('sukses', 'Informasi dihapus.');
    }

    public function guru()
    {
        $this->jaga();
        return view('admin.guru', [
            'guru' => DB::table('guru')->join('pengguna', 'pengguna.id', '=', 'guru.pengguna_id')->select('guru.*', 'pengguna.identitas')->latest('guru.id')->get(),
            'roles' => DB::table('guru_role')->get()->groupBy('guru_id'),
        ]);
    }

    public function simpanGuru(Request $request)
    {
        $this->jaga();
        $data = $request->validate([
            'id_guru' => 'required',
            'nama_guru' => 'required',
            'kata_sandi' => 'required',
            'role' => 'array',
            'telepon' => 'nullable',
            'alamat' => 'nullable',
        ]);

        DB::transaction(function () use ($data) {
            $penggunaId = DB::table('pengguna')->insertGetId([
                'nama' => $data['nama_guru'],
                'identitas' => $data['id_guru'],
                'kata_sandi' => Hash::make($data['kata_sandi']),
                'jenis_pengguna' => 'guru',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $guruId = DB::table('guru')->insertGetId([
                'pengguna_id' => $penggunaId,
                'id_guru' => $data['id_guru'],
                'nama_guru' => $data['nama_guru'],
                'telepon' => $data['telepon'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            foreach ($data['role'] ?? [] as $role) {
                DB::table('guru_role')->insert(['guru_id' => $guruId, 'role' => $role, 'created_at' => now(), 'updated_at' => now()]);
            }
        });

        return back()->with('sukses', 'Guru dan akun login berhasil dibuat.');
    }

    public function hapusGuru(int $id)
    {
        $this->jaga();
        $penggunaId = DB::table('guru')->where('id', $id)->value('pengguna_id');
        DB::table('pengguna')->where('id', $penggunaId)->delete();
        return back()->with('sukses', 'Guru dihapus.');
    }

    public function ubahPasswordGuru(Request $request, int $id)
    {
        $this->jaga();
        $data = $request->validate(['kata_sandi' => 'required|min:6']);
        $penggunaId = DB::table('guru')->where('id', $id)->value('pengguna_id');
        DB::table('pengguna')->where('id', $penggunaId)->update(['kata_sandi' => Hash::make($data['kata_sandi']), 'updated_at' => now()]);
        return back()->with('sukses', 'Password guru berhasil diubah.');
    }

    public function siswa()
    {
        $this->jaga();
        return view('admin.siswa', [
            'siswa' => DB::table('siswa')->leftJoin('kelas', 'kelas.id', '=', 'siswa.kelas_id')->select('siswa.*', 'kelas.nama_kelas')->latest('siswa.id')->get(),
            'kelas' => DB::table('kelas')->orderBy('nama_kelas')->get(),
        ]);
    }

    public function simpanSiswa(Request $request)
    {
        $this->jaga();
        $data = $request->validate([
            'nis' => 'required',
            'nama_siswa' => 'required',
            'kata_sandi' => 'required',
            'kelas_id' => 'nullable|exists:kelas,id',
            'jenis_kelamin' => 'nullable',
            'telepon' => 'nullable',
            'alamat' => 'nullable',
        ]);

        DB::transaction(function () use ($data) {
            $penggunaId = DB::table('pengguna')->insertGetId([
                'nama' => $data['nama_siswa'],
                'identitas' => $data['nis'],
                'kata_sandi' => Hash::make($data['kata_sandi']),
                'jenis_pengguna' => 'siswa',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('siswa')->insert([
                'pengguna_id' => $penggunaId,
                'kelas_id' => $data['kelas_id'] ?? null,
                'nis' => $data['nis'],
                'nama_siswa' => $data['nama_siswa'],
                'jenis_kelamin' => $data['jenis_kelamin'] ?? null,
                'telepon' => $data['telepon'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return back()->with('sukses', 'Siswa dan akun login berhasil dibuat.');
    }

    public function hapusSiswa(int $id)
    {
        $this->jaga();
        $penggunaId = DB::table('siswa')->where('id', $id)->value('pengguna_id');
        DB::table('pengguna')->where('id', $penggunaId)->delete();
        return back()->with('sukses', 'Siswa dihapus.');
    }

    public function ubahPasswordSiswa(Request $request, int $id)
    {
        $this->jaga();
        $data = $request->validate(['kata_sandi' => 'required|min:6']);
        $penggunaId = DB::table('siswa')->where('id', $id)->value('pengguna_id');
        DB::table('pengguna')->where('id', $penggunaId)->update(['kata_sandi' => Hash::make($data['kata_sandi']), 'updated_at' => now()]);
        return back()->with('sukses', 'Password siswa berhasil diubah.');
    }

    public function kelas()
    {
        $this->jaga();
        return view('admin.kelas', [
            'kelas' => DB::table('kelas')->orderBy('nama_kelas')->get(),
            'siswa' => DB::table('siswa')->orderBy('nama_siswa')->get()->groupBy('kelas_id'),
        ]);
    }

    public function simpanKelas(Request $request)
    {
        $this->jaga();
        $data = $request->validate(['nama_kelas' => 'required', 'tingkat' => 'nullable', 'keterangan' => 'nullable']);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        DB::table('kelas')->insert($data);
        return back()->with('sukses', 'Kelas berhasil disimpan.');
    }

    public function mataPelajaran()
    {
        $this->jaga();
        return view('admin.mata-pelajaran', [
            'mapel' => DB::table('mata_pelajaran')->leftJoin('guru', 'guru.id', '=', 'mata_pelajaran.guru_id')->select('mata_pelajaran.*', 'guru.nama_guru')->latest('mata_pelajaran.id')->get(),
            'guru' => DB::table('guru')->orderBy('nama_guru')->get(),
        ]);
    }

    public function simpanMataPelajaran(Request $request)
    {
        $this->jaga();
        $data = $request->validate(['nama_mata_pelajaran' => 'required', 'guru_id' => 'nullable|exists:guru,id', 'keterangan' => 'nullable']);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        DB::table('mata_pelajaran')->insert($data);
        return back()->with('sukses', 'Mata pelajaran berhasil disimpan.');
    }
}
