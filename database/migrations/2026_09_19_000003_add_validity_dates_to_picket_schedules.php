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
        Schema::table('picket_schedules', function (Blueprint $table) {
            $table->date('start_date')->nullable()->default('2026-07-01')->after('employee_id');
            $table->date('end_date')->nullable()->default('2027-06-30')->after('start_date');
            $table->index(['start_date', 'end_date']);
        });

        // Ensure all existing rows have the academic year dates
        DB::table('picket_schedules')->whereNull('start_date')->update([
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('picket_schedules', function (Blueprint $table) {
            $table->dropIndex(['start_date', 'end_date']);
            $table->dropColumn(['start_date', 'end_date']);
        });
    }
};
