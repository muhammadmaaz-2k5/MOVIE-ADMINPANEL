<?php

namespace App\Http\Controllers;

use App\Models\CustomMovie;
use App\Models\MidnightSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MidnightFeedController extends Controller
{
    /**
     * GET /api/midnight/feed
     *
     * Delivers the curated 18+ Midnight Nightclub cinema feed dynamically:
     * - Hero featured late-night items
     * - Nightclub sub-category chips
     * - Dedicated dynamic Midnight nightclub sections from database
     */
    public function feed(Request $request, TmdbProxyController $tmdb)
    {
        $cacheKey = 'api_midnight_feed_v1';
        $feed = Cache::remember($cacheKey, 1800, function () use ($tmdb) {
            // 1. Fetch custom exclusives from database
            $customMovies = CustomMovie::where('is_active', true)
                ->orderBy('id', 'desc')
                ->take(10)
                ->get()
                ->map(function ($movie) {
                    return [
                        'id' => 1000000000 + $movie->id,
                        'tmdb_id' => $movie->tmdb_id,
                        'title' => $movie->title,
                        'name' => $movie->title,
                        'overview' => $movie->overview,
                        'poster_path' => $movie->poster_path,
                        'backdrop_path' => $movie->backdrop_path,
                        'vote_average' => (float)$movie->rating,
                        'release_date' => $movie->year ? "{$movie->year}-01-01" : null,
                        'media_type' => $movie->type ?: 'movie',
                        'is_custom' => true,
                        'custom_id' => $movie->id,
                        'is_adult' => true,
                    ];
                })->values()->toArray();

            // 2. Fetch all active dynamic Midnight sections configured by Admin
            $dbSections = MidnightSection::where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            $sections = [];
            $allFetchedItems = [];

            foreach ($dbSections as $sec) {
                $endpoint = ltrim($sec->endpoint, '/');
                $params = $sec->params ?: [];
                $mediaType = $sec->media_type ?: 'movie';

                if ($endpoint === 'custom') {
                    // Custom Content Exclusives section
                    $items = $customMovies;
                } else {
                    // Fetch TMDB content dynamically
                    $queryParams = array_merge([
                        'page' => 1,
                        'include_adult' => 'true',
                    ], $params);

                    $raw = $tmdb->fetch($endpoint, $queryParams);
                    $items = array_slice($raw['results'] ?? [], 0, 15);
                }

                if (!empty($items)) {
                    $sections[] = [
                        'id' => (int)$sec->id,
                        'emoji' => $sec->emoji ?: '🍸',
                        'title' => $sec->title,
                        'tagline' => $sec->tagline ?: '',
                        'endpoint' => $endpoint,
                        'media_type' => $mediaType,
                        'items' => $items,
                    ];

                    foreach ($items as $it) {
                        $allFetchedItems[] = $it;
                    }
                }
            }

            // 3. Featured Carousel (Top late-night spotlight items)
            $featured = !empty($customMovies)
                ? array_merge(array_slice($customMovies, 0, 2), array_slice($allFetchedItems, 0, 3))
                : array_slice($allFetchedItems, 0, 5);

            // 4. Sub-categories (Built dynamically from section titles & emojis)
            $categories = [
                ['id' => 0, 'label' => 'All Midnight', 'emoji' => '🍸'],
            ];
            foreach ($sections as $index => $sec) {
                $cleanLabel = preg_replace('/^(VIP\s+|Neon\s+|Late\s+Night\s+)/i', '', $sec['title']);
                $categories[] = [
                    'id' => $index + 1,
                    'label' => mb_substr($cleanLabel, 0, 18),
                    'emoji' => $sec['emoji'],
                ];
            }

            return [
                'title' => 'ENGORA MIDNIGHT',
                'tagline' => '18+ Adult Nightlife & Late Night Cinema',
                'is_18_plus' => true,
                'categories' => $categories,
                'featured' => $featured,
                'sections' => $sections,
            ];
        });

        return response()->json($feed);
    }
}
