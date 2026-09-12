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
        Schema::create('spmb_candidates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('spmb_registration_id')->unique();
            $table->string('registration_number')->nullable()->index();
            $table->string('full_name')->index();
            $table->string('nickname')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('nik', 30)->nullable()->index();
            $table->string('nisn', 30)->nullable();
            $table->string('child_number')->nullable();
            $table->string('siblings_count')->nullable();

            // Academic & Unit Target
            $table->string('target_unit', 50)->default('SD')->index();
            $table->string('target_class', 100)->nullable();
            $table->string('academic_year', 50)->nullable()->index();
            $table->string('wave', 100)->nullable();

            // Contact & Parents
            $table->string('parent_phone', 50)->nullable()->index();
            $table->string('father_name')->nullable();
            $table->string('father_job')->nullable();
            $table->string('father_phone', 50)->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_job')->nullable();
            $table->string('mother_phone', 50)->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone', 50)->nullable();

            // Address
            $table->text('address')->nullable();
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->string('village', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 20)->nullable();

            // School Origin
            $table->string('previous_school')->nullable();
            $table->string('previous_school_npsn', 30)->nullable();
            $table->text('previous_school_address')->nullable();

            // Statuses from SPMB
            $table->string('spmb_status', 50)->default('verified')->index(); // verified, accepted, completed
            $table->string('spmb_payment_status', 50)->default('unpaid')->index(); // unpaid, partial, paid
            $table->timestamp('spmb_verified_at')->nullable();
            $table->timestamp('spmb_registered_at')->nullable();

            // Unit School Status
            $table->boolean('is_active_student')->default(false)->index();
            $table->string('assigned_class')->nullable();
            $table->timestamp('activated_at')->nullable();

            // Payload & Documents snapshot
            $table->json('documents')->nullable();
            $table->json('payments_data')->nullable();
            $table->json('raw_payload')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spmb_candidates');
    }
};
