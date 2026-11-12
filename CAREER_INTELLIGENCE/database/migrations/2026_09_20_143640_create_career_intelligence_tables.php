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
        // 1. Occupations (Daftar Profesi / Pekerjaan Standar Industri)
        Schema::create('occupations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('field', ['it', 'elektro'])->default('it');
            $table->string('category')->nullable(); // e.g. 'Software Engineering', 'Embedded & IoT', 'Network & Security'
            $table->text('description')->nullable();
            $table->string('average_salary')->nullable(); // e.g. 'Rp 8.000.000 - Rp 15.000.000'
            $table->string('demand_level')->default('Tinggi'); // e.g. 'Tinggi', 'Sangat Tinggi', 'Menengah'
            $table->timestamps();
        });

        // 2. Occupation Skills (Standar Benchmark Kebutuhan Industri)
        Schema::create('occupation_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('occupation_id')->constrained('occupations')->cascadeOnDelete();
            $table->string('skill_name');
            $table->integer('benchmark_score')->default(85); // 0 - 100
            $table->boolean('is_core')->default(true);
            $table->timestamps();
        });

        // 3. Student Profiles (Profil Lengkap Mahasiswa)
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('prodi')->default('Teknik Komputer');
            $table->string('jurusan')->default('Teknologi Informasi');
            $table->string('semester')->default('Semester 7');
            $table->string('phone')->nullable();
            $table->enum('target_field', ['it', 'elektro'])->default('it');
            $table->foreignId('target_occupation_id')->nullable()->constrained('occupations')->nullOnDelete();
            $table->text('bio')->nullable();
            $table->decimal('ipk', 3, 2)->nullable()->default(3.50);
            $table->integer('employability_score')->default(0);
            $table->timestamps();
        });

        // 4. Portfolios (Portofolio Proyek Mahasiswa)
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->enum('field', ['it', 'elektro'])->default('it');
            $table->string('category')->nullable(); // e.g. 'Web Dev', 'IoT & Embedded', 'Mobile App', 'Robotika'
            $table->text('description')->nullable();
            $table->string('tech_stack')->nullable(); // e.g. 'Laravel, React, PostgreSQL' or 'Arduino, C++, ESP32'
            $table->string('project_url')->nullable();
            $table->string('repository_url')->nullable();
            $table->string('file_path')->nullable();
            $table->date('completion_date')->nullable();
            $table->timestamps();
        });

        // 5. Assessments (Sesi Asesmen Gemini AI)
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('field', ['it', 'elektro'])->default('it');
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->integer('total_questions')->default(10);
            $table->integer('correct_answers')->default(0);
            $table->decimal('hard_skill_score', 5, 2)->default(0);
            $table->decimal('soft_skill_score', 5, 2)->default(0);
            $table->decimal('total_score', 5, 2)->default(0);
            $table->integer('employability_score')->default(0);
            $table->text('ai_feedback')->nullable();
            $table->text('ai_strengths')->nullable();
            $table->text('ai_weaknesses')->nullable();
            $table->timestamps();
        });

        // 6. Assessment Questions (Pertanyaan Dinamis Hasil Generate Gemini AI)
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->text('question_text');
            $table->enum('type', ['hard_skill', 'soft_skill'])->default('hard_skill');
            $table->string('topic')->nullable(); // e.g. 'Backend Logic', 'IoT Sensors', 'Troubleshooting'
            $table->json('options'); // {"A": "...", "B": "...", "C": "...", "D": "..."}
            $table->string('correct_option', 5); // 'A', 'B', 'C', 'D'
            $table->string('user_answer', 5)->nullable();
            $table->boolean('is_correct')->nullable();
            $table->text('explanation')->nullable();
            $table->timestamps();
        });

        // 7. Skill Gap Results (Hasil Perhitungan Celah Kompetensi)
        Schema::create('skill_gap_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assessment_id')->nullable()->constrained('assessments')->nullOnDelete();
            $table->foreignId('occupation_id')->constrained('occupations')->cascadeOnDelete();
            $table->decimal('match_percentage', 5, 2)->default(0); // e.g. 78.50%
            $table->decimal('gap_score', 5, 2)->default(0);
            $table->json('gap_details')->nullable(); // Breakdown tiap skill: user_level, benchmark, gap
            $table->timestamps();
        });

        // 8. Career Roadmaps (Jalur Pembelajaran Terstruktur)
        Schema::create('career_roadmaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('occupation_id')->constrained('occupations')->cascadeOnDelete();
            $table->string('milestone_title'); // e.g. 'Tahap 1: Penguatan Fundamental'
            $table->integer('stage_order')->default(1);
            $table->text('description')->nullable();
            $table->json('action_items')->nullable(); // [{"task": "...", "status": "completed"|"pending"}]
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('career_roadmaps');
        Schema::dropIfExists('skill_gap_results');
        Schema::dropIfExists('assessment_questions');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('portfolios');
        Schema::dropIfExists('student_profiles');
        Schema::dropIfExists('occupation_skills');
        Schema::dropIfExists('occupations');
    }
};
