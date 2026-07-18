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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('nuptk_nip_nik')->nullable();
            $table->enum('type', ['teacher', 'employee']);
            $table->enum('unit', ['paud', 'sd', 'smp']);
            $table->string('subject_position')->nullable();
            $table->enum('gender', ['Male', 'Female']);
            $table->string('employment_status')->nullable();
            $table->integer('zkteco_uid')->unique()->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['Active', 'Leave', 'Inactive'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
