<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomMovieStream extends Model
{
    protected $fillable = [
        'custom_movie_id',
        'server_name',
        'server_icon',
        'stream_url',
        'season_number',
        'episode_number',
        'sort_order'
    ];

    protected $casts = [
        'custom_movie_id' => 'integer',
        'season_number' => 'integer',
        'episode_number' => 'integer',
        'sort_order' => 'integer'
    ];

    public function customMovie()
    {
        return $this->belongsTo(CustomMovie::class, 'custom_movie_id');
    }
}
