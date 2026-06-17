<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Guru\GuruController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Siswa\SiswaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('beranda');
Route::get('/berita', [LandingController::class, 'semuaBerita'])->name('berita.semua');
Route::get('/berita/{id}', [LandingController::class, 'berita'])->name('berita.detail');
Route::get('/uploads/{path}', [LandingController::class, 'upload'])->where('path', '.*')->name('uploads.show');

Route::get('/admin', [AuthController::class, 'formAdmin'])->name('admin.login');
Route::post('/admin', [AuthController::class, 'loginAdmin'])->name('admin.login.proses');
Route::get('/login/guru', [AuthController::class, 'formGuru'])->name('guru.login');
Route::post('/login/guru', [AuthController::class, 'loginGuru'])->name('guru.login.proses');
Route::get('/login/siswa', [AuthController::class, 'formSiswa'])->name('siswa.login');
Route::post('/login/siswa', [AuthController::class, 'loginSiswa'])->name('siswa.login.proses');
Route::get('/ubah-password', [AuthController::class, 'formUbahPassword'])->name('password.form');
Route::post('/ubah-password', [AuthController::class, 'ubahPassword'])->name('password.update');
Route::post('/keluar', [AuthController::class, 'keluar'])->name('keluar');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/akses-nilai-wali-kelas', [AdminController::class, 'toggleAksesNilaiWaliKelas'])->name('akses-nilai-wali-kelas');
    Route::get('/slider', [AdminController::class, 'slider'])->name('slider');
    Route::post('/slider', [AdminController::class, 'simpanSlider'])->name('slider.simpan');
    Route::post('/slider/{id}/hapus', [AdminController::class, 'hapusSlider'])->name('slider.hapus');
    Route::get('/berita', [AdminController::class, 'berita'])->name('berita');
    Route::post('/berita', [AdminController::class, 'simpanBerita'])->name('berita.simpan');
    Route::post('/berita/{id}/hapus', [AdminController::class, 'hapusBerita'])->name('berita.hapus');
    Route::get('/prestasi', [AdminController::class, 'prestasi'])->name('prestasi');
    Route::post('/prestasi', [AdminController::class, 'simpanPrestasi'])->name('prestasi.simpan');
    Route::post('/prestasi/{id}/hapus', [AdminController::class, 'hapusPrestasi'])->name('prestasi.hapus');
    Route::get('/galeri', [AdminController::class, 'galeri'])->name('galeri');
    Route::post('/galeri', [AdminController::class, 'simpanGaleri'])->name('galeri.simpan');
    Route::post('/galeri/{id}/hapus', [AdminController::class, 'hapusGaleri'])->name('galeri.hapus');
    Route::get('/informasi', [AdminController::class, 'informasi'])->name('informasi');
    Route::post('/informasi', [AdminController::class, 'simpanInformasi'])->name('informasi.simpan');
    Route::post('/informasi/{id}/hapus', [AdminController::class, 'hapusInformasi'])->name('informasi.hapus');
    Route::get('/data-sekolah', [AdminController::class, 'dataSekolah'])->name('data-sekolah');
    Route::post('/data-sekolah', [AdminController::class, 'simpanDataSekolah'])->name('data-sekolah.simpan');
    Route::get('/guru', [AdminController::class, 'guru'])->name('guru');
    Route::post('/guru', [AdminController::class, 'simpanGuru'])->name('guru.simpan');
    Route::post('/guru/{id}/ubah', [AdminController::class, 'ubahGuru'])->name('guru.ubah');
    Route::post('/guru/{id}/password', [AdminController::class, 'ubahPasswordGuru'])->name('guru.password');
    Route::post('/guru/{id}/hapus', [AdminController::class, 'hapusGuru'])->name('guru.hapus');
    Route::get('/siswa', [AdminController::class, 'siswa'])->name('siswa');
    Route::post('/siswa', [AdminController::class, 'simpanSiswa'])->name('siswa.simpan');
    Route::post('/siswa/{id}/ubah', [AdminController::class, 'ubahSiswa'])->name('siswa.ubah');
    Route::post('/siswa/{id}/password', [AdminController::class, 'ubahPasswordSiswa'])->name('siswa.password');
    Route::post('/siswa/{id}/hapus', [AdminController::class, 'hapusSiswa'])->name('siswa.hapus');
    Route::get('/kelas', [AdminController::class, 'kelas'])->name('kelas');
    Route::post('/kelas', [AdminController::class, 'simpanKelas'])->name('kelas.simpan');
    Route::get('/naik-kelas', [AdminController::class, 'naikKelas'])->name('naik-kelas');
    Route::post('/tahun-ajaran', [AdminController::class, 'simpanTahunAjaran'])->name('tahun-ajaran.simpan');
    Route::post('/tahun-ajaran/{id}/aktif', [AdminController::class, 'aktifkanTahunAjaran'])->name('tahun-ajaran.aktif');
    Route::post('/naik-kelas', [AdminController::class, 'prosesNaikKelas'])->name('naik-kelas.proses');
    Route::post('/lulus-kelas', [AdminController::class, 'prosesLulusKelas'])->name('lulus-kelas.proses');
    Route::get('/mata-pelajaran', [AdminController::class, 'mataPelajaran'])->name('mata-pelajaran');
    Route::post('/mata-pelajaran', [AdminController::class, 'simpanMataPelajaran'])->name('mata-pelajaran.simpan');
    Route::post('/mata-pelajaran/{id}/ubah', [AdminController::class, 'ubahMataPelajaran'])->name('mata-pelajaran.ubah');
    Route::post('/mata-pelajaran/{id}/hapus', [AdminController::class, 'hapusMataPelajaran'])->name('mata-pelajaran.hapus');
    Route::get('/admin-user', [AdminController::class, 'adminUser'])->name('admin-user');
    Route::post('/admin-user', [AdminController::class, 'simpanAdminUser'])->name('admin-user.simpan');
    Route::post('/admin-user/{id}/password', [AdminController::class, 'ubahPasswordAdminUser'])->name('admin-user.password');
    Route::post('/admin-user/{id}/hapus', [AdminController::class, 'hapusAdminUser'])->name('admin-user.hapus');
    Route::get('/backup', [AdminController::class, 'backup'])->name('backup');
    Route::get('/backup/sql', [AdminController::class, 'unduhBackupSql'])->name('backup.sql');
});

Route::prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('dashboard');
    Route::get('/biodata', [GuruController::class, 'biodata'])->name('biodata');
    Route::post('/biodata', [GuruController::class, 'simpanBiodata'])->name('biodata.simpan');
    Route::get('/nilai/{mapel?}', [GuruController::class, 'nilai'])->name('nilai');
    Route::post('/nilai/{mapel}', [GuruController::class, 'simpanNilai'])->name('nilai.simpan');
    Route::post('/nilai/{mapel}/kkm', [GuruController::class, 'simpanKkm'])->name('nilai.kkm');
    Route::get('/nilai/{mapel}/cetak', [GuruController::class, 'cetakNilai'])->name('nilai.cetak');
    Route::get('/kegiatan-tambahan', [GuruController::class, 'kegiatanTambahan'])->name('kegiatan-tambahan');
    Route::post('/kegiatan-tambahan', [GuruController::class, 'simpanKegiatanTambahan'])->name('kegiatan-tambahan.simpan');
    Route::get('/administrasi', [GuruController::class, 'administrasi'])->name('administrasi');
    Route::post('/administrasi', [GuruController::class, 'simpanAdministrasi'])->name('administrasi.simpan');
    Route::get('/download-csv', [GuruController::class, 'downloadCsv'])->name('download-csv');
    Route::get('/download-csv/{jenis}', [GuruController::class, 'unduhCsv'])->name('download-csv.unduh');
    Route::get('/rekap-raport', [GuruController::class, 'rekapRaport'])->name('rekap-raport');
    Route::get('/raport/{siswa}/cetak', [GuruController::class, 'cetakRaportSiswa'])->name('raport.cetak');
    Route::get('/raport/{siswa}/cetak-diniyah', [GuruController::class, 'cetakRaportDiniyah'])->name('raport-diniyah.cetak');
    Route::get('/catatan-walikelas', [GuruController::class, 'catatan'])->name('catatan');
    Route::post('/catatan-walikelas', [GuruController::class, 'simpanCatatan'])->name('catatan.simpan');
    Route::get('/data-siswa', [GuruController::class, 'dataSiswa'])->name('data-siswa');
});

Route::prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('dashboard');
    Route::get('/biodata', [SiswaController::class, 'biodata'])->name('biodata');
    Route::post('/biodata', [SiswaController::class, 'simpanBiodata'])->name('biodata.simpan');
    Route::get('/raport', [SiswaController::class, 'raport'])->name('raport');
    Route::get('/raport/cetak', [SiswaController::class, 'cetakRaport'])->name('raport.cetak');
    Route::get('/raport-diniyah/cetak', [SiswaController::class, 'cetakRaportDiniyah'])->name('raport-diniyah.cetak');
    Route::get('/tagihan', [SiswaController::class, 'tagihan'])->name('tagihan');
});
