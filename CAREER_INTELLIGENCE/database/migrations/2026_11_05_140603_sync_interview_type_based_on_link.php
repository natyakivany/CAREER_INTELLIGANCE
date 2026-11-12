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
        // Ketika link tidak diisi, otomatis offline
        DB::table('job_applications')
            ->whereNull('interview_link')
            ->orWhere('interview_link', '')
            ->update(['interview_type' => 'offline']);

        // Ketika link diisi, otomatis online
        DB::table('job_applications')
            ->whereNotNull('interview_link')
            ->where('interview_link', '!=', '')
            ->update(['interview_type' => 'online']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse operation necessary
    }
};
