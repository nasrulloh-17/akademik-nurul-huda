<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guru_role', function (Blueprint $table) {
            if (! Schema::hasColumn('guru_role', 'kelas_id')) {
                $table->foreignId('kelas_id')->nullable()->after('role')->constrained('kelas')->nullOnDelete();
            }

            if (! Schema::hasColumn('guru_role', 'staff_jenis')) {
                $table->string('staff_jenis')->nullable()->after('kelas_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guru_role', function (Blueprint $table) {
            if (Schema::hasColumn('guru_role', 'kelas_id')) {
                $table->dropConstrainedForeignId('kelas_id');
            }

            if (Schema::hasColumn('guru_role', 'staff_jenis')) {
                $table->dropColumn('staff_jenis');
            }
        });
    }
};
