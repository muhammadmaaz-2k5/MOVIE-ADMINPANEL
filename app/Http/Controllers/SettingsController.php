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
        
        'enable_ad_home_banner' => ['type' => 'boolean', 'default' => true],
        'ad_url_home_banner' => ['type' => 'string', 'default' => ''],
        'enable_ad_home_inline' => ['type' => 'boolean', 'default' => true],
        'ad_url_home_inline' => ['type' => 'string', 'default' => ''],
        
        'enable_ad_search_banner' => ['type' => 'boolean', 'default' => true],
        'ad_url_search_banner' => ['type' => 'string', 'default' => ''],
        'enable_ad_search_inline' => ['type' => 'boolean', 'default' => true],
        'ad_url_search_inline' => ['type' => 'string', 'default' => ''],
        
        'enable_ad_detail_banner' => ['type' => 'boolean', 'default' => true],
        'ad_url_detail_banner' => ['type' => 'string', 'default' => ''],
        'enable_ad_detail_inline' => ['type' => 'boolean', 'default' => true],
        'ad_url_detail_inline' => ['type' => 'string', 'default' => ''],
        
        'enable_ad_player_banner' => ['type' => 'boolean', 'default' => true],
        'ad_url_player_banner' => ['type' => 'string', 'default' => ''],
        
        'enable_ad_browse_banner' => ['type' => 'boolean', 'default' => true],
        'ad_url_browse_banner' => ['type' => 'string', 'default' => ''],
        'enable_ad_browse_inline' => ['type' => 'boolean', 'default' => true],
        'ad_url_browse_inline' => ['type' => 'string', 'default' => ''],
        
        'enable_ad_season_banner' => ['type' => 'boolean', 'default' => true],
        'ad_url_season_banner' => ['type' => 'string', 'default' => ''],
        
        'enable_ad_actor_banner' => ['type' => 'boolean', 'default' => true],
        'ad_url_actor_banner' => ['type' => 'string', 'default' => ''],
        'enable_ad_actor_inline' => ['type' => 'boolean', 'default' => true],
        'ad_url_actor_inline' => ['type' => 'string', 'default' => ''],
        
        'enable_ad_category_banner' => ['type' => 'boolean', 'default' => true],
        'ad_url_category_banner' => ['type' => 'string', 'default' => ''],
        
        'enable_ad_seeall_banner' => ['type' => 'boolean', 'default' => true],
        'ad_url_seeall_banner' => ['type' => 'string', 'default' => ''],
        
        'enable_ad_language_banner' => ['type' => 'boolean', 'default' => true],
        'ad_url_language_banner' => ['type' => 'string', 'default' => ''],
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
            
            'enable_ad_home_banner' => 'nullable|boolean',
            'ad_url_home_banner' => 'nullable|string|max:2048',
            'enable_ad_home_inline' => 'nullable|boolean',
            'ad_url_home_inline' => 'nullable|string|max:2048',
            
            'enable_ad_search_banner' => 'nullable|boolean',
            'ad_url_search_banner' => 'nullable|string|max:2048',
            'enable_ad_search_inline' => 'nullable|boolean',
            'ad_url_search_inline' => 'nullable|string|max:2048',
            
            'enable_ad_detail_banner' => 'nullable|boolean',
            'ad_url_detail_banner' => 'nullable|string|max:2048',
            'enable_ad_detail_inline' => 'nullable|boolean',
            'ad_url_detail_inline' => 'nullable|string|max:2048',
            
            'enable_ad_player_banner' => 'nullable|boolean',
            'ad_url_player_banner' => 'nullable|string|max:2048',
            
            'enable_ad_browse_banner' => 'nullable|boolean',
            'ad_url_browse_banner' => 'nullable|string|max:2048',
            'enable_ad_browse_inline' => 'nullable|boolean',
            'ad_url_browse_inline' => 'nullable|string|max:2048',
            
            'enable_ad_season_banner' => 'nullable|boolean',
            'ad_url_season_banner' => 'nullable|string|max:2048',
            
            'enable_ad_actor_banner' => 'nullable|boolean',
            'ad_url_actor_banner' => 'nullable|string|max:2048',
            'enable_ad_actor_inline' => 'nullable|boolean',
            'ad_url_actor_inline' => 'nullable|string|max:2048',
            
            'enable_ad_category_banner' => 'nullable|boolean',
            'ad_url_category_banner' => 'nullable|string|max:2048',
            
            'enable_ad_seeall_banner' => 'nullable|boolean',
            'ad_url_seeall_banner' => 'nullable|string|max:2048',
            
            'enable_ad_language_banner' => 'nullable|boolean',
            'ad_url_language_banner' => 'nullable|string|max:2048',
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
        
        $placements = [
            'home_banner', 'home_inline', 'search_banner', 'search_inline',
            'detail_banner', 'detail_inline', 'player_banner', 'browse_banner',
            'browse_inline', 'season_banner', 'actor_banner', 'actor_inline',
            'category_banner', 'seeall_banner', 'language_banner'
        ];
        foreach ($placements as $p) {
            Setting::setValue("enable_ad_$p", true, 'boolean');
        }

        return response()->json([
            'message' => 'All ads enabled',
            'settings' => $this->buildSettingsResponse(),
        ]);
    }

    public function disableAllAds()
    {
        Setting::setValue('ads_enabled', false, 'boolean');
        Setting::setValue('enable_webview_ads', false, 'boolean');
        
        $placements = [
            'home_banner', 'home_inline', 'search_banner', 'search_inline',
            'detail_banner', 'detail_inline', 'player_banner', 'browse_banner',
            'browse_inline', 'season_banner', 'actor_banner', 'actor_inline',
            'category_banner', 'seeall_banner', 'language_banner'
        ];
        foreach ($placements as $p) {
            Setting::setValue("enable_ad_$p", false, 'boolean');
        }

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
