<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('semesters')) {
            Schema::create('semesters', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->nullable();
                $table->integer('order')->default(1);
                $table->boolean('is_active')->default(false)->index();
                $table->text('description')->nullable();
                $table->timestamps();
            });

            // Seed default 4 semesters
            $now = now();
            DB::table('semesters')->insert([
                [
                    'name' => 'Tengah Semester Ganjil',
                    'code' => 'PTS-1',
                    'order' => 1,
                    'is_active' => false,
                    'description' => 'Penilaian Tengah Semester 1 (Ganjil)',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Semester Ganjil',
                    'code' => 'PAS-1',
                    'order' => 2,
                    'is_active' => true, // Default active
                    'description' => 'Laporan Hasil Belajar Akhir Semester 1 (Ganjil)',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Tengah Semester Genap',
                    'code' => 'PTS-2',
                    'order' => 3,
                    'is_active' => false,
                    'description' => 'Penilaian Tengah Semester 2 (Genap)',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Semester Genap',
                    'code' => 'PAT-2',
                    'order' => 4,
                    'is_active' => false,
                    'description' => 'Laporan Hasil Belajar Akhir Semester 2 (Genap) & Kenaikan Kelas / Kelulusan',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
