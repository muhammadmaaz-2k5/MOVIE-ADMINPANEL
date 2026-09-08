<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PromotedApp;

class PromotedAppSeeder extends Seeder
{
    public function run(): void
    {
        $apps = [
            [
                'name'           => 'CinePlay Ultra 4K Player',
                'tagline'        => 'Hardware accelerated 4K HDR & subtitle player',
                'description'    => 'Ultra high-performance media player with HDR10+ support, multi-audio tracks, background playback, and automatic subtitle downloader.',
                'category'       => 'Utilities',
                'package_name'   => 'com.cineplay.ultraplayer',
                'play_store_url' => 'https://play.google.com/store/apps/details?id=com.cineplay.ultraplayer',
                'icon_url'       => 'https://images.unsplash.com/photo-1574375927938-d5a98e8ffe85?w=256&h=256&fit=crop',
                'banner_url'     => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=800&h=400&fit=crop',
                'rating'         => 4.9,
                'downloads'      => '500K+',
                'badge'          => 'FEATURED',
                'sort_order'     => 1,
                'is_featured'    => true,
                'is_active'      => true,
            ],
            [
                'name'           => 'AnimeWorld Pro',
                'tagline'        => 'Seasonal anime schedule, tracking & reminders',
                'description'    => 'The ultimate anime companion. Track ongoing simulcasts, manga updates, voice cast notes, and episode notifications.',
                'category'       => 'Anime',
                'package_name'   => 'com.animeworld.hub',
                'play_store_url' => 'https://play.google.com/store/apps/details?id=com.animeworld.hub',
                'icon_url'       => 'https://images.unsplash.com/photo-1578632767115-351597cf2477?w=256&h=256&fit=crop',
                'banner_url'     => 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?w=800&h=400&fit=crop',
                'rating'         => 4.8,
                'downloads'      => '250K+',
                'badge'          => 'HOT',
                'sort_order'     => 2,
                'is_featured'    => false,
                'is_active'      => true,
            ],
            [
                'name'           => 'Cast2Screen Smart TV',
                'tagline'        => 'Cast videos & mirror screen to any Smart TV',
                'description'    => '1-tap stream casting to Roku, Chromecast, Fire TV, Apple TV, and DLNA devices with zero latency.',
                'category'       => 'Utilities',
                'package_name'   => 'com.streamcast.smartmirror',
                'play_store_url' => 'https://play.google.com/store/apps/details?id=com.streamcast.smartmirror',
                'icon_url'       => 'https://images.unsplash.com/photo-1593784991095-a205069470b6?w=256&h=256&fit=crop',
                'banner_url'     => 'https://images.unsplash.com/photo-1526738549149-8e07eca6c147?w=800&h=400&fit=crop',
                'rating'         => 4.7,
                'downloads'      => '1M+',
                'badge'          => 'POPULAR',
                'sort_order'     => 3,
                'is_featured'    => false,
                'is_active'      => true,
            ],
            [
                'name'           => 'Midnight Cinema VIP',
                'tagline'        => 'Curated late-night cinema & noir lounge',
                'description'    => 'Exclusive portal for mature cinematic storytelling, psychological thrillers, and private indie collections.',
                'category'       => 'Entertainment',
                'package_name'   => 'com.engora.midnightclub',
                'play_store_url' => 'https://play.google.com/store/apps/details?id=com.engora.midnightclub',
                'icon_url'       => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=256&h=256&fit=crop',
                'banner_url'     => 'https://images.unsplash.com/photo-1485846234645-a62644f84728?w=800&h=400&fit=crop',
                'rating'         => 4.9,
                'downloads'      => '100K+',
                'badge'          => '18+ VIP',
                'sort_order'     => 4,
                'is_featured'    => false,
                'is_active'      => true,
            ],
            [
                'name'           => 'Subtitle Master & Audio Sync',
                'tagline'        => 'Instant subtitles in 50+ languages with auto sync',
                'description'    => 'Search and download SRT subtitles in seconds with time-shift fine tuning and offline playback support.',
                'category'       => 'Utilities',
                'package_name'   => 'com.subtitlesync.multilingual',
                'play_store_url' => 'https://play.google.com/store/apps/details?id=com.subtitlesync.multilingual',
                'icon_url'       => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=256&h=256&fit=crop',
                'banner_url'     => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=800&h=400&fit=crop',
                'rating'         => 4.8,
                'downloads'      => '300K+',
                'badge'          => 'NEW',
                'sort_order'     => 5,
                'is_featured'    => false,
                'is_active'      => true,
            ],
        ];

        foreach ($apps as $appData) {
            PromotedApp::updateOrCreate(
                ['package_name' => $appData['package_name']],
                $appData
            );
        }
    }
}
