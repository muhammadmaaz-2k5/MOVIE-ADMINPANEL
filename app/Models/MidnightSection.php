<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MidnightSection extends Model
{
    protected $table = 'midnight_sections';

    protected $fillable = [
        'emoji',
        'title',
        'tagline',
        'endpoint',
        'params',
        'media_type',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'params' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function customMovies()
    {
        return $this->hasMany(CustomMovie::class, 'midnight_section_id');
    }
}
