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
        Schema::table('homeroom_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('homeroom_assignments', 'subject_id')) {
                $table->foreignId('subject_id')->nullable()->after('classroom_id')->constrained('subjects')->nullOnDelete();
            }
            if (!Schema::hasColumn('homeroom_assignments', 'semester')) {
                $table->string('semester', 20)->default('all')->after('role'); // all, ganjil, genap
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('homeroom_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('homeroom_assignments', 'subject_id')) {
                $table->dropForeign(['subject_id']);
                $table->dropColumn('subject_id');
            }
            if (Schema::hasColumn('homeroom_assignments', 'semester')) {
                $table->dropColumn('semester');
            }
        });
    }
};

