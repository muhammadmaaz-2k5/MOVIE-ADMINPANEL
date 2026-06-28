<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    protected $fillable = [
        'emoji',
        'title', 
        'endpoint',
        'params',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'params' => 'array',
        'is_active' => 'boolean'
    ];
}