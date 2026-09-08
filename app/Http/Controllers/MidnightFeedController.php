<?php

namespace App\Http\Controllers;

use App\Models\CustomMovie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MidnightFeedController extends Controller
{
    /**
     * GET /api/midnight/feed
     *
     * Delivers the curated 18+ Midnight Nightclub cinema feed:
     * - Hero featured late-night items
     * - Nightclub sub-category chips
     * - Dedicated Midnight nightclub sections
     */
    public function feed(Request $request, TmdbProxyController $tmdb)
    {
        $cacheKey = 'api_midnight_feed_v1';
        $feed = Cache::remember($cacheKey, 1800, function () use ($tmdb) {
            // 1. Fetch custom exclusives from database
            $customMovies = CustomMovie::where('is_active', true)
                ->orderBy('id', 'desc')
                ->take(8)
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

            // 2. Neon Noir & Crime Thrillers
            $noirRaw = $tmdb->fetch('discover/movie', [
                'with_genres' => '80,53',
                'sort_by' => 'popularity.desc',
                'page' => 1,
                'include_adult' => true,
            ]);
            $noirItems = array_slice($noirRaw['results'] ?? [], 0, 15);

            // 3. After Hours & Passionate Drama
            $passionRaw = $tmdb->fetch('discover/movie', [
                'with_genres' => '10749,18',
                'sort_by' => 'popularity.desc',
                'page' => 1,
                'include_adult' => true,
            ]);
            $passionItems = array_slice($passionRaw['results'] ?? [], 0, 15);

            // 4. Midnight Madness & Horror
            $horrorRaw = $tmdb->fetch('discover/movie', [
                'with_genres' => '27,96',
                'sort_by' => 'popularity.desc',
                'page' => 1,
                'include_adult' => true,
            ]);
            $horrorItems = array_slice($horrorRaw['results'] ?? [], 0, 15);

            // 5. Psychological & Mystery
            $psychRaw = $tmdb->fetch('discover/movie', [
                'with_genres' => '96,53',
                'sort_by' => 'vote_average.desc',
                'vote_count.gte' => 300,
                'page' => 1,
                'include_adult' => true,
            ]);
            $psychItems = array_slice($psychRaw['results'] ?? [], 0, 15);

            // 6. Featured Carousel (Top late-night titles)
            $featured = !empty($customMovies)
                ? array_merge(array_slice($customMovies, 0, 2), array_slice($noirItems, 0, 3))
                : array_slice($noirItems, 0, 5);

            // 7. Sub-categories
            $categories = [
                ['id' => 0, 'label' => 'All Midnight', 'emoji' => '🍸'],
                ['id' => 1, 'label' => 'Neon Noir', 'emoji' => '🌙'],
                ['id' => 2, 'label' => 'After Hours', 'emoji' => '💋'],
                ['id' => 3, 'label' => 'Midnight Horror', 'emoji' => '💀'],
                ['id' => 4, 'label' => 'Psychological', 'emoji' => '🔮'],
                ['id' => 5, 'label' => 'VIP Lounge', 'emoji' => '🍾'],
            ];

            // 8. Assemble Nightclub Sections
            $sections = [];

            if (!empty($customMovies)) {
                $sections[] = [
                    'id' => 901,
                    'emoji' => '🍾',
                    'title' => 'VIP Nightclub Exclusives',
                    'tagline' => 'Hand-curated adult late-night streams',
                    'endpoint' => 'custom',
                    'media_type' => 'movie',
                    'items' => $customMovies,
                ];
            }

            $sections[] = [
                'id' => 902,
                'emoji' => '🌙',
                'title' => 'Neon Noir & Nightlife Crime',
                'tagline' => 'Dark alleys, gritty undergrounds, and midnight heists',
                'endpoint' => 'discover/movie',
                'media_type' => 'movie',
                'items' => $noirItems,
            ];

            $sections[] = [
                'id' => 903,
                'emoji' => '💋',
                'title' => 'After Hours & Passion',
                'tagline' => 'Intense, sensual, and mature late-night romance',
                'endpoint' => 'discover/movie',
                'media_type' => 'movie',
                'items' => $passionItems,
            ];

            $sections[] = [
                'id' => 904,
                'emoji' => '💀',
                'title' => 'Midnight Madness & Horror',
                'tagline' => 'Sinister chills and screams for the dead of night',
                'endpoint' => 'discover/movie',
                'media_type' => 'movie',
                'items' => $horrorItems,
            ];

            $sections[] = [
                'id' => 905,
                'emoji' => '🔮',
                'title' => 'Late Night Mindbenders',
                'tagline' => 'Twisted psychological thrillers that keep you awake',
                'endpoint' => 'discover/movie',
                'media_type' => 'movie',
                'items' => $psychItems,
            ];

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
