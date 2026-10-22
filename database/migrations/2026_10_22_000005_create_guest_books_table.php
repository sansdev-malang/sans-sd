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
        if (!Schema::hasTable('guest_books')) {
            Schema::create('guest_books', function (Blueprint $table) {
                $table->id();
                $table->string('name');                 // Nama Tamu
                $table->string('phone');                // No HP / WhatsApp
                $table->string('student_name')->nullable();
                $table->date('student_birth_date')->nullable();
                $table->string('therapy_history', 20)->nullable(); // 'Ya' / 'Tidak'
                $table->string('therapy_diagnosis')->nullable();
                $table->string('category', 50);         // Kategori: Umum, Dinas, Puskesmas/Dinkes, SPMB
                $table->string('institution')->nullable(); // Institusi / Instansi
                $table->string('position')->nullable();    // Jabatan
                $table->string('occupation')->nullable();  // Pekerjaan
                $table->text('address')->nullable();       // Alamat
                $table->string('city')->nullable();        // Kota / Kabupaten
                $table->string('province')->nullable();    // Provinsi
                $table->text('purpose')->nullable();       // Keperluan
                $table->string('host_recipient')->nullable(); // Penerima Tamu / Yang Ditemui
                $table->text('feedback')->nullable();      // Kesan & Pesan
                $table->date('date');                      // Tanggal Kunjungan
                $table->time('time')->nullable();          // Waktu / Jam Datang
                $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['date']);
                $table->index(['category']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_books');
    }
};
