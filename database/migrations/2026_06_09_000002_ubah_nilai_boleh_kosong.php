<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE nilai MODIFY nilai_tugas DECIMAL(5,2) NULL');
        DB::statement('ALTER TABLE nilai MODIFY nilai_uts DECIMAL(5,2) NULL');
        DB::statement('ALTER TABLE nilai MODIFY nilai_uas DECIMAL(5,2) NULL');
    }

    public function down(): void
    {
        DB::statement('UPDATE nilai SET nilai_tugas = 0 WHERE nilai_tugas IS NULL');
        DB::statement('UPDATE nilai SET nilai_uts = 0 WHERE nilai_uts IS NULL');
        DB::statement('UPDATE nilai SET nilai_uas = 0 WHERE nilai_uas IS NULL');

        DB::statement('ALTER TABLE nilai MODIFY nilai_tugas DECIMAL(5,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE nilai MODIFY nilai_uts DECIMAL(5,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE nilai MODIFY nilai_uas DECIMAL(5,2) NOT NULL DEFAULT 0');
    }
};
