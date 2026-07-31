<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'category',
        'target_audience',
        'attachment',
        'is_active',
        'created_by',
        'publish_date',
        'expiry_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'publish_date' => 'datetime',
        'expiry_date' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
