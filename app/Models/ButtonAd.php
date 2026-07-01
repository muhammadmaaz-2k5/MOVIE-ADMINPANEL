<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ButtonAd extends Model
{
    protected $table = 'button_ads';
    
    protected $fillable = [
        'name',
        'button_text',
        'button_link',
        'button_color',
        'button_icon',
        'target_screen',
        'sort_order',
        'is_enabled'
    ];

    protected $casts = [
        'is_enabled' => 'boolean'
    ];
}