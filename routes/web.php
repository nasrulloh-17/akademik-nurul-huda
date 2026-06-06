<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Guru\GuruController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Siswa\SiswaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('beranda');
Route::get('/berita/{id}', [LandingController::class, 'berita'])->name('berita.detail');

Route::get('/admin', [AuthController::class, 'formAdmin'])->name('admin.login');
Route::post('/admin', [AuthController::class, 'loginAdmin'])->name('admin.login.proses');
Route::get('/login/guru', [AuthController::class, 'formGuru'])->name('guru.login');
Route::post('/login/guru', [AuthController::class, 'loginGuru'])->name('guru.login.proses');
Route::get('/login/siswa', [AuthController::class, 'formSiswa'])->name('siswa.login');
Route::post('/login/siswa', [AuthController::class, 'loginSiswa'])->name('siswa.login.proses');
Route::post('/keluar', [AuthController::class, 'keluar'])->name('keluar');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
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
    Route::get('/guru', [AdminController::class, 'guru'])->name('guru');
    Route::post('/guru', [AdminController::class, 'simpanGuru'])->name('guru.simpan');
    Route::post('/guru/{id}/password', [AdminController::class, 'ubahPasswordGuru'])->name('guru.password');
    Route::post('/guru/{id}/hapus', [AdminController::class, 'hapusGuru'])->name('guru.hapus');
    Route::get('/siswa', [AdminController::class, 'siswa'])->name('siswa');
    Route::post('/siswa', [AdminController::class, 'simpanSiswa'])->name('siswa.simpan');
    Route::post('/siswa/{id}/password', [AdminController::class, 'ubahPasswordSiswa'])->name('siswa.password');
    Route::post('/siswa/{id}/hapus', [AdminController::class, 'hapusSiswa'])->name('siswa.hapus');
    Route::get('/kelas', [AdminController::class, 'kelas'])->name('kelas');
    Route::post('/kelas', [AdminController::class, 'simpanKelas'])->name('kelas.simpan');
    Route::get('/mata-pelajaran', [AdminController::class, 'mataPelajaran'])->name('mata-pelajaran');
    Route::post('/mata-pelajaran', [AdminController::class, 'simpanMataPelajaran'])->name('mata-pelajaran.simpan');
});

Route::prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('dashboard');
    Route::get('/nilai/{mapel?}', [GuruController::class, 'nilai'])->name('nilai');
    Route::post('/nilai/{mapel}', [GuruController::class, 'simpanNilai'])->name('nilai.simpan');
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
    Route::get('/tagihan', [SiswaController::class, 'tagihan'])->name('tagihan');
});
