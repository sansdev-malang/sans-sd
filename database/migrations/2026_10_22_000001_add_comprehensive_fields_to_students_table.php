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
        try {
            \Illuminate\Support\Facades\DB::statement("SET SESSION innodb_strict_mode = 0");
        } catch (\Throwable $e) {
            // Ignored if user lacks SESSION_VARIABLES_ADMIN privilege on production
        }

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE students ROW_FORMAT=DYNAMIC");
        } catch (\Throwable $e) {
            // Ignored if not permitted or already dynamic
        }

        Schema::table('students', function (Blueprint $table) {
            // Gelar / Salutation
            if (!Schema::hasColumn('students', 'father_title_prefix')) {
                $table->string('father_title_prefix', 30)->nullable()->after('father_name');
            }
            if (!Schema::hasColumn('students', 'father_title_suffix')) {
                $table->string('father_title_suffix', 50)->nullable()->after('father_title_prefix');
            }
            if (!Schema::hasColumn('students', 'mother_title_prefix')) {
                $table->string('mother_title_prefix', 30)->nullable()->after('mother_name');
            }
            if (!Schema::hasColumn('students', 'mother_title_suffix')) {
                $table->string('mother_title_suffix', 50)->nullable()->after('mother_title_prefix');
            }
            if (!Schema::hasColumn('students', 'guardian_title_prefix')) {
                $table->string('guardian_title_prefix', 30)->nullable()->after('guardian_name');
            }
            if (!Schema::hasColumn('students', 'guardian_title_suffix')) {
                $table->string('guardian_title_suffix', 50)->nullable()->after('guardian_title_prefix');
            }

            // Fisik, Ciri & Etnis
            if (!Schema::hasColumn('students', 'ethnic_group')) {
                $table->string('ethnic_group', 100)->nullable()->after('home_language');
            }
            if (!Schema::hasColumn('students', 'skin_color')) {
                $table->string('skin_color', 50)->nullable()->after('blood_type');
            }
            if (!Schema::hasColumn('students', 'hair_type')) {
                $table->string('hair_type', 50)->nullable()->after('skin_color');
            }
            if (!Schema::hasColumn('students', 'hair_color')) {
                $table->string('hair_color', 50)->nullable()->after('hair_type');
            }

            // Ortu Alamat & Media Sosial
            if (!Schema::hasColumn('students', 'father_address')) {
                $table->text('father_address')->nullable()->after('father_email');
            }
            if (!Schema::hasColumn('students', 'father_social_media')) {
                $table->string('father_social_media', 150)->nullable()->after('father_address');
            }
            if (!Schema::hasColumn('students', 'mother_address')) {
                $table->text('mother_address')->nullable()->after('mother_email');
            }
            if (!Schema::hasColumn('students', 'mother_social_media')) {
                $table->string('mother_social_media', 150)->nullable()->after('mother_address');
            }

            // Wali Detail
            if (!Schema::hasColumn('students', 'guardian_nik')) {
                $table->string('guardian_nik', 30)->nullable()->after('guardian_relation');
            }
            if (!Schema::hasColumn('students', 'is_accompanied_by_guardian')) {
                $table->boolean('is_accompanied_by_guardian')->nullable()->default(false)->after('guardian_nik');
            }
            if (!Schema::hasColumn('students', 'guardian_company')) {
                $table->string('guardian_company', 255)->nullable()->after('guardian_job');
            }
            if (!Schema::hasColumn('students', 'guardian_company_address')) {
                $table->text('guardian_company_address')->nullable()->after('guardian_company');
            }
            if (!Schema::hasColumn('students', 'guardian_company_phone')) {
                $table->string('guardian_company_phone', 50)->nullable()->after('guardian_company_address');
            }
            if (!Schema::hasColumn('students', 'guardian_income')) {
                $table->string('guardian_income', 100)->nullable()->after('guardian_company_phone');
            }
            if (!Schema::hasColumn('students', 'guardian_email')) {
                $table->string('guardian_email', 150)->nullable()->after('guardian_income');
            }
            if (!Schema::hasColumn('students', 'guardian_social_media')) {
                $table->string('guardian_social_media', 150)->nullable()->after('guardian_email');
            }

            // Domisili & Transportasi Tambahan
            if (!Schema::hasColumn('students', 'dusun')) {
                $table->string('dusun', 100)->nullable()->after('address');
            }
            if (!Schema::hasColumn('students', 'living_with')) {
                $table->string('living_with', 100)->nullable()->after('residence_status');
            }
            if (!Schema::hasColumn('students', 'transportation')) {
                $table->string('transportation', 100)->nullable()->after('distance_to_school');
            }
            if (!Schema::hasColumn('students', 'travel_time_minutes')) {
                $table->integer('travel_time_minutes')->nullable()->after('transportation');
            }

            // Kontak Darurat
            if (!Schema::hasColumn('students', 'emergency_contact_name')) {
                $table->string('emergency_contact_name', 255)->nullable()->after('parent_email');
            }
            if (!Schema::hasColumn('students', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone', 50)->nullable()->after('emergency_contact_name');
            }
            if (!Schema::hasColumn('students', 'emergency_contact_relation')) {
                $table->string('emergency_contact_relation', 100)->nullable()->after('emergency_contact_phone');
            }

            // Cita-cita, Hobi, Harapan & Catatan
            if (!Schema::hasColumn('students', 'student_hobby')) {
                $table->string('student_hobby', 255)->nullable()->after('emergency_contact_relation');
            }
            if (!Schema::hasColumn('students', 'student_aspiration')) {
                $table->string('student_aspiration', 255)->nullable()->after('student_hobby');
            }
            if (!Schema::hasColumn('students', 'parent_aspiration')) {
                $table->text('parent_aspiration')->nullable()->after('student_aspiration');
            }
            if (!Schema::hasColumn('students', 'planned_school_after')) {
                $table->string('planned_school_after', 255)->nullable()->after('parent_aspiration');
            }
            if (!Schema::hasColumn('students', 'parent_message')) {
                $table->text('parent_message')->nullable()->after('planned_school_after');
            }
            if (!Schema::hasColumn('students', 'parent_suggestion')) {
                $table->text('parent_suggestion')->nullable()->after('parent_message');
            }

            // Hybrid JSON Fields
            if (!Schema::hasColumn('students', 'family_data')) {
                $table->json('family_data')->nullable()->after('parent_suggestion');
            }
            if (!Schema::hasColumn('students', 'health_habits')) {
                $table->json('health_habits')->nullable()->after('family_data');
            }
            if (!Schema::hasColumn('students', 'survey_answers')) {
                $table->json('survey_answers')->nullable()->after('health_habits');
            }
            if (!Schema::hasColumn('students', 'extra_fields')) {
                $table->json('extra_fields')->nullable()->after('survey_answers');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $cols = [
                'father_title_prefix', 'father_title_suffix', 'mother_title_prefix', 'mother_title_suffix',
                'guardian_title_prefix', 'guardian_title_suffix', 'ethnic_group', 'skin_color', 'hair_type',
                'hair_color', 'father_address', 'father_social_media', 'mother_address', 'mother_social_media',
                'guardian_nik', 'is_accompanied_by_guardian', 'guardian_company', 'guardian_company_address',
                'guardian_company_phone', 'guardian_income', 'guardian_email', 'guardian_social_media',
                'dusun', 'living_with', 'transportation', 'travel_time_minutes', 'emergency_contact_name',
                'emergency_contact_phone', 'emergency_contact_relation', 'student_hobby', 'student_aspiration',
                'parent_aspiration', 'planned_school_after', 'parent_message', 'parent_suggestion',
                'family_data', 'health_habits', 'survey_answers', 'extra_fields'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('students', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
