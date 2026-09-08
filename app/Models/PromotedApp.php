<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotedApp extends Model
{
    use HasFactory;

    protected $table = 'promoted_apps';

    protected $fillable = [
        'name',
        'tagline',
        'description',
        'category',
        'package_name',
        'play_store_url',
        'icon_url',
        'banner_url',
        'rating',
        'downloads',
        'badge',
        'sort_order',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'rating'      => 'float',
        'sort_order'  => 'integer',
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
    ];

    /**
     * Get computed Play Store link if package_name is present
     */
    public function getPlayStoreUrlAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        if (!empty($this->package_name)) {
            return 'https://play.google.com/store/apps/details?id=' . $this->package_name;
        }
        return '';
    }
}
