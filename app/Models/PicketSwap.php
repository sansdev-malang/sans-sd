<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PicketSwap extends Model
{
    protected $fillable = [
        'requester_id',
        'requested_date',
        'target_employee_id',
        'target_date',
        'status', // pending, approved_by_target, approved, rejected
        'approved_by_id',
        'notes',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'target_date' => 'date',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'requester_id');
    }

    public function targetEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'target_employee_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }
}
