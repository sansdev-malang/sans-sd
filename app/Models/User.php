<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'employee_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Helper untuk cek role
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array|string $roles): bool
    {
        if (is_string($roles)) {
            $roles = explode(',', $roles);
        }
        return in_array($this->role, $roles);
    }

    /**
     * Check if user has an admin-level role.
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin_sd', 'admin_paud', 'admin_smp', 'kepala_sekolah', 'waka']);
    }

    /**
     * Get a descriptive, human-friendly role name for display in UI, impersonation banner, and profile cards.
     */
    public function getRoleDisplayName(): string
    {
        if ($this->hasRole('super_admin') || $this->role === 'superadmin') {
            return 'Super Admin';
        }
        if ($this->hasRole('admin_sd') || $this->role === 'admin') {
            return 'Admin SD';
        }
        if ($this->hasRole('admin_paud')) {
            return 'Admin PAUD';
        }
        if ($this->hasRole('admin_smp')) {
            return 'Admin SMP';
        }
        if ($this->hasRole('kepala_sekolah')) {
            return 'Kepala Sekolah';
        }
        if ($this->hasRole('waka')) {
            return 'Wakil Kepala Sekolah';
        }

        if ($this->employee_id) {
            return \Illuminate\Support\Facades\Cache::remember('user_role_display_' . $this->id, 60, function () {
                $assignments = \App\Models\HomeroomAssignment::where('employee_id', $this->employee_id)
                    ->where('is_active', true)
                    ->with(['classroom', 'subject'])
                    ->get();

                if ($assignments->isNotEmpty()) {
                    // 1. Wali Kelas
                    $waliKelas = $assignments->firstWhere('role', 'wali_kelas');
                    if ($waliKelas && $waliKelas->classroom) {
                        return 'Wali Kelas ' . $waliKelas->classroom->name;
                    }

                    // 2. Guru Mapel (e.g. Guru PAI, PJOK, etc.)
                    $guruMapel = $assignments->where('role', 'guru_mapel');
                    if ($guruMapel->isNotEmpty()) {
                        $firstMapel = $guruMapel->first();
                        $subjectCode = strtoupper($firstMapel->subject?->code ?? '');
                        $subjectName = $firstMapel->subject?->name ?? '';
                        if ($subjectCode === 'PAI' || str_contains(strtolower($subjectName), 'agama islam')) {
                            return 'Guru PAI';
                        }
                        if ($subjectName) {
                            return 'Guru ' . $subjectName;
                        }
                        return 'Guru Mapel';
                    }

                    // 3. GPQ
                    if ($assignments->contains('role', 'gpq')) {
                        return "Guru Al-Qur'an (GPQ)";
                    }

                    // 4. GPK
                    if ($assignments->contains('role', 'gpk')) {
                        return 'Guru Pendamping Khusus (GPK)';
                    }

                    // 5. Guru Kelas
                    $guruKelas = $assignments->firstWhere('role', 'guru_kelas');
                    if ($guruKelas && $guruKelas->classroom) {
                        return 'Guru Kelas ' . $guruKelas->classroom->name;
                    }

                    // 6. Koordinator / Pendamping Tingkat
                    $tingkat = $assignments->firstWhere('role', 'pendamping_tingkat') ?? $assignments->firstWhere('role', 'koordinator_tingkat');
                    if ($tingkat) {
                        return $tingkat->role_label;
                    }
                }

                // If no assignments or not matched, check employee
                $emp = $this->employee;
                if ($emp) {
                    if ($emp->position && !in_array(strtolower($emp->position), ['employee', 'pegawai', 'staff'])) {
                        return $emp->position;
                    }
                    if ($emp->employeeType?->name) {
                        return $emp->employeeType->name;
                    }
                }

                return 'Pegawai';
            });
        }

        return ucwords(str_replace('_', ' ', (string)$this->role));
    }

    /**
     * Check if the user is currently assigned as an active homeroom teacher in the active academic year.
     */
    public function isActiveHomeroomTeacher(): bool
    {
        if (!$this->employee_id) {
            return false;
        }

        return \Illuminate\Support\Facades\Cache::remember('user_is_active_homeroom_' . $this->employee_id, 60, function () {
            $activeAy = \App\Models\AcademicYear::where('is_active', true)->first();

            // Check Classroom
            $classroomQuery = \App\Models\Classroom::where('homeroom_teacher_id', $this->employee_id);
            if ($activeAy) {
                $classroomQuery->where('academic_year_id', $activeAy->id);
            }
            if ($classroomQuery->exists()) {
                return true;
            }

            // Also check HomeroomAssignment if used
            if (class_exists(\App\Models\HomeroomAssignment::class)) {
                $assignmentQuery = \App\Models\HomeroomAssignment::where('employee_id', $this->employee_id)
                    ->where('is_active', true)
                    ->where('role', 'wali_kelas');
                if ($activeAy) {
                    $assignmentQuery->where('academic_year_id', $activeAy->id);
                }
                if ($assignmentQuery->exists()) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * Check if the user is a teacher / tenaga pendidik (Semua Guru).
     */
    public function isTeacher(): bool
    {
        if (in_array($this->role, ['guru', 'wali_kelas'])) {
            return true;
        }

        if (!$this->employee_id) {
            return false;
        }

        return \Illuminate\Support\Facades\Cache::remember('user_is_teacher_' . $this->employee_id, 300, function () {
            $employee = $this->employee;
            if (!$employee) {
                return false;
            }

            if ($employee->employee_type_id == 1) {
                return true;
            }

            if ($employee->relationLoaded('employeeType') ? ($employee->employeeType?->code === 'teacher') : ($employee->employeeType()->where('code', 'teacher')->exists())) {
                return true;
            }

            $pos = strtolower($employee->position ?? '');
            return str_contains($pos, 'guru') || in_array(strtoupper(trim($employee->position ?? '')), ['GPK', 'GPQ']);
        });
    }

    /**
     * Check if user has an active teaching assignment in homeroom_assignments.
     */
    public function hasActiveTeachingAssignment(): bool
    {
        if (!$this->employee_id) {
            return false;
        }

        return \App\Models\HomeroomAssignment::where('employee_id', $this->employee_id)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Check if the user can access E-Rapor.
     * Hak akses saat ini dibatasi untuk:
     * 1. Super Admin & Admin Sekolah
     * 2. Kepala Sekolah & Waka
     * 3. Wali Kelas
     * 4. Guru: Khusus Guru PAIBP dan GPQ
     */
    public function canAccessRapor(): bool
    {
        // 1. Super Admin & School Administrators
        if (in_array($this->role, ['super_admin', 'superadmin', 'admin_sd', 'admin_paud', 'admin_smp'])) {
            return true;
        }

        // 2. Kepala Sekolah & Waka
        if ($this->hasRole('kepala_sekolah') || $this->role === 'kepala_sekolah' || 
            $this->hasRole('waka') || $this->role === 'waka') {
            return true;
        }

        // 3. Wali Kelas
        if ($this->isActiveHomeroomTeacher() || $this->role === 'wali_kelas') {
            return true;
        }

        if (!$this->employee_id) {
            return false;
        }

        return \Illuminate\Support\Facades\Cache::remember('user_can_access_rapor_' . $this->id, 60, function () {
            // Check Wali Kelas in Classroom or HomeroomAssignment
            $isHomeroom = \App\Models\Classroom::where('homeroom_teacher_id', $this->employee_id)->where('is_active', true)->exists()
                || \App\Models\HomeroomAssignment::where('employee_id', $this->employee_id)
                    ->where('is_active', true)
                    ->where('role', 'wali_kelas')
                    ->exists();

            if ($isHomeroom) {
                return true;
            }

            // Check GPQ (role in assignment, position in employee, or subject code)
            $isGpq = \App\Models\HomeroomAssignment::where('employee_id', $this->employee_id)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->where('role', 'gpq')
                      ->orWhereHas('subject', fn($sq) => $sq->whereIn('code', ['GPQ', 'QURAN']));
                })
                ->exists();

            if ($isGpq) {
                return true;
            }

            $emp = $this->employee;
            if ($emp && strtoupper(trim($emp->position ?? '')) === 'GPQ') {
                return true;
            }

            // Check Guru PAIBP (Pendidikan Agama Islam dan Budi Pekerti)
            $isPaibp = \App\Models\HomeroomAssignment::where('employee_id', $this->employee_id)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereHas('subject', function ($sq) {
                        $sq->whereIn('code', ['PAI', 'PAIBP'])
                           ->orWhere('name', 'like', '%agama islam%');
                    })
                    ->orWhere('notes', 'like', '%PAI%')
                    ->orWhere('notes', 'like', '%Agama Islam%');
                })
                ->exists();

            if ($isPaibp) {
                return true;
            }

            if ($emp && (str_contains(strtolower($emp->position ?? ''), 'pai') || str_contains(strtolower($emp->position ?? ''), 'agama islam'))) {
                return true;
            }

            return false;
        });
    }

    /**
     * Check if the user can access CBT (Super Admin, Kepala Sekolah, Admin, Homeroom Teachers, or Any Teacher).
     */
    public function canAccessCbt(): bool
    {
        if (in_array($this->role, ['super_admin', 'superadmin', 'kepala_sekolah', 'admin_paud', 'admin_sd', 'admin_smp', 'waka', 'wali_kelas', 'guru'])) {
            return true;
        }

        return $this->canAccessRapor() || $this->isTeacher();
    }

    /**
     * Get the employee profile associated with the user.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    protected static function booted()
    {
        static::updated(function ($user) {
            if ($user->employee_id && ($user->isDirty('name') || $user->isDirty('email'))) {
                $employee = \App\Models\Employee::find($user->employee_id);
                if ($employee) {
                    $rawName = $user->name;
                    $front = $employee->front_title;
                    $back = $employee->back_title;
                    \App\Models\Employee::sanitizeTitlesAndName($front, $rawName, $back);

                    $employee->updateQuietly([
                        'front_title' => $front ?: null,
                        'name' => $rawName,
                        'back_title' => $back ?: null,
                        'email' => $user->email,
                    ]);
                }
            }
        });
    }
}
