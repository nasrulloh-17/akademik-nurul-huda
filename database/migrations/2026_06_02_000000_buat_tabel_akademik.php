<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tahun_ajaran');
            $table->enum('semester', ['ganjil', 'genap'])->default('ganjil');
            $table->boolean('aktif')->default(false);
            $table->timestamps();
            $table->unique(['nama_tahun_ajaran', 'semester']);
        });

        Schema::create('pengguna', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('identitas')->unique();
            $table->string('kata_sandi');
            $table->enum('jenis_pengguna', ['admin', 'guru', 'siswa']);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
            $table->string('tingkat')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $table->string('id_guru')->unique();
            $table->string('nama_guru');
            $table->string('telepon')->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });

        Schema::create('guru_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru')->cascadeOnDelete();
            $table->enum('role', ['pengampu mata pelajaran', 'wali kelas', 'staff']);
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->string('staff_jenis')->nullable();
            $table->timestamps();
        });

        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->string('nis')->unique();
            $table->string('nama_siswa');
            $table->string('jenis_kelamin')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('telepon')->nullable();
            $table->text('alamat')->nullable();
            $table->string('foto_profil')->nullable();
            $table->enum('status', ['aktif', 'lulus'])->default('aktif');
            $table->date('tanggal_lulus')->nullable();
            $table->timestamps();
        });

        Schema::create('riwayat_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['siswa_id', 'tahun_ajaran_id']);
        });

        Schema::create('slider', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->string('link')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('berita', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('isi');
            $table->string('foto_kegiatan')->nullable();
            $table->date('tanggal_berita')->nullable();
            $table->timestamps();
        });

        Schema::create('informasi_sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('isi');
            $table->string('kontak')->nullable();
            $table->timestamps();
        });

        Schema::create('data_sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_mts')->nullable();
            $table->string('kepala_mts')->nullable();
            $table->string('nama_sma')->nullable();
            $table->string('kepala_sma')->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });

        Schema::create('prestasi', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('keterangan')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });

        Schema::create('galeri', function (Blueprint $table) {
            $table->id();
            $table->string('judul')->nullable();
            $table->string('foto');
            $table->timestamps();
        });

        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('guru')->nullOnDelete();
            $table->string('nama_mata_pelajaran');
            $table->decimal('kkm', 5, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajaran')->nullOnDelete();
            $table->decimal('nilai_tugas', 5, 2)->default(0);
            $table->decimal('nilai_uts', 5, 2)->default(0);
            $table->decimal('nilai_uas', 5, 2)->default(0);
            $table->text('catatan_guru')->nullable();
            $table->timestamps();
            $table->unique(['siswa_id', 'mata_pelajaran_id', 'tahun_ajaran_id'], 'nilai_siswa_mapel_tahun_unique');
        });

        Schema::create('catatan_walikelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('guru')->nullOnDelete();
            $table->text('catatan');
            $table->timestamps();
        });

        Schema::create('nilai_kegiatan_tambahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('guru')->nullOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->string('kategori');
            $table->string('kegiatan');
            $table->string('nilai')->nullable();
            $table->timestamps();
            $table->unique(
                ['siswa_id', 'tahun_ajaran_id', 'kategori', 'kegiatan'],
                'nilai_kegiatan_siswa_tahun_kategori_unique'
            );
        });

        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->string('nama_tagihan');
            $table->decimal('jumlah', 12, 2);
            $table->date('jatuh_tempo')->nullable();
            $table->enum('status', ['belum lunas', 'lunas'])->default('belum lunas');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['tagihan', 'nilai_kegiatan_tambahan', 'catatan_walikelas', 'nilai', 'mata_pelajaran', 'galeri', 'prestasi', 'data_sekolah', 'informasi_sekolah', 'berita', 'slider', 'riwayat_kelas', 'siswa', 'guru_role', 'guru', 'kelas', 'pengguna', 'tahun_ajaran'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
