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
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'unit')) {
                $table->string('unit', 20)->default('sd')->after('id')->index();
            }
            if (!Schema::hasColumn('subjects', 'short_name')) {
                $table->string('short_name', 50)->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'short_name')) {
                $table->dropColumn('short_name');
            }
            if (Schema::hasColumn('subjects', 'unit')) {
                $table->dropColumn('unit');
            }
        });
    }
};

