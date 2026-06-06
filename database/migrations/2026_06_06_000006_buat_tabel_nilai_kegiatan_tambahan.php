<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('nilai_kegiatan_tambahan')) {
            return;
        }

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
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_kegiatan_tambahan');
    }
};
