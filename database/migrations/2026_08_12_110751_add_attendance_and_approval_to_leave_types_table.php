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
        Schema::table('leave_types', function (Blueprint $table) {
            $table->boolean('requires_attendance')->default(true)->after('gets_presence_bonus');
            $table->boolean('requires_approval')->default(true)->after('requires_attendance');
        });

        // Set default configurations for default system types
        DB::table('leave_types')->whereIn('code', ['sakit-pribadi', 'tidak-bekerja', 'cuti-tahunan'])->update([
            'requires_attendance' => false,
            'requires_approval' => true,
        ]);

        DB::table('leave_types')->where('code', 'dinas-luar')->update([
            'requires_attendance' => false,
            'requires_approval' => false,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->dropColumn(['requires_attendance', 'requires_approval']);
        });
    }
};
