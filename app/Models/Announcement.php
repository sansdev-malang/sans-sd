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
        'central_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'publish_date' => 'datetime',
        'expiry_date' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleting(function ($announcement) {
            \Illuminate\Support\Facades\DB::table('notifications')
                ->where('type', 'App\Notifications\NewAnnouncementNotification')
                ->where(function ($q) use ($announcement) {
                    $q->where('data->id', $announcement->id)
                      ->orWhere('data->id', (string) $announcement->id);
                })
                ->delete();
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
