<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'front_title',
        'name',
        'back_title',
        'email',
        'employee_type_id',
        'unit',
        'gender',
        'birth_place',
        'birth_date',
        'nik',
        'niy',
        'nuptk',
        'no_ukg',
        'nrg',
        'pangkat_golongan',
        'last_education',
        'major',
        'position',
        'additional_position',
        'task_start_date',
        'appointment_date',
        'last_sk_date',
        'last_sk_number',
        'work_period',
        'address',
        'phone',
        'notes',
        'employment_status',
        'zkteco_uid',
        'photo',
        'status',
    ];

    /**
     * Get the employee type.
     */
        protected $appends = ['raw_name'];

    public function getNameAttribute($value)
    {
        $front = !empty($this->front_title) ? trim($this->front_title) . ' ' : '';
        $back = !empty($this->back_title) ? ', ' . trim($this->back_title) : '';
        return $front . $value . $back;
    }

    public function getRawNameAttribute()
    {
        return $this->attributes['name'] ?? '';
    }

    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class);
    }

    /**
     * Get the attendances for the employee.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the user account associated with the employee.
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function workingShifts(): HasMany
    {
        return $this->hasMany(EmployeeWorkingShift::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    protected static function booted()
    {
        static::saving(function ($employee) {
            $front = $employee->attributes['front_title'] ?? null;
            $name = $employee->attributes['name'] ?? '';
            $back = $employee->attributes['back_title'] ?? null;

            static::sanitizeTitlesAndName($front, $name, $back);

            $employee->attributes['front_title'] = $front ?: null;
            $employee->attributes['name'] = $name;
            $employee->attributes['back_title'] = $back ?: null;
        });

        static::saved(function ($employee) {
            if ($employee->isDirty('name') || $employee->isDirty('email')) {
                $user = \App\Models\User::where('employee_id', $employee->id)->first();
                if ($user) {
                    $user->updateQuietly([
                        'name' => $employee->raw_name ?? $employee->getAttributes()['name'],
                        'email' => $employee->email,
                    ]);
                }
            }
        });
    }

    /**
     * Otomatis membersihkan gelar dari kolom nama dan memindahkannya ke kolom gelar yang sesuai.
     */
    public static function sanitizeTitlesAndName(?string &$frontTitle, string &$name, ?string &$backTitle): void
    {
        $frontTitle = $frontTitle !== null ? trim($frontTitle) : '';
        $backTitle = $backTitle !== null ? trim($backTitle) : '';
        $name = trim($name);

        $frontPatterns = [
            'dr\.', 'drg\.', 'dra\.', 'drs\.', 'prof\.', 'kh\.', 'kh\b', 'hj\.', 'hj\b', 
            'h\.', 'h\b', 'ust\.', 'ustad\b', 'ustadz\b', 'ir\.', 'rr\.', 'raden\b'
        ];

        // 1. Bersihkan gelar depan eksplisit dari awal nama
        if (!empty($frontTitle)) {
            $escapedFront = preg_quote($frontTitle, '/');
            $escapedFrontClean = preg_quote(rtrim($frontTitle, '.'), '/');
            $name = preg_replace('/^\s*(' . $escapedFront . '|' . $escapedFrontClean . '\.?)\s*/i', '', $name);
        } else {
            foreach ($frontPatterns as $pat) {
                if (preg_match('/^(' . $pat . ')\s+/i', $name, $matches)) {
                    $frontTitle = trim($matches[1]);
                    if (!str_ends_with($frontTitle, '.') && !in_array(strtolower($frontTitle), ['kh', 'hj', 'h', 'ustad', 'ustadz', 'raden'])) {
                        $frontTitle .= '.';
                    }
                    $name = preg_replace('/^' . preg_quote($matches[0], '/') . '/i', '', $name);
                    break;
                }
            }
        }

        // 2. Tangani tanda koma di nama (misal: "Sri Yudiyanti, S.Pd, M.M" atau "Sri Yudiyanti, spd,dsjks")
        if (strpos($name, ',') !== false) {
            $parts = explode(',', $name);
            $cleanBaseName = trim(array_shift($parts));
            $afterComma = trim(implode(', ', array_filter(array_map('trim', $parts))));

            if (!empty($afterComma)) {
                if (empty($backTitle)) {
                    $backTitle = $afterComma;
                } else {
                    $backTitle = $backTitle . ', ' . $afterComma;
                }
                $name = $cleanBaseName;
            }
        }

        // 3. Bersihkan gelar belakang dari akhir nama jika masih ada
        if (!empty($backTitle)) {
            $escapedBack = preg_quote($backTitle, '/');
            $escapedBackClean = preg_quote(rtrim($backTitle, '.'), '/');
            $name = preg_replace('/[\s,]+(' . $escapedBack . '|' . $escapedBackClean . '\.?)\s*$/i', '', $name);

            $parts = array_map('trim', explode(',', $backTitle));
            foreach ($parts as $part) {
                if (!empty($part)) {
                    $pEsc = preg_quote($part, '/');
                    $name = preg_replace('/[\s,]+' . $pEsc . '\s*$/i', '', $name);
                }
            }
        }

        // 4. Normalisasi format gelar belakang
        if (!empty($backTitle)) {
            $tokens = array_filter(array_map('trim', explode(',', $backTitle)), fn($t) => $t !== '');
            $backTitle = implode(', ', $tokens);
        }

        // 5. Bersihkan sisa tanda koma atau spasi berlebih
        $name = trim($name, " \t\n\r\0\x0B,");
        $name = preg_replace('/\s+/', ' ', $name);
        $frontTitle = trim($frontTitle);
        $backTitle = trim($backTitle);
    }
}



