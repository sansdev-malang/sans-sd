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
        Schema::create('student_classroom_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->nullOnDelete();
            
            // Snapshot data pada saat tahun ajaran tersebut
            $table->string('grade_level', 20)->nullable();             // Contoh: '1', '2', ..., '6'
            $table->string('classroom_name', 100)->nullable();         // Contoh: '1A (Berlian)'
            $table->string('homeroom_teacher_name')->nullable();       // Nama Wali Kelas
            $table->string('gpk_teacher_name')->nullable();            // Nama GPK (untuk siswa inklusi)
            $table->enum('status', ['aktif', 'naik_kelas', 'tinggal_kelas', 'lulus', 'mutasi_keluar', 'pindah_rombel'])->default('aktif')->index();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            
            $table->index(['student_id', 'academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_classroom_histories');
    }
};
