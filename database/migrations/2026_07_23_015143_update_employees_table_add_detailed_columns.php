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
            $table->string('birth_place')->nullable()->after('gender');
            $table->date('birth_date')->nullable()->after('birth_place');
            $table->string('nik')->nullable()->after('birth_date');
            $table->string('niy')->nullable()->after('nik');
            $table->string('nuptk')->nullable()->after('niy');
            $table->string('no_ukg')->nullable()->after('nuptk');
            $table->string('nrg')->nullable()->after('no_ukg');
            $table->string('pangkat_golongan')->nullable()->after('nrg');
            $table->string('last_education')->nullable()->after('pangkat_golongan');
            $table->string('major')->nullable()->after('last_education');
            $table->string('position')->nullable()->after('major');
            $table->string('additional_position')->nullable()->after('position');
            $table->date('task_start_date')->nullable()->after('additional_position');
            $table->date('appointment_date')->nullable()->after('employment_status');
            $table->date('last_sk_date')->nullable()->after('appointment_date');
            $table->string('last_sk_number')->nullable()->after('last_sk_date');
            $table->string('work_period')->nullable()->after('last_sk_number');
            $table->text('address')->nullable()->after('work_period');
            $table->string('phone')->nullable()->after('address');
            $table->text('notes')->nullable()->after('phone');

            $table->dropColumn('nuptk_nip_nik');
            $table->dropColumn('subject_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'birth_place', 'birth_date', 'nik', 'niy', 'nuptk', 'no_ukg', 'nrg', 
                'pangkat_golongan', 'last_education', 'major', 'position', 
                'additional_position', 'task_start_date', 'appointment_date', 
                'last_sk_date', 'last_sk_number', 'work_period', 'address', 
                'phone', 'notes'
            ]);
            $table->string('nuptk_nip_nik')->nullable();
            $table->string('subject_position')->nullable();
        });
    }
};
