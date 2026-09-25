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
        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropUnique('academic_years_name_unique');
            $table->unique(['name', 'semester'], 'academic_years_name_semester_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropUnique('academic_years_name_semester_unique');
            $table->unique('name', 'academic_years_name_unique');
        });
    }
};
