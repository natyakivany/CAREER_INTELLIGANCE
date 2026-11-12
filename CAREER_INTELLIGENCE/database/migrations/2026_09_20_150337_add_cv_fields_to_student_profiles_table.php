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
            $table->string('cv_headline')->nullable()->after('bio');
            $table->text('cv_summary')->nullable()->after('cv_headline');
            $table->json('cv_custom_skills')->nullable()->after('cv_summary');
            $table->json('cv_experience')->nullable()->after('cv_custom_skills');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn(['cv_headline', 'cv_summary', 'cv_custom_skills', 'cv_experience']);
        });
    }
};
