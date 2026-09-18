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
        Schema::table('bonus_schemas', function (Blueprint $table) {
            if (!Schema::hasColumn('bonus_schemas', 'calculation_mode')) {
                $table->string('calculation_mode')->default('early_arrival')->after('is_active');
            }
        });

        Schema::table('bonus_tiers', function (Blueprint $table) {
            if (!Schema::hasColumn('bonus_tiers', 'min_early_minutes')) {
                $table->unsignedInteger('min_early_minutes')->default(0)->after('nominal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bonus_tiers', function (Blueprint $table) {
            if (Schema::hasColumn('bonus_tiers', 'min_early_minutes')) {
                $table->dropColumn('min_early_minutes');
            }
        });

        Schema::table('bonus_schemas', function (Blueprint $table) {
            if (Schema::hasColumn('bonus_schemas', 'calculation_mode')) {
                $table->dropColumn('calculation_mode');
            }
        });
    }
};
