<?php

namespace Database\Seeders;

use App\Models\HomeSection;
use Illuminate\Database\Seeder;

class HomeSectionSeeder extends Seeder
{
    public function run(): void
    {
        HomeSection::truncate();

        $sections = [
            ['emoji' => '🔥', 'title' => 'Trending Movies', 'endpoint' => 'trending/movie/week', 'params' => null, 'sort_order' => 0],
            ['emoji' => '📺', 'title' => 'Trending TV Shows', 'endpoint' => 'trending/tv/week', 'params' => null, 'sort_order' => 1],
            ['emoji' => '⭐', 'title' => 'Popular Movies', 'endpoint' => 'movie/popular', 'params' => null, 'sort_order' => 2],
            ['emoji' => '🍿', 'title' => 'Popular TV Shows', 'endpoint' => 'tv/popular', 'params' => null, 'sort_order' => 3],
            ['emoji' => '🏆', 'title' => 'Top Rated Movies', 'endpoint' => 'movie/top_rated', 'params' => null, 'sort_order' => 4],
            ['emoji' => '🌟', 'title' => 'Top Rated TV Shows', 'endpoint' => 'tv/top_rated', 'params' => null, 'sort_order' => 5],
            ['emoji' => '💥', 'title' => 'Action Movies', 'endpoint' => 'discover/movie', 'params' => ['with_genres' => '28'], 'sort_order' => 6],
            ['emoji' => '😂', 'title' => 'Comedy Movies', 'endpoint' => 'discover/movie', 'params' => ['with_genres' => '35'], 'sort_order' => 7],
            ['emoji' => '👻', 'title' => 'Horror Movies', 'endpoint' => 'discover/movie', 'params' => ['with_genres' => '27'], 'sort_order' => 8],
            ['emoji' => '💕', 'title' => 'Romantic Movies', 'endpoint' => 'discover/movie', 'params' => ['with_genres' => '10749'], 'sort_order' => 9],
            ['emoji' => '📚', 'title' => 'Documentaries', 'endpoint' => 'discover/movie', 'params' => ['with_genres' => '99'], 'sort_order' => 10],
            ['emoji' => '🕵️', 'title' => 'Mystery TV Shows', 'endpoint' => 'discover/tv', 'params' => ['with_genres' => '96'], 'sort_order' => 11],
            ['emoji' => '🚀', 'title' => 'Sci-Fi & Fantasy', 'endpoint' => 'discover/tv', 'params' => ['with_genres' => '10765'], 'sort_order' => 12],
        ];

        foreach ($sections as $section) {
            HomeSection::create($section);
        }
    }
}