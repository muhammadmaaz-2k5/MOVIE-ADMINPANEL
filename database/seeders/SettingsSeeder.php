<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'ads_enabled' => ['value' => false, 'type' => 'boolean'],
            'enable_webview_ads' => ['value' => false, 'type' => 'boolean'],
            'webview_ad_url' => ['value' => 'https://thereviewepisode.com', 'type' => 'string'],
            'app_mode' => ['value' => 'live', 'type' => 'string'],
        ];

        foreach ($defaults as $key => $meta) {
            if (!Setting::where('key', $key)->exists()) {
                Setting::setValue($key, $meta['value'], $meta['type']);
            }
        }
    }
}
