<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dateTime('interview_date')->nullable()->after('status');
            $table->string('interview_link')->nullable()->after('interview_date');
            $table->string('interview_status')->nullable()->after('interview_link'); // Menunggu Konfirmasi, Hadir, Tidak Hadir
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['interview_date', 'interview_link', 'interview_status']);
        });
    }
};
