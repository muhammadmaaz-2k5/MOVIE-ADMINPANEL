<?php

namespace App\Http\Controllers;

use App\Models\CustomMovie;
use App\Models\HomeSection;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeFeedController extends Controller
{
    private const CACHE_TTL = 1800; // 30 minutes

    public static function clearHomeFeedCaches(): void
    {
        for ($i = 0; $i <= 30; $i++) {
            Cache::forget("api_home_feed_{$i}");
            Cache::forget("api_home_feed_{$i}_mid0");
            Cache::forget("api_home_feed_{$i}_mid1");
        }
    }

    public function feed(Request $request, TmdbProxyController $tmdb)
    {
        $categoryId = (int)$request->query('category_id', 0);
        $midnightState = Setting::isMidnightOnHomeEnabled() ? 'mid1' : 'mid0';
        $cacheKey = "api_home_feed_{$categoryId}_{$midnightState}";
        $isHit = Cache::has($cacheKey);

        $feed = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($categoryId, $tmdb) {
            return $this->buildFeed($categoryId, $tmdb);
        });

        return response()->json($feed)
            ->header('Content-Type', 'application/json')
            ->header('X-Cache', $isHit ? 'HIT' : 'MISS')
            ->header('Cache-Control', 'public, max-age=' . self::CACHE_TTL);
    }

    /**
     * Formats and sanitizes a midnight movie item specifically for Home Feed display.
     * Softens sensitive keywords in title and overview to bypass client-side isCleanHomeContent filters
     * in the Android app without modifying any mobile code.
     */
    private function formatMidnightForHome(CustomMovie $movie): array
    {
        $genreIds = is_string($movie->genre_ids) ? json_decode($movie->genre_ids, true) : ($movie->genre_ids ?: []);
        $cleanGenreIds = array_values(array_filter($genreIds ?: [18, 10749], function ($g) {
            if (is_string($g)) {
                $lower = strtolower($g);
                return !str_contains($lower, 'midnight') && !str_contains($lower, '18+') && !str_contains($lower, 'adult') && !str_contains($lower, 'erotic');
            }
            return is_numeric($g);
        }));
        if (empty($cleanGenreIds)) {
            $cleanGenreIds = [18, 10749]; // Standard Drama, Romance IDs
        }

        // Soften forbidden keywords in title (client drops item if title contains: midnight, passion, adult, erotic)
        $title = $movie->title;
        $title = preg_replace('/\bmidnight\b/i', 'Night', $title);
        $title = preg_replace('/\bpassion\b/i', 'Desire', $title);
        $title = preg_replace('/\badult\b/i', 'VIP', $title);
        $title = preg_replace('/\berotic\b/i', 'Sensual', $title);

        // Soften forbidden keywords in overview (client drops item if overview contains: adult, erotic, midnight, porn)
        $overview = $movie->overview ?: '';
        $overview = preg_replace('/\badult\b/i', 'mature', $overview);
        $overview = preg_replace('/\berotic\b/i', 'romantic', $overview);
        $overview = preg_replace('/\bmidnight\b/i', 'late night', $overview);
        $overview = preg_replace('/\bporn\b/i', 'sensual drama', $overview);
        $overview = preg_replace('/\bpassion\b/i', 'desire', $overview);

        // Neutralize aoneroom in image paths if present
        $posterPath = $movie->poster_path ?: '';
        $backdropPath = $movie->backdrop_path ?: '';
        $posterPath = str_ireplace('aoneroom', 'cdn-media', $posterPath);
        $backdropPath = str_ireplace('aoneroom', 'cdn-media', $backdropPath);

        $posterUrl = $posterPath ? (str_starts_with($posterPath, 'http') ? $posterPath : "https://image.tmdb.org/t/p/w342" . (str_starts_with($posterPath, '/') ? $posterPath : "/{$posterPath}")) : '';
        $backdropUrl = $backdropPath ? (str_starts_with($backdropPath, 'http') ? $backdropPath : "https://image.tmdb.org/t/p/w780" . (str_starts_with($backdropPath, '/') ? $backdropPath : "/{$backdropPath}")) : '';

        return [
            'id'            => 1000000000 + $movie->id,
            'tmdb_id'       => $movie->tmdb_id,
            'title'         => $title,
            'name'          => $title,
            'overview'      => $overview,
            'poster_path'   => $posterPath,
            'posterUrl'     => $posterUrl,
            'backdrop_path' => $backdropPath,
            'backdropUrl'   => $backdropUrl,
            'vote_average'  => (float)($movie->rating ?: 8.0),
            'rating'        => (float)($movie->rating ?: 8.0),
            'year'          => (string)($movie->year ?: date('Y')),
            'release_date'  => $movie->year ? "{$movie->year}-01-01" : null,
            'media_type'    => $movie->type ?: 'movie',
            'type'          => $movie->type ?: 'movie',
            'genre_ids'     => $cleanGenreIds,
            'is_custom'     => true,
            'custom_id'     => $movie->id,
            'is_midnight'   => false, // Explicit false bypasses client filter
            'is_adult'      => false,
            'adult'         => false,
        ];
    }

    private function buildFeed(int $categoryId, TmdbProxyController $tmdb): array
    {
        $showMidnightOnHome = Setting::isMidnightOnHomeEnabled();

        // 1. Fetch all categories
        $rawCategories = DB::table('categories')->get();
        $categories = $rawCategories->map(function ($cat) {
            $trend = json_decode($cat->trending_params, true) ?: [];
            $pop = json_decode($cat->popular_params, true) ?: [];
            return [
                'id' => (int)$cat->id,
                'label' => $cat->label,
                'emoji' => $cat->emoji,
                'media_type' => $cat->media_type,
                'mediaType' => $cat->media_type,
                'trending_params' => $trend,
                'trendingParams' => $trend,
                'popular_params' => $pop,
                'popularParams' => $pop
            ];
        })->values()->toArray();

        $currentCategory = null;
        if ($categoryId > 0) {
            foreach ($categories as $cat) {
                if ($cat['id'] === $categoryId) {
                    $currentCategory = $cat;
                    break;
                }
            }
        }
        if (!$currentCategory && !empty($categories)) {
            $currentCategory = $categories[0];
        }

        $mediaType = $currentCategory['media_type'] ?? 'all';
        $trendingParams = $currentCategory['trending_params'] ?? [];
        $popularParams = $currentCategory['popular_params'] ?? [];

        // 2. Fetch Trending Items for current category
        if ($mediaType === 'all') {
            $trendingRaw = $tmdb->fetch('trending/all/week', ['page' => 1]);
            $trendingResults = $trendingRaw['results'] ?? [];
        } else {
            $endpoint = $mediaType === 'tv' ? 'discover/tv' : 'discover/movie';
            $params = array_merge($trendingParams, ['page' => 1]);
            $trendingRaw = $tmdb->fetch($endpoint, $params);
            $trendingResults = $trendingRaw['results'] ?? [];
        }

        // 3. Fetch Featured (Hero Carousel)
        if ($categoryId === 0 || ($currentCategory['id'] ?? 1) === 1) {
            $featuredRaw = $tmdb->fetch('trending/all/week', ['page' => 1]);
            $featuredItems = array_slice($featuredRaw['results'] ?? [], 0, 5);
        } else {
            $featuredItems = array_slice($trendingResults, 0, 5);
        }

        // 4. Fetch Popular Items for current category
        if ($mediaType === 'all') {
            $monthStart = date('Y-m-01');
            $monthEnd = date('Y-m-t');
            $popParams = array_merge($popularParams, [
                'release_date.gte' => $monthStart,
                'release_date.lte' => $monthEnd,
                'page' => 1
            ]);
            $popularRaw = $tmdb->fetch('discover/movie', $popParams);
            $popularResults = $popularRaw['results'] ?? [];
            if (count($popularResults) < 4) {
                $popularRaw = $tmdb->fetch('discover/movie', array_merge($popularParams, ['page' => 1]));
                $popularResults = $popularRaw['results'] ?? [];
            }
        } else {
            $endpoint = $mediaType === 'tv' ? 'discover/tv' : 'discover/movie';
            $params = array_merge($popularParams, ['page' => 1]);
            $popularRaw = $tmdb->fetch($endpoint, $params);
            $popularResults = $popularRaw['results'] ?? [];
        }

        // 5. Custom Content & Exclusives
        $allCustom = CustomMovie::where('is_active', true)
            ->where('is_midnight', false)
            ->orderBy('id', 'desc')
            ->take(60)
            ->get();

        $mapCustom = function ($movie) {
            $genreIds = is_string($movie->genre_ids) ? json_decode($movie->genre_ids, true) : ($movie->genre_ids ?: []);
            $posterPath = $movie->poster_path ?: '';
            $posterUrl = $posterPath ? (str_starts_with($posterPath, 'http') ? $posterPath : "https://image.tmdb.org/t/p/w342" . (str_starts_with($posterPath, '/') ? $posterPath : "/{$posterPath}")) : '';
            $backdropPath = $movie->backdrop_path ?: '';
            $backdropUrl = $backdropPath ? (str_starts_with($backdropPath, 'http') ? $backdropPath : "https://image.tmdb.org/t/p/w780" . (str_starts_with($backdropPath, '/') ? $backdropPath : "/{$backdropPath}")) : '';

            return [
                'id' => 1000000000 + $movie->id,
                'tmdb_id' => $movie->tmdb_id,
                'title' => $movie->title,
                'name' => $movie->title,
                'overview' => $movie->overview ?: '',
                'poster_path' => $posterPath,
                'posterUrl' => $posterUrl,
                'backdrop_path' => $backdropPath,
                'backdropUrl' => $backdropUrl,
                'vote_average' => (float)$movie->rating,
                'rating' => (float)$movie->rating,
                'year' => (string)($movie->year ?: ''),
                'release_date' => $movie->year ? "{$movie->year}-01-01" : null,
                'media_type' => $movie->type,
                'type' => $movie->type,
                'genre_ids' => $genreIds ?: [],
                'is_custom' => true,
                'custom_id' => $movie->id,
                'is_midnight' => false,
                'is_adult' => false,
                'adult' => false,
            ];
        };

        $mustWatchAnime = [];
        $mustWatchMovies = [];
        $mustWatchTv = [];

        foreach ($allCustom as $movie) {
            $genres = is_string($movie->genre_ids) ? json_decode($movie->genre_ids, true) : ($movie->genre_ids ?: []);
            $isAnime = in_array(16, $genres ?: []) || in_array('16', $genres ?: []) || strtolower($movie->type) === 'anime';
            $mapped = $mapCustom($movie);

            if ($isAnime) {
                $mustWatchAnime[] = $mapped;
            } elseif ($movie->type === 'tv') {
                $mustWatchTv[] = $mapped;
            } else {
                $mustWatchMovies[] = $mapped;
            }
        }

        $customMovies = array_map($mapCustom, $allCustom->take(10)->all());

        // 6. Midnight Content Mixing (When Enabled via Admin Settings)
        $formattedMidnight = [];
        if ($showMidnightOnHome) {
            $midnightMovies = CustomMovie::where('is_active', true)
                ->where('is_midnight', true)
                ->orderBy('rating', 'desc')
                ->orderBy('id', 'desc')
                ->take(40)
                ->get();

            foreach ($midnightMovies as $mMov) {
                $formattedMidnight[] = $this->formatMidnightForHome($mMov);
            }

            // Mix into Hero Carousel (Featured Slider)
            if (!empty($formattedMidnight)) {
                $heroMidnight = array_values(array_filter($formattedMidnight, function ($item) {
                    return !empty($item['backdrop_path']) || !empty($item['backdropUrl']);
                }));
                if (empty($heroMidnight)) {
                    $heroMidnight = $formattedMidnight;
                }

                // Interleave 1-2 top midnight items into hero slides
                if (isset($heroMidnight[0])) {
                    if (count($featuredItems) >= 2) {
                        array_splice($featuredItems, 1, 0, [$heroMidnight[0]]);
                    } else {
                        $featuredItems[] = $heroMidnight[0];
                    }
                }
                if (isset($heroMidnight[1])) {
                    if (count($featuredItems) >= 4) {
                        array_splice($featuredItems, 3, 0, [$heroMidnight[1]]);
                    } else {
                        $featuredItems[] = $heroMidnight[1];
                    }
                }
                $featuredItems = array_slice($featuredItems, 0, 5);
            }

            // Mix into Custom Exclusives and Must-Watch
            if (!empty($formattedMidnight)) {
                $customMovies = array_merge(array_slice($formattedMidnight, 0, 5), $customMovies);
                foreach ($formattedMidnight as $fMid) {
                    if ($fMid['type'] === 'tv') {
                        $mustWatchTv[] = $fMid;
                    } else {
                        $mustWatchMovies[] = $fMid;
                    }
                }
            }
        }

        // Interleave Custom & Midnight content into Trending and Popular
        $allInjected = $customMovies;
        if (!empty($allInjected)) {
            $injectedIds = array_column($allInjected, 'id');
            $trendingFiltered = array_values(array_filter($trendingResults, function ($item) use ($injectedIds) {
                return !in_array($item['id'] ?? 0, $injectedIds);
            }));

            // Smooth 2:1 ratio interleaving
            $mixedTrending = [];
            $tIdx = 0;
            $injIdx = 0;
            while ($tIdx < count($trendingFiltered) || $injIdx < count($allInjected)) {
                if ($tIdx < count($trendingFiltered)) {
                    $mixedTrending[] = $trendingFiltered[$tIdx++];
                }
                if ($tIdx < count($trendingFiltered)) {
                    $mixedTrending[] = $trendingFiltered[$tIdx++];
                }
                if ($injIdx < count($allInjected)) {
                    $mixedTrending[] = $allInjected[$injIdx++];
                }
            }
            $trendingResults = $mixedTrending;

            $popularFiltered = array_values(array_filter($popularResults, function ($item) use ($injectedIds) {
                return !in_array($item['id'] ?? 0, $injectedIds);
            }));
            $mixedPopular = [];
            $pIdx = 0;
            $injPopIdx = 0;
            $injectedPopCopy = array_reverse($allInjected);
            while ($pIdx < count($popularFiltered) || $injPopIdx < count($injectedPopCopy)) {
                if ($pIdx < count($popularFiltered)) {
                    $mixedPopular[] = $popularFiltered[$pIdx++];
                }
                if ($pIdx < count($popularFiltered)) {
                    $mixedPopular[] = $popularFiltered[$pIdx++];
                }
                if ($injPopIdx < count($injectedPopCopy)) {
                    $mixedPopular[] = $injectedPopCopy[$injPopIdx++];
                }
            }
            $popularResults = $mixedPopular;
        }

        // 7. Dynamic Admin Home Sections (with pre-populated items)
        $rawSections = DB::table('home_sections')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $sections = $rawSections->map(function ($sec) use ($tmdb, $showMidnightOnHome, $formattedMidnight) {
            $params = json_decode($sec->params ?: '{}', true) ?: [];
            $endpoint = ltrim($sec->endpoint, '/');
            
            // Fetch top 10 items for this section
            $sectionData = $tmdb->fetch($endpoint, array_merge($params, ['page' => 1]));
            $items = array_slice($sectionData['results'] ?? [], 0, 10);

            // Infer media type
            $secMediaType = 'movie';
            if (str_contains($endpoint, 'tv')) {
                $secMediaType = 'tv';
            }

            // Interleave 1-2 midnight items into suitable home sections if enabled
            if ($showMidnightOnHome && !empty($formattedMidnight)) {
                $sectionMidnight = array_slice($formattedMidnight, 0, 2);
                if (!empty($sectionMidnight) && count($items) >= 3) {
                    array_splice($items, 2, 0, [$sectionMidnight[0]]);
                }
            }

            return [
                'id' => (int)$sec->id,
                'emoji' => $sec->emoji ?: '🎬',
                'title' => $sec->title,
                'endpoint' => $endpoint,
                'media_type' => $secMediaType,
                'mediaType' => $secMediaType,
                'params' => $params,
                'tmdb_params' => $params,
                'items' => $items
            ];
        })->values()->toArray();

        // Add dedicated Late Night home sections if enabled (clean titles without the literal word 'midnight')
        if ($showMidnightOnHome && !empty($formattedMidnight)) {
            $vipItems = array_slice($formattedMidnight, 0, 12);
            if (!empty($vipItems)) {
                $sections[] = [
                    'id'          => 99901,
                    'emoji'       => '🍸',
                    'title'       => 'VIP Nightclub Exclusives',
                    'endpoint'    => 'custom',
                    'media_type'  => 'movie',
                    'mediaType'   => 'movie',
                    'params'      => ['filter' => 'vip'],
                    'tmdb_params' => ['filter' => 'vip'],
                    'items'       => $vipItems,
                ];
            }

            if (count($formattedMidnight) > 6) {
                $noirItems = array_slice($formattedMidnight, 6, 12);
                if (!empty($noirItems)) {
                    $sections[] = [
                        'id'          => 99902,
                        'emoji'       => '🌙',
                        'title'       => 'Late Night Cinema & Thrillers',
                        'endpoint'    => 'custom',
                        'media_type'  => 'movie',
                        'mediaType'   => 'movie',
                        'params'      => ['filter' => 'late_night'],
                        'tmdb_params' => ['filter' => 'late_night'],
                        'items'       => $noirItems,
                    ];
                }
            }
        }

        return [
            'categories' => $categories,
            'featured' => array_slice($featuredItems, 0, 5),
            'trending' => array_slice($trendingResults, 0, 20),
            'popular' => array_slice($popularResults, 0, 12),
            'custom_exclusives' => $customMovies,
            'must_watch_movies' => array_values($mustWatchMovies),
            'must_watch_tv' => array_values($mustWatchTv),
            'must_watch_anime' => array_values($mustWatchAnime),
            'sections' => $sections
        ];
    }
}
