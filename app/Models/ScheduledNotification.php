<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledNotification extends Model
{
    protected $table = 'scheduled_notifications';

    protected $fillable = [
        'title',
        'body',
        'type',
        'image_type',
        'image_path',
        'tmdb_id',
        'screen',
        'drama_slug',
        'episode_number',
        'scheduled_at',
        'status',
        'sent_at',
        'failed_at',
        'failure_reason',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at'      => 'datetime',
        'failed_at'    => 'datetime',
    ];
}
