<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE job_applications MODIFY COLUMN status ENUM('Menunggu Review', 'Proses Seleksi', 'Wawancara', 'Diterima', 'Ditolak', 'Tawaran Ditolak') DEFAULT 'Menunggu Review'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE job_applications MODIFY COLUMN status ENUM('Menunggu Review', 'Diterima', 'Ditolak') DEFAULT 'Menunggu Review'");
        }
    }
};
