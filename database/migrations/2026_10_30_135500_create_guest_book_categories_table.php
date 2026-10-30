<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('guest_book_categories')) {
            Schema::create('guest_book_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->unique();
                $table->string('slug', 100)->unique();
                $table->string('sub_label', 150)->nullable();
                $table->string('icon', 50)->default('briefcase');
                $table->string('color', 50)->default('teal');
                $table->string('form_type', 50)->default('umum');
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            // Seed initial 8 categories
            $now = now();
            DB::table('guest_book_categories')->insert([
                [
                    'name' => 'Umum',
                    'slug' => 'umum',
                    'sub_label' => 'Umum / Mitra / Wali',
                    'icon' => 'users',
                    'color' => 'slate',
                    'form_type' => 'umum',
                    'order' => 1,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Dinas',
                    'slug' => 'dinas',
                    'sub_label' => 'Kedinasan / Pengawas',
                    'icon' => 'landmark',
                    'color' => 'indigo',
                    'form_type' => 'dinas',
                    'order' => 2,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Puskesmas/Dinkes',
                    'slug' => 'puskesmas-dinkes',
                    'sub_label' => 'Pelayanan Kesehatan',
                    'icon' => 'activity',
                    'color' => 'emerald',
                    'form_type' => 'puskesmas',
                    'order' => 3,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'SPMB',
                    'slug' => 'spmb',
                    'sub_label' => 'Calon Wali Murid',
                    'icon' => 'user-plus',
                    'color' => 'amber',
                    'form_type' => 'spmb',
                    'order' => 4,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Offering/Marketing',
                    'slug' => 'offering-marketing',
                    'sub_label' => 'Vendor & Penawaran',
                    'icon' => 'briefcase',
                    'color' => 'violet',
                    'form_type' => 'offering',
                    'order' => 5,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Penelitian/Observasi',
                    'slug' => 'penelitian-observasi',
                    'sub_label' => 'Riset & Observasi',
                    'icon' => 'graduation-cap',
                    'color' => 'cyan',
                    'form_type' => 'penelitian',
                    'order' => 6,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Magang',
                    'slug' => 'magang',
                    'sub_label' => 'PKL / PPL / Magang',
                    'icon' => 'user-check',
                    'color' => 'rose',
                    'form_type' => 'magang',
                    'order' => 7,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Studi Banding/Studi Tiru',
                    'slug' => 'studi-banding-studi-tiru',
                    'sub_label' => 'Studi Tiru Antar Sekolah',
                    'icon' => 'building-2',
                    'color' => 'sky',
                    'form_type' => 'studi_banding',
                    'order' => 8,
                    'is_active' => true,
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
        Schema::dropIfExists('guest_book_categories');
    }
};

