<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Expand enum columns to include 'all' value for combined IT & Elektro assessments.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Expand assessments.field enum to include 'all'
            DB::statement("ALTER TABLE `assessments` MODIFY COLUMN `field` ENUM('it', 'elektro', 'all') NOT NULL DEFAULT 'it'");

            // Expand student_profiles.target_field enum to include 'all'
            DB::statement("ALTER TABLE `student_profiles` MODIFY COLUMN `target_field` ENUM('it', 'elektro', 'all') NOT NULL DEFAULT 'it'");
        }
    }

    /**
     * Revert enum columns back to original values.
     */
    public function down(): void
    {
        // Update any 'all' values back to 'it' before shrinking the enum
        DB::table('assessments')->where('field', 'all')->update(['field' => 'it']);
        DB::table('student_profiles')->where('target_field', 'all')->update(['target_field' => 'it']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `assessments` MODIFY COLUMN `field` ENUM('it', 'elektro') NOT NULL DEFAULT 'it'");
            DB::statement("ALTER TABLE `student_profiles` MODIFY COLUMN `target_field` ENUM('it', 'elektro') NOT NULL DEFAULT 'it'");
        }
    }
};
