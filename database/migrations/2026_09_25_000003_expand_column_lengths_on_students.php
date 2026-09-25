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
            $table->string('citizenship', 150)->nullable()->change();
            $table->string('birth_certificate_no', 255)->nullable()->change();
            $table->string('special_needs_type', 255)->nullable()->change();
            $table->string('residence_status', 100)->nullable()->change();
            $table->string('distance_to_school', 100)->nullable()->change();
            $table->string('home_phone', 100)->nullable()->change();
            $table->string('home_language', 150)->nullable()->change();
            $table->string('father_phone', 100)->nullable()->change();
            $table->string('mother_phone', 100)->nullable()->change();
            $table->string('guardian_phone', 100)->nullable()->change();
            $table->string('parent_phone', 100)->nullable()->change();
            $table->string('father_income', 100)->nullable()->change();
            $table->string('mother_income', 100)->nullable()->change();
            $table->string('father_education', 100)->nullable()->change();
            $table->string('mother_education', 100)->nullable()->change();
            $table->string('guardian_education', 100)->nullable()->change();
            $table->string('sttb_number_date', 255)->nullable()->change();
            $table->string('weight', 50)->nullable()->change();
            $table->string('height', 50)->nullable()->change();
            $table->string('blood_type', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
