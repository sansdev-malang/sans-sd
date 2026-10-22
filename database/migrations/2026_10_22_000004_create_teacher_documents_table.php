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
        if (!Schema::hasTable('teacher_documents')) {
            Schema::create('teacher_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('teacher_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
                $table->string('semester', 20)->default('Tahunan'); // Ganjil, Genap, Tahunan
                $table->string('category', 50); // RKAS, LPJ, PROTA, PROSEM, RPP/Modul Ajar, Silabus/ATP, Lainnya
                $table->string('title');
                $table->json('files')->nullable();
                $table->string('file_path')->nullable();
                $table->string('file_name')->nullable();
                $table->unsignedBigInteger('file_size')->nullable();
                $table->string('file_extension', 20)->nullable();
                $table->text('notes')->nullable();
                $table->string('status', 30)->default('Menunggu Review'); // Menunggu Review, Disetujui, Perlu Revisi
                $table->text('feedback')->nullable();
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();

                $table->index(['teacher_id', 'academic_year_id']);
                $table->index(['category', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_documents');
    }
};
