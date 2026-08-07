<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create leave_types table
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('status_code', ['S', 'I', 'C', 'H'])->default('I');
            $table->boolean('gets_presence_bonus')->default(false);
            $table->timestamps();
        });

        // 2. Seed initial leave types
        $initialTypes = [
            [
                'name' => 'Izin Sakit Pribadi',
                'code' => 'sakit-pribadi',
                'status_code' => 'S',
                'gets_presence_bonus' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Izin Tidak Bekerja',
                'code' => 'tidak-bekerja',
                'status_code' => 'I',
                'gets_presence_bonus' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Izin Sakit Keluarga (Anak/Orangtua/Suami/Istri)',
                'code' => 'sakit-keluarga',
                'status_code' => 'I',
                'gets_presence_bonus' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Izin Cuti',
                'code' => 'cuti-tahunan',
                'status_code' => 'C',
                'gets_presence_bonus' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Izin Terlambat',
                'code' => 'izin-terlambat',
                'status_code' => 'H',
                'gets_presence_bonus' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Izin Pulang Lebih Awal',
                'code' => 'pulang-awal',
                'status_code' => 'H',
                'gets_presence_bonus' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Izin Keluar Pada Jam Efektif',
                'code' => 'keluar-jam-efektif',
                'status_code' => 'H',
                'gets_presence_bonus' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Izin Keluar Pribadi',
                'code' => 'keluar-pribadi',
                'status_code' => 'H',
                'gets_presence_bonus' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Izin Kedinasan Pagi',
                'code' => 'dinas-pagi',
                'status_code' => 'H',
                'gets_presence_bonus' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Izin Kedinasan Siang',
                'code' => 'dinas-siang',
                'status_code' => 'H',
                'gets_presence_bonus' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Izin Kedinasan Luar',
                'code' => 'dinas-luar',
                'status_code' => 'H',
                'gets_presence_bonus' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('leave_types')->insert($initialTypes);

        // 3. Add leave_type_id to leave_requests
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->foreignId('leave_type_id')->nullable()->after('employee_id')->constrained('leave_types')->onDelete('set null');
        });

        // 4. Map existing legacy leave requests
        $types = DB::table('leave_types')->get()->keyBy('status_code');
        $sakitId = $types->get('S')?->id;
        $izinId = $types->get('I')?->id;
        $cutiId = $types->get('C')?->id;
        $dinasId = DB::table('leave_types')->where('code', 'dinas-luar')->value('id');

        DB::table('leave_requests')->where('type', 'Sakit')->update(['leave_type_id' => $sakitId]);
        DB::table('leave_requests')->where('type', 'Izin')->update(['leave_type_id' => $izinId]);
        DB::table('leave_requests')->where('type', 'Cuti')->update(['leave_type_id' => $cutiId]);
        DB::table('leave_requests')->where('type', 'Dinas')->update(['leave_type_id' => $dinasId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['leave_type_id']);
            $table->dropColumn('leave_type_id');
        });

        Schema::dropIfExists('leave_types');
    }
};
