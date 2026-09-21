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
        if (!Schema::hasColumn('working_shifts', 'is_active')) {
            Schema::table('working_shifts', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('is_shift')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('working_shifts', 'is_active')) {
            Schema::table('working_shifts', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
