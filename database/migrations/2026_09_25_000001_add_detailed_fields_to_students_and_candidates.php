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
        // 1. Tambah detail lengkap ke tabel students
        Schema::table('students', function (Blueprint $table) {
            // Legalitas & Berkas
            if (!Schema::hasColumn('students', 'no_kk')) {
                $table->string('no_kk', 30)->nullable()->after('nik')->index();
            }
            if (!Schema::hasColumn('students', 'birth_certificate_no')) {
                $table->string('birth_certificate_no', 100)->nullable()->after('no_kk');
            }
            if (!Schema::hasColumn('students', 'citizenship')) {
                $table->string('citizenship', 50)->default('WNI')->after('religion');
            }
            if (!Schema::hasColumn('students', 'checklist_documents')) {
                $table->json('checklist_documents')->nullable()->after('documents');
            }

            // Inklusi & Kekhususan
            if (!Schema::hasColumn('students', 'student_type')) {
                $table->string('student_type', 50)->default('REGULER')->after('gender')->index();
            }
            if (!Schema::hasColumn('students', 'special_needs_type')) {
                $table->string('special_needs_type', 255)->nullable()->after('student_type');
            }
            if (!Schema::hasColumn('students', 'special_needs_notes')) {
                $table->text('special_needs_notes')->nullable()->after('special_needs_type');
            }

            // Alamat & Domisili Detail
            if (!Schema::hasColumn('students', 'rt')) {
                $table->string('rt', 10)->nullable()->after('address');
            }
            if (!Schema::hasColumn('students', 'rw')) {
                $table->string('rw', 10)->nullable()->after('rt');
            }
            if (!Schema::hasColumn('students', 'village')) {
                $table->string('village', 100)->nullable()->after('rw');
            }
            if (!Schema::hasColumn('students', 'district')) {
                $table->string('district', 100)->nullable()->after('village');
            }
            if (!Schema::hasColumn('students', 'district_category')) {
                $table->string('district_category', 100)->nullable()->after('district');
            }
            if (!Schema::hasColumn('students', 'postal_code')) {
                $table->string('postal_code', 20)->nullable()->after('province');
            }
            if (!Schema::hasColumn('students', 'residence_status')) {
                $table->string('residence_status', 50)->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('students', 'distance_to_school')) {
                $table->string('distance_to_school', 50)->nullable()->after('residence_status');
            }
            if (!Schema::hasColumn('students', 'home_phone')) {
                $table->string('home_phone', 50)->nullable()->after('distance_to_school');
            }

            // Keluarga & Saudara
            if (!Schema::hasColumn('students', 'child_number')) {
                $table->integer('child_number')->nullable()->after('home_phone');
            }
            if (!Schema::hasColumn('students', 'siblings_count')) {
                $table->integer('siblings_count')->nullable()->after('child_number');
            }
            if (!Schema::hasColumn('students', 'step_siblings_count')) {
                $table->integer('step_siblings_count')->nullable()->after('siblings_count');
            }
            if (!Schema::hasColumn('students', 'adoptive_siblings_count')) {
                $table->integer('adoptive_siblings_count')->nullable()->after('step_siblings_count');
            }
            if (!Schema::hasColumn('students', 'home_language')) {
                $table->string('home_language', 100)->nullable()->after('adoptive_siblings_count');
            }

            // Fisik, Kesehatan, UKS
            if (!Schema::hasColumn('students', 'weight')) {
                $table->string('weight', 20)->nullable()->after('home_language');
            }
            if (!Schema::hasColumn('students', 'height')) {
                $table->string('height', 20)->nullable()->after('weight');
            }
            if (!Schema::hasColumn('students', 'blood_type')) {
                $table->string('blood_type', 10)->nullable()->after('height');
            }
            if (!Schema::hasColumn('students', 'severe_disease_history')) {
                $table->text('severe_disease_history')->nullable()->after('blood_type');
            }
            if (!Schema::hasColumn('students', 'frequent_disease')) {
                $table->text('frequent_disease')->nullable()->after('severe_disease_history');
            }

            // Detail Ayah
            if (!Schema::hasColumn('students', 'father_nik')) {
                $table->string('father_nik', 30)->nullable()->after('father_name');
            }
            if (!Schema::hasColumn('students', 'father_birth_place')) {
                $table->string('father_birth_place', 100)->nullable()->after('father_nik');
            }
            if (!Schema::hasColumn('students', 'father_birth_date')) {
                $table->date('father_birth_date')->nullable()->after('father_birth_place');
            }
            if (!Schema::hasColumn('students', 'father_religion')) {
                $table->string('father_religion', 50)->nullable()->after('father_birth_date');
            }
            if (!Schema::hasColumn('students', 'father_education')) {
                $table->string('father_education', 50)->nullable()->after('father_religion');
            }
            if (!Schema::hasColumn('students', 'father_company')) {
                $table->string('father_company', 255)->nullable()->after('father_job');
            }
            if (!Schema::hasColumn('students', 'father_company_address')) {
                $table->text('father_company_address')->nullable()->after('father_company');
            }
            if (!Schema::hasColumn('students', 'father_company_phone')) {
                $table->string('father_company_phone', 50)->nullable()->after('father_company_address');
            }
            if (!Schema::hasColumn('students', 'father_income')) {
                $table->string('father_income', 50)->nullable()->after('father_company_phone');
            }
            if (!Schema::hasColumn('students', 'father_email')) {
                $table->string('father_email', 100)->nullable()->after('father_income');
            }

            // Detail Ibu
            if (!Schema::hasColumn('students', 'mother_nik')) {
                $table->string('mother_nik', 30)->nullable()->after('mother_name');
            }
            if (!Schema::hasColumn('students', 'mother_birth_place')) {
                $table->string('mother_birth_place', 100)->nullable()->after('mother_nik');
            }
            if (!Schema::hasColumn('students', 'mother_birth_date')) {
                $table->date('mother_birth_date')->nullable()->after('mother_birth_place');
            }
            if (!Schema::hasColumn('students', 'mother_religion')) {
                $table->string('mother_religion', 50)->nullable()->after('mother_birth_date');
            }
            if (!Schema::hasColumn('students', 'mother_education')) {
                $table->string('mother_education', 50)->nullable()->after('mother_religion');
            }
            if (!Schema::hasColumn('students', 'mother_company')) {
                $table->string('mother_company', 255)->nullable()->after('mother_job');
            }
            if (!Schema::hasColumn('students', 'mother_company_address')) {
                $table->text('mother_company_address')->nullable()->after('mother_company');
            }
            if (!Schema::hasColumn('students', 'mother_company_phone')) {
                $table->string('mother_company_phone', 50)->nullable()->after('mother_company_address');
            }
            if (!Schema::hasColumn('students', 'mother_income')) {
                $table->string('mother_income', 50)->nullable()->after('mother_company_phone');
            }
            if (!Schema::hasColumn('students', 'mother_email')) {
                $table->string('mother_email', 100)->nullable()->after('mother_income');
            }

            // Detail Wali
            if (!Schema::hasColumn('students', 'guardian_relation')) {
                $table->string('guardian_relation', 100)->nullable()->after('guardian_name');
            }
            if (!Schema::hasColumn('students', 'guardian_birth_place')) {
                $table->string('guardian_birth_place', 100)->nullable()->after('guardian_relation');
            }
            if (!Schema::hasColumn('students', 'guardian_birth_date')) {
                $table->date('guardian_birth_date')->nullable()->after('guardian_birth_place');
            }
            if (!Schema::hasColumn('students', 'guardian_education')) {
                $table->string('guardian_education', 50)->nullable()->after('guardian_birth_date');
            }
            if (!Schema::hasColumn('students', 'guardian_job')) {
                $table->string('guardian_job', 100)->nullable()->after('guardian_education');
            }
            if (!Schema::hasColumn('students', 'guardian_religion')) {
                $table->string('guardian_religion', 50)->nullable()->after('guardian_job');
            }
            if (!Schema::hasColumn('students', 'guardian_address')) {
                $table->text('guardian_address')->nullable()->after('guardian_religion');
            }

            // Asal Sekolah & Kelulusan
            if (!Schema::hasColumn('students', 'origin_category')) {
                $table->string('origin_category', 50)->nullable()->after('previous_school');
            }
            if (!Schema::hasColumn('students', 'previous_school_address')) {
                $table->text('previous_school_address')->nullable()->after('origin_category');
            }
            if (!Schema::hasColumn('students', 'sttb_number_date')) {
                $table->string('sttb_number_date', 100)->nullable()->after('previous_school_address');
            }
            if (!Schema::hasColumn('students', 'graduation_year')) {
                $table->string('graduation_year', 20)->nullable()->after('notes');
            }
            if (!Schema::hasColumn('students', 'diploma_number')) {
                $table->string('diploma_number', 100)->nullable()->after('graduation_year');
            }
        });

        // 2. Tambah kolom pendukung ke spmb_candidates agar sinkron
        Schema::table('spmb_candidates', function (Blueprint $table) {
            if (!Schema::hasColumn('spmb_candidates', 'no_kk')) {
                $table->string('no_kk', 30)->nullable()->after('nik');
            }
            if (!Schema::hasColumn('spmb_candidates', 'student_type')) {
                $table->string('student_type', 50)->default('REGULER')->after('target_class');
            }
            if (!Schema::hasColumn('spmb_candidates', 'special_needs_type')) {
                $table->string('special_needs_type', 255)->nullable()->after('student_type');
            }
            if (!Schema::hasColumn('spmb_candidates', 'father_nik')) {
                $table->string('father_nik', 30)->nullable()->after('father_name');
            }
            if (!Schema::hasColumn('spmb_candidates', 'mother_nik')) {
                $table->string('mother_nik', 30)->nullable()->after('mother_name');
            }
            if (!Schema::hasColumn('spmb_candidates', 'father_education')) {
                $table->string('father_education', 50)->nullable()->after('father_job');
            }
            if (!Schema::hasColumn('spmb_candidates', 'mother_education')) {
                $table->string('mother_education', 50)->nullable()->after('mother_job');
            }
            if (!Schema::hasColumn('spmb_candidates', 'blood_type')) {
                $table->string('blood_type', 10)->nullable()->after('siblings_count');
            }
            if (!Schema::hasColumn('spmb_candidates', 'weight')) {
                $table->string('weight', 20)->nullable()->after('blood_type');
            }
            if (!Schema::hasColumn('spmb_candidates', 'height')) {
                $table->string('height', 20)->nullable()->after('weight');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe rollback if needed
    }
};
