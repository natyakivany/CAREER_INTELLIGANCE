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
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('department')->default('Teknologi Informasi');
            $table->string('type')->default('Magang Mandiri Kampus');
            $table->string('location')->default('Padang (Onsite)');
            $table->string('duration')->default('6 Bulan');
            $table->json('skills')->nullable();
            $table->enum('status', ['Buka', 'Tutup'])->default('Buka');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
