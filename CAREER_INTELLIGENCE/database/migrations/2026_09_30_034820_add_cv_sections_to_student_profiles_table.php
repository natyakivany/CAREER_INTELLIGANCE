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
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('high_school_name')->nullable();
            $table->string('high_school_major')->nullable();
            $table->string('high_school_year')->nullable();
            $table->text('cv_internships')->nullable(); // Magang
            $table->text('cv_organizations')->nullable(); // Organisasi
            $table->text('cv_tools')->nullable(); // Tools & Technology
            $table->text('cv_certificates')->nullable(); // Sertifikat
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'high_school_name',
                'high_school_major',
                'high_school_year',
                'cv_internships',
                'cv_organizations',
                'cv_tools',
                'cv_certificates',
            ]);
        });
    }
};
