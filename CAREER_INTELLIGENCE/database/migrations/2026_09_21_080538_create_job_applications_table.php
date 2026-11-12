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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_listing_id')->constrained()->cascadeOnDelete();
            $table->float('match_score')->nullable();
            $table->enum('status', ['Menunggu Review', 'Proses Seleksi', 'Wawancara', 'Diterima', 'Ditolak', 'Tawaran Ditolak'])->default('Menunggu Review');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'job_listing_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
