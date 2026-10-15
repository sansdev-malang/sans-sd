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
     * Check if the user is currently assigned as an active homeroom teacher in the active academic year.
     */
    public function isActiveHomeroomTeacher(): bool
    {
        if (!$this->employee_id) {
            return false;
        }

        return \Illuminate\Support\Facades\Cache::remember('user_is_active_homeroom_' . $this->employee_id, 60, function () {
            $activeAy = \App\Models\AcademicYear::where('is_active', true)->first();
            if (!$activeAy) {
                return false;
            }

            return \App\Models\HomeroomAssignment::where('employee_id', $this->employee_id)
                ->where('academic_year_id', $activeAy->id)
                ->where('is_active', true)
                ->exists();
        });
    }

    /**
     * Check if the user can access E-Rapor and CBT (Super Admin, Kepala Sekolah, Admin, or Active Homeroom Teachers / Teachers).
     */
    public function canAccessRapor(): bool
    {
        if (in_array($this->role, ['super_admin', 'kepala_sekolah', 'admin_paud', 'admin_sd', 'admin_smp', 'waka', 'wali_kelas', 'guru'])) {
            return true;
        }

        return $this->isActiveHomeroomTeacher() || $this->employee_id !== null;
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
