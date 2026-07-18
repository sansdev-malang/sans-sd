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
        Schema::create('bonus_schemas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('bonus_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bonus_schema_id')->constrained('bonus_schemas')->onDelete('cascade');
            $table->unsignedInteger('tier_level');
            $table->decimal('nominal', 10, 2);
            $table->unsignedInteger('max_late_minutes');
            $table->unsignedInteger('max_absent_days')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonus_tiers');
        Schema::dropIfExists('bonus_schemas');
    }
};
