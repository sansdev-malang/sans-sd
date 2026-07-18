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
        Schema::table('employees', function (Blueprint $table) {
            // Change zkteco_uid from integer to string
            $table->string('zkteco_uid')->nullable()->change();
            
            // Add employee_type_id foreign key
            $table->foreignId('employee_type_id')->nullable()->constrained('employee_types')->nullOnDelete();
            
            // Drop enum type column
            $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->integer('zkteco_uid')->nullable()->change();
            $table->dropForeign(['employee_type_id']);
            $table->dropColumn('employee_type_id');
            $table->enum('type', ['teacher', 'employee'])->default('employee');
        });
    }
};
