<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    private const SETTINGS = [
        'ads_enabled' => ['type' => 'boolean', 'default' => true],
        'admob_enabled' => ['type' => 'boolean', 'default' => true],
        'admob_banner_id' => ['type' => 'string', 'default' => 'ca-app-pub-3940256099942544/6300978111'],
        'admob_interstitial_id' => ['type' => 'string', 'default' => 'ca-app-pub-3940256099942544/1033173712'],
        'admob_rewarded_id' => ['type' => 'string', 'default' => 'ca-app-pub-3940256099942544/5224354917'],
        'admob_rewarded_interstitial_id' => ['type' => 'string', 'default' => 'ca-app-pub-3940256099942544/5354046379'],
        'admob_app_open_id' => ['type' => 'string', 'default' => 'ca-app-pub-3940256099942544/9257395921'],
        'admob_native_id' => ['type' => 'string', 'default' => 'ca-app-pub-3940256099942544/2247696110'],
        'enable_ad_app_open' => ['type' => 'boolean', 'default' => true],
        'enable_ad_native' => ['type' => 'boolean', 'default' => true],
        'enable_webview_ads' => ['type' => 'boolean', 'default' => false],
        'webview_ad_url' => ['type' => 'string', 'default' => 'https://thereviewepisode.com'],
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
            'admob_enabled' => 'nullable|boolean',
            'admob_banner_id' => 'nullable|string|max:255',
            'admob_interstitial_id' => 'nullable|string|max:255',
            'admob_rewarded_id' => 'nullable|string|max:255',
            'admob_rewarded_interstitial_id' => 'nullable|string|max:255',
            'admob_app_open_id' => 'nullable|string|max:255',
            'admob_native_id' => 'nullable|string|max:255',
            'enable_ad_app_open' => 'nullable|boolean',
            'enable_ad_native' => 'nullable|boolean',
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

        \Illuminate\Support\Facades\Cache::forget('api_config_settings');

        return $this->index();
    }

    public function enableAllAds()
    {
        Setting::setValue('ads_enabled', true, 'boolean');
        Setting::setValue('admob_enabled', true, 'boolean');
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

        \Illuminate\Support\Facades\Cache::forget('api_config_settings');

        return response()->json([
            'message' => 'All ads enabled',
            'settings' => $this->buildSettingsResponse(),
        ]);
    }

    public function disableAllAds()
    {
        Setting::setValue('ads_enabled', false, 'boolean');
        Setting::setValue('admob_enabled', false, 'boolean');
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

        \Illuminate\Support\Facades\Cache::forget('api_config_settings');

        return response()->json([
            'message' => 'All ads disabled',
            'settings' => $this->buildSettingsResponse(),
        ]);
    }

    public function setSafeReviewMode()
    {
        Setting::setValue('app_mode', Setting::APP_MODE_SAFE_REVIEW, 'string');
        \Illuminate\Support\Facades\Cache::forget('api_config_settings');

        return response()->json([
            'message' => 'App set to Safe Review Mode',
            'settings' => $this->buildSettingsResponse(),
        ]);
    }

    public function setLiveMode()
    {
        Setting::setValue('app_mode', Setting::APP_MODE_LIVE, 'string');
        \Illuminate\Support\Facades\Cache::forget('api_config_settings');

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
