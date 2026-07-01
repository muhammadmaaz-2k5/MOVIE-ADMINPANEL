<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdSeeder extends Seeder
{
    public function run(): void
    {
        // WebView Ads
        DB::table('webview_ads')->insert([
            [
                'name' => 'Top Banner',
                'position' => 'top',
                'ad_code' => '<div style="width:100%;height:90px;background:linear-gradient(135deg,#667eea 0%,#764ba8 100%);display:flex;align-items:center;justify-content:center;color:white;font-size:24px;font-weight:bold;">Advertisement</div>',
                'sort_order' => 1,
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bottom Banner',
                'position' => 'bottom',
                'ad_code' => '<div style="width:100%;height:50px;background:#1a1a2e;display:flex;align-items:center;justify-content:center;color:#aaa;font-size:12px;">Sponsored Content</div>',
                'sort_order' => 2,
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Button Ads
        DB::table('button_ads')->insert([
            [
                'name' => 'Premium Content',
                'button_text' => 'Premium',
                'button_link' => 'https://example.com/premium',
                'button_color' => '#6C5CE7',
                'button_icon' => '⭐',
                'target_screen' => 'home',
                'sort_order' => 1,
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'App Download',
                'button_text' => 'Get App',
                'button_link' => 'https://example.com/download',
                'button_color' => '#00B894',
                'button_icon' => '📱',
                'target_screen' => 'home',
                'sort_order' => 2,
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}