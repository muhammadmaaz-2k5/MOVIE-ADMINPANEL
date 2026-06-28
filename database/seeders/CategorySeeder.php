<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Try truncating SQLite / MySQL
        try {
            DB::table('categories')->truncate();
        } catch (\Exception $e) {
            DB::table('categories')->delete();
        }
        
        $categories = [
            ['label' => 'All',        'emoji' => '🌐', 'media_type' => 'all',   'trending_params' => '{}', 'popular_params' => '{}'],
            ['label' => 'Hollywood',  'emoji' => '🇺🇸', 'media_type' => 'movie', 'trending_params' => json_encode(['with_original_language' => 'en', 'sort_by' => 'popularity.desc', 'include_adult' => false]), 'popular_params' => json_encode(['with_original_language' => 'en', 'sort_by' => 'popularity.desc', 'include_adult' => false])],
            ['label' => 'Bollywood',  'emoji' => '🇮🇳', 'media_type' => 'movie', 'trending_params' => json_encode(['with_original_language' => 'hi', 'sort_by' => 'popularity.desc', 'include_adult' => false]), 'popular_params' => json_encode(['with_original_language' => 'hi', 'sort_by' => 'popularity.desc', 'include_adult' => false])],
            ['label' => 'Punjabi',    'emoji' => '🎵', 'media_type' => 'movie', 'trending_params' => json_encode(['with_original_language' => 'pa', 'sort_by' => 'popularity.desc', 'include_adult' => false]), 'popular_params' => json_encode(['with_original_language' => 'pa', 'sort_by' => 'popularity.desc', 'include_adult' => false])],
            ['label' => 'KDrama',     'emoji' => '🇰🇷', 'media_type' => 'tv',    'trending_params' => json_encode(['with_original_language' => 'ko', 'sort_by' => 'popularity.desc', 'include_adult' => false]), 'popular_params' => json_encode(['with_original_language' => 'ko', 'sort_by' => 'popularity.desc', 'include_adult' => false])],
            ['label' => 'Anime',      'emoji' => '🇯🇵', 'media_type' => 'tv',    'trending_params' => json_encode(['with_original_language' => 'ja', 'with_genres' => '16', 'sort_by' => 'popularity.desc', 'include_adult' => false]), 'popular_params' => json_encode(['with_original_language' => 'ja', 'with_genres' => '16', 'sort_by' => 'popularity.desc', 'include_adult' => false])],
            ['label' => 'Turkish',    'emoji' => '🇹🇷', 'media_type' => 'tv',    'trending_params' => json_encode(['with_original_language' => 'tr', 'sort_by' => 'popularity.desc', 'include_adult' => false]), 'popular_params' => json_encode(['with_original_language' => 'tr', 'sort_by' => 'popularity.desc', 'include_adult' => false])],
            ['label' => 'Arabic',     'emoji' => '🇸🇦', 'media_type' => 'movie', 'trending_params' => json_encode(['with_original_language' => 'ar', 'sort_by' => 'popularity.desc', 'include_adult' => false]), 'popular_params' => json_encode(['with_original_language' => 'ar', 'sort_by' => 'popularity.desc', 'include_adult' => false])],
            ['label' => 'Chinese',    'emoji' => '🇨🇳', 'media_type' => 'movie', 'trending_params' => json_encode(['with_original_language' => 'zh', 'sort_by' => 'popularity.desc', 'include_adult' => false]), 'popular_params' => json_encode(['with_original_language' => 'zh', 'sort_by' => 'popularity.desc', 'include_adult' => false])],
            ['label' => 'Spanish',    'emoji' => '🇪🇸', 'media_type' => 'movie', 'trending_params' => json_encode(['with_original_language' => 'es', 'sort_by' => 'popularity.desc', 'include_adult' => false]), 'popular_params' => json_encode(['with_original_language' => 'es', 'sort_by' => 'popularity.desc', 'include_adult' => false])],
            ['label' => 'French',     'emoji' => '🇫🇷', 'media_type' => 'movie', 'trending_params' => json_encode(['with_original_language' => 'fr', 'sort_by' => 'popularity.desc', 'include_adult' => false]), 'popular_params' => json_encode(['with_original_language' => 'fr', 'sort_by' => 'popularity.desc', 'include_adult' => false])],
            ['label' => 'Tamil',      'emoji' => '🎞️', 'media_type' => 'movie', 'trending_params' => json_encode(['with_original_language' => 'ta', 'sort_by' => 'popularity.desc', 'include_adult' => false]), 'popular_params' => json_encode(['with_original_language' => 'ta', 'sort_by' => 'popularity.desc', 'include_adult' => false])],
            ['label' => 'Telugu',     'emoji' => '🎥', 'media_type' => 'movie', 'trending_params' => json_encode(['with_original_language' => 'te', 'sort_by' => 'popularity.desc', 'include_adult' => false]), 'popular_params' => json_encode(['with_original_language' => 'te', 'sort_by' => 'popularity.desc', 'include_adult' => false])],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->insert($cat);
        }
    }
}
