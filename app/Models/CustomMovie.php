<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomMovie extends Model
{
    protected $fillable = [
        'tmdb_id',
        'title',
        'type',
        'genre_ids',
        'poster_path',
        'backdrop_path',
        'overview',
        'language',
        'rating',
        'year',
        'runtime',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tmdb_id' => 'integer',
        'rating' => 'double',
        'genre_ids' => 'array'
    ];

    public function streams()
    {
        return $this->hasMany(CustomMovieStream::class, 'custom_movie_id');
    }
}
