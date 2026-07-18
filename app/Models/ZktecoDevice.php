<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZktecoDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address',
        'port',
        'model_name',
        'location',
        'is_online',
    ];

    protected $casts = [
        'port' => 'integer',
        'is_online' => 'boolean',
    ];
}
