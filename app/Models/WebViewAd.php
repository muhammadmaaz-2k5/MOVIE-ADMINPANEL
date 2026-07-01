<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebViewAd extends Model
{
    protected $table = 'webview_ads';
    
    protected $fillable = [
        'name',
        'position',
        'ad_code',
        'sort_order',
        'is_enabled'
    ];

    protected $casts = [
        'is_enabled' => 'boolean'
    ];
}