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
        Schema::table('picket_areas', function (Blueprint $table) {
            if (!Schema::hasColumn('picket_areas', 'start_time')) {
                $table->time('start_time')->default('06:30:00')->after('jobs');
            }
            if (!Schema::hasColumn('picket_areas', 'end_time')) {
                $table->time('end_time')->default('07:00:00')->after('start_time');
            }
        });

        // Ensure all existing picket areas have proper start_time & end_time
        DB::table('picket_areas')->whereNull('start_time')->update(['start_time' => '06:30:00', 'end_time' => '07:00:00']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('picket_areas', function (Blueprint $table) {
            if (Schema::hasColumn('picket_areas', 'end_time')) {
                $table->dropColumn('end_time');
            }
            if (Schema::hasColumn('picket_areas', 'start_time')) {
                $table->dropColumn('start_time');
            }
        });
    }
};
