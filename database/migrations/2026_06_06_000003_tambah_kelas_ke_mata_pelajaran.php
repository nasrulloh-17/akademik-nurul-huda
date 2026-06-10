<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            if (! Schema::hasColumn('mata_pelajaran', 'kelas_id')) {
                $table->foreignId('kelas_id')->nullable()->after('id')->constrained('kelas')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            if (Schema::hasColumn('mata_pelajaran', 'kelas_id')) {
                $table->dropConstrainedForeignId('kelas_id');
            }
        });
    }
};
