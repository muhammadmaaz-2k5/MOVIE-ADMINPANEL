<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    private const SETTINGS = [
        'ads_enabled' => ['type' => 'boolean', 'default' => false],
        'enable_webview_ads' => ['type' => 'boolean', 'default' => false],
        'webview_ad_url' => ['type' => 'string', 'default' => 'https://nazaarabox.com'],
        'app_mode' => ['type' => 'string', 'default' => Setting::APP_MODE_LIVE],
    ];

    public function index()
    {
        $settings = [];
        foreach (self::SETTINGS as $key => $meta) {
            $settings[$key] = Setting::getValue($key, $meta['default']);
        }

        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'ads_enabled' => 'nullable|boolean',
            'enable_webview_ads' => 'nullable|boolean',
            'webview_ad_url' => 'nullable|string|max:2048',
            'app_mode' => 'nullable|in:live,safe_review',
        ]);

        foreach (self::SETTINGS as $key => $meta) {
            if (array_key_exists($key, $validated)) {
                Setting::setValue($key, $validated[$key] ?? $meta['default'], $meta['type']);
            }
        }

        return $this->index();
    }

    public function enableAllAds()
    {
        Setting::setValue('ads_enabled', true, 'boolean');
        Setting::setValue('enable_webview_ads', true, 'boolean');

        return response()->json([
            'message' => 'All ads enabled',
            'settings' => $this->buildSettingsResponse(),
        ]);
    }

    public function disableAllAds()
    {
        Setting::setValue('ads_enabled', false, 'boolean');
        Setting::setValue('enable_webview_ads', false, 'boolean');

        return response()->json([
            'message' => 'All ads disabled',
            'settings' => $this->buildSettingsResponse(),
        ]);
    }

    public function setSafeReviewMode()
    {
        Setting::setValue('app_mode', Setting::APP_MODE_SAFE_REVIEW, 'string');

        return response()->json([
            'message' => 'App set to Safe Review Mode',
            'settings' => $this->buildSettingsResponse(),
        ]);
    }

    public function setLiveMode()
    {
        Setting::setValue('app_mode', Setting::APP_MODE_LIVE, 'string');

        return response()->json([
            'message' => 'App set to Live Mode',
            'settings' => $this->buildSettingsResponse(),
        ]);
    }

    public static function buildSettingsResponse(): array
    {
        $settings = [];
        foreach (self::SETTINGS as $key => $meta) {
            $settings[$key] = Setting::getValue($key, $meta['default']);
        }

        return $settings;
    }
}
