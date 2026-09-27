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
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'gpk_employee_id')) {
                $table->foreignId('gpk_employee_id')
                    ->nullable()
                    ->after('special_needs_notes')
                    ->constrained('employees')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'gpk_employee_id')) {
                $table->dropForeign(['gpk_employee_id']);
                $table->dropColumn('gpk_employee_id');
            }
        });
    }
};
