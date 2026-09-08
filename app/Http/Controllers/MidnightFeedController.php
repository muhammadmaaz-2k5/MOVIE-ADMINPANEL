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
     * 100% Manual 18+ Midnight Nightclub Cinema Feed:
     * - Disconnected completely from TMDB
     * - Exclusively delivers manual custom Movies, TV Shows, and Anime flagged with is_midnight = true
     * - Categorized into manual Midnight categories configured in the Admin Dashboard
     */
    public function feed(Request $request)
    {
        $cacheKey = 'api_midnight_feed_v1';
        $feed = Cache::remember($cacheKey, 1800, function () {
            // 1. Fetch all active manual categories from database
            $dbCategories = MidnightSection::where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            // 2. Fetch all active custom items marked for Midnight (Movies, TV Shows, Anime)
            $allMidnightMovies = CustomMovie::where('is_active', true)
                ->where('is_midnight', true)
                ->orderBy('id', 'desc')
                ->get();

            // Helper to format custom movie into standard mobile media item
            $formatItem = function ($movie) {
                return [
                    'id'            => 1000000000 + $movie->id,
                    'tmdb_id'       => $movie->tmdb_id,
                    'title'         => $movie->title,
                    'name'          => $movie->title,
                    'overview'      => $movie->overview,
                    'poster_path'   => $movie->poster_path,
                    'backdrop_path' => $movie->backdrop_path,
                    'vote_average'  => (float)$movie->rating,
                    'release_date'  => $movie->year ? "{$movie->year}-01-01" : null,
                    'media_type'    => $movie->type ?: 'movie',
                    'is_custom'     => true,
                    'custom_id'     => $movie->id,
                    'is_adult'      => true,
                    'is_midnight'   => true,
                ];
            };

            $formattedAll = $allMidnightMovies->map($formatItem)->values()->toArray();

            // 3. Build Sections mapped to manual categories
            $sections = [];
            $unassignedItems = [];

            // Group movies by midnight_section_id
            $groupedBySection = [];
            foreach ($allMidnightMovies as $movie) {
                if ($movie->midnight_section_id) {
                    $groupedBySection[$movie->midnight_section_id][] = $formatItem($movie);
                } else {
                    $unassignedItems[] = $formatItem($movie);
                }
            }

            foreach ($dbCategories as $cat) {
                $categoryItems = $groupedBySection[$cat->id] ?? [];

                // If this is the first section and there are unassigned midnight items, include them
                if (empty($sections) && !empty($unassignedItems)) {
                    $categoryItems = array_merge($unassignedItems, $categoryItems);
                }

                $sections[] = [
                    'id'         => (int)$cat->id,
                    'emoji'      => $cat->emoji ?: '🍸',
                    'title'      => $cat->title,
                    'tagline'    => $cat->tagline ?: '',
                    'endpoint'   => 'manual',
                    'media_type' => $cat->media_type ?: 'movie',
                    'items'      => $categoryItems,
                ];
            }

            // Fallback: If no categories exist, create a default "VIP Lounge Exclusives" section
            if (empty($sections) && !empty($formattedAll)) {
                $sections[] = [
                    'id'         => 1,
                    'emoji'      => '🍾',
                    'title'      => 'VIP Lounge Exclusives',
                    'tagline'    => 'Hand-curated adult late-night streams',
                    'endpoint'   => 'manual',
                    'media_type' => 'movie',
                    'items'      => $formattedAll,
                ];
            }

            // 4. Featured Spotlight Carousel (Top late-night manual items)
            $featured = array_slice($formattedAll, 0, 6);

            // 5. Sub-categories (Built dynamically from active manual categories)
            $categories = [
                ['id' => 0, 'label' => 'All Midnight', 'emoji' => '🍸'],
            ];
            foreach ($dbCategories as $cat) {
                $cleanLabel = preg_replace('/^(VIP\s+|Neon\s+|Late\s+Night\s+)/i', '', $cat->title);
                $categories[] = [
                    'id'    => (int)$cat->id,
                    'label' => mb_substr($cleanLabel, 0, 18),
                    'emoji' => $cat->emoji ?: '🍸',
                ];
            }

            return [
                'title'      => 'ENGORA MIDNIGHT',
                'tagline'    => '18+ Adult Nightlife & Late Night Cinema',
                'is_18_plus' => true,
                'categories' => $categories,
                'featured'   => $featured,
                'sections'   => $sections,
            ];
        });

        return response()->json($feed);
    }
}
