<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public const APP_MODE_LIVE = 'live';
    public const APP_MODE_SAFE_REVIEW = 'safe_review';

    protected $fillable = ['key', 'value', 'type'];

    public static function getAppMode(): string
    {
        $mode = static::getValue('app_mode', self::APP_MODE_LIVE);

        return in_array($mode, [self::APP_MODE_LIVE, self::APP_MODE_SAFE_REVIEW], true)
            ? $mode
            : self::APP_MODE_LIVE;
    }

    public static function isLiveMode(): bool
    {
        return static::getAppMode() === self::APP_MODE_LIVE;
    }

    public static function isSafeReviewMode(): bool
    {
        return static::getAppMode() === self::APP_MODE_SAFE_REVIEW;
    }

    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }
        
        return match($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    public static function setValue(string $key, $value, string $type = 'string'): void
    {
        $valueToSave = match($type) {
            'boolean' => $value ? '1' : '0',
            'integer' => (string) $value,
            'json' => json_encode($value),
            default => (string) $value,
        };

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $valueToSave, 'type' => $type]
        );
    }
}