<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DownloadLink extends Model
{
    protected $fillable = [
        'content_type',
        'content_id',
        'content_title',
        'content_poster',
        'season_number',
        'episode_number',
        'server_name',
        'server_icon',
        'quality',
        'language',
        'file_size',
        'download_url',
        'notes',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'content_id'     => 'integer',
        'season_number'  => 'integer',
        'episode_number' => 'integer',
        'sort_order'     => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForContent($query, string $type, int $contentId)
    {
        return $query->where('content_type', $type)->where('content_id', $contentId);
    }
}
