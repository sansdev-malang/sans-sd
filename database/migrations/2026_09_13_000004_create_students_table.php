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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            
            // Relasi & Nomor Identitas
            $table->string('nis')->unique()->index();                 // Nomor Induk Siswa (e.g. 27.SD.001)
            $table->string('nisn')->nullable()->index();
            $table->string('nik')->nullable()->index();
            $table->foreignId('spmb_candidate_id')->nullable()->constrained('spmb_candidates')->nullOnDelete();
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();

            // Biodata Siswa
            $table->string('full_name');
            $table->string('nickname')->nullable();
            $table->string('gender')->nullable();                     // L / P
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('religion')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('previous_school')->nullable();
            $table->text('student_photo_url')->nullable();
            
            // Data Orang Tua / Kontak
            $table->string('father_name')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('father_job')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('mother_job')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('parent_phone')->nullable();               // Primary WhatsApp
            $table->string('parent_email')->nullable();
            
            // Berkas / Dokumen Lampiran (JSON format)
            $table->json('documents')->nullable();
            
            // Status Kesiswaan
            $table->enum('status', ['aktif', 'lulus', 'mutasi', 'keluar', 'nonaktif'])->default('aktif')->index();
            $table->date('enrolled_date')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
