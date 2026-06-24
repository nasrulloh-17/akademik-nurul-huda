<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('jenis_tagihan')) {
            Schema::create('jenis_tagihan', function (Blueprint $table) {
                $table->id();
                $table->string('nama_tagihan')->unique();
                $table->text('keterangan')->nullable();
                $table->boolean('aktif')->default(true);
                $table->timestamps();
            });
        }

        foreach (['SPP dan Makan', 'Kelengkapan Sekolah', 'Lainnya'] as $nama) {
            DB::table('jenis_tagihan')->updateOrInsert(
                ['nama_tagihan' => $nama],
                [
                    'aktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        Schema::table('tagihan', function (Blueprint $table) {
            if (! Schema::hasColumn('tagihan', 'jenis_tagihan_id')) {
                $table->foreignId('jenis_tagihan_id')
                    ->nullable()
                    ->after('siswa_id')
                    ->constrained('jenis_tagihan')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('tagihan', 'tahun_ajaran_id')) {
                $table->foreignId('tahun_ajaran_id')
                    ->nullable()
                    ->after('jenis_tagihan_id')
                    ->constrained('tahun_ajaran')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('tagihan', 'periode')) {
                $table->string('periode')->nullable()->after('nama_tagihan');
            }

            if (! Schema::hasColumn('tagihan', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('status');
            }
        });

        DB::statement("ALTER TABLE tagihan MODIFY status ENUM('belum lunas', 'sebagian', 'lunas') DEFAULT 'belum lunas'");

        DB::table('tagihan')
            ->whereNull('jenis_tagihan_id')
            ->orderBy('id')
            ->each(function ($tagihan) {
                $jenisId = DB::table('jenis_tagihan')
                    ->where('nama_tagihan', $tagihan->nama_tagihan)
                    ->value('id');

                if ($jenisId) {
                    DB::table('tagihan')->where('id', $tagihan->id)->update([
                        'jenis_tagihan_id' => $jenisId,
                        'updated_at' => now(),
                    ]);
                }
            });

        if (! Schema::hasTable('pembayaran_tagihan')) {
            Schema::create('pembayaran_tagihan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tagihan_id')->constrained('tagihan')->cascadeOnDelete();
                $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
                $table->foreignId('petugas_id')->nullable()->constrained('pengguna')->nullOnDelete();
                $table->date('tanggal_bayar');
                $table->decimal('jumlah_bayar', 12, 2);
                $table->string('metode_bayar')->default('tunai');
                $table->string('bukti_pembayaran')->nullable();
                $table->text('keterangan')->nullable();
                $table->enum('status', ['valid', 'dibatalkan'])->default('valid');
                $table->text('alasan_pembatalan')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_tagihan');

        DB::statement("UPDATE tagihan SET status = 'belum lunas' WHERE status = 'sebagian'");
        DB::statement("ALTER TABLE tagihan MODIFY status ENUM('belum lunas', 'lunas') DEFAULT 'belum lunas'");

        Schema::table('tagihan', function (Blueprint $table) {
            foreach (['jenis_tagihan_id', 'tahun_ajaran_id'] as $kolom) {
                if (Schema::hasColumn('tagihan', $kolom)) {
                    $table->dropConstrainedForeignId($kolom);
                }
            }

            foreach (['periode', 'keterangan'] as $kolom) {
                if (Schema::hasColumn('tagihan', $kolom)) {
                    $table->dropColumn($kolom);
                }
            }
        });

        Schema::dropIfExists('jenis_tagihan');
    }
};
