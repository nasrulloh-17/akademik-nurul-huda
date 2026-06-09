<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catatan_walikelas', function (Blueprint $table) {
            if (! Schema::hasColumn('catatan_walikelas', 'tahun_ajaran_id')) {
                $table->foreignId('tahun_ajaran_id')
                    ->nullable()
                    ->after('guru_id')
                    ->constrained('tahun_ajaran')
                    ->nullOnDelete();
            }
        });

        $tahunAjaranId = DB::table('tahun_ajaran')->where('aktif', true)->value('id')
            ?? DB::table('tahun_ajaran')->orderByDesc('id')->value('id');

        if ($tahunAjaranId) {
            DB::table('catatan_walikelas')
                ->whereNull('tahun_ajaran_id')
                ->update(['tahun_ajaran_id' => $tahunAjaranId]);
        }
    }

    public function down(): void
    {
        Schema::table('catatan_walikelas', function (Blueprint $table) {
            if (Schema::hasColumn('catatan_walikelas', 'tahun_ajaran_id')) {
                $table->dropConstrainedForeignId('tahun_ajaran_id');
            }
        });
    }
};
