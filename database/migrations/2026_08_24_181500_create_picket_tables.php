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
        Schema::create('picket_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('jobs')->nullable();
            $table->string('duty_hours')->default('06.30 - 07.00');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('picket_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('picket_area_id')->constrained('picket_areas')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 1 = Senin, ..., 6 = Sabtu
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['picket_area_id', 'day_of_week']);
            $table->index('employee_id');
        });

        Schema::create('picket_swaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('employees')->cascadeOnDelete();
            $table->date('requested_date');
            $table->foreignId('target_employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('target_date');
            $table->string('status')->default('pending'); // pending, approved_by_target, approved, rejected
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('requester_id');
            $table->index('target_employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('picket_swaps');
        Schema::dropIfExists('picket_schedules');
        Schema::dropIfExists('picket_areas');
    }
};
