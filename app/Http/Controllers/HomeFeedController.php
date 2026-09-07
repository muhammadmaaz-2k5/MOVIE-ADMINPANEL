<?php

namespace App\Http\Controllers;

use App\Models\CustomMovie;
use App\Models\HomeSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeFeedController extends Controller
{
    private const CACHE_TTL = 1800; // 30 minutes

    public function feed(Request $request, TmdbProxyController $tmdb)
    {
        $categoryId = (int)$request->query('category_id', 0);
        $cacheKey = "api_home_feed_{$categoryId}";
        $isHit = Cache::has($cacheKey);

        $feed = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($categoryId, $tmdb) {
            return $this->buildFeed($categoryId, $tmdb);
        });

        return response()->json($feed)
            ->header('Content-Type', 'application/json')
            ->header('X-Cache', $isHit ? 'HIT' : 'MISS')
            ->header('Cache-Control', 'public, max-age=' . self::CACHE_TTL);
    }

    private function buildFeed(int $categoryId, TmdbProxyController $tmdb): array
    {
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

        // 2. Fetch Featured (Hero Carousel, top 5 trending titles)
        $featuredRaw = $tmdb->fetch('trending/all/week', ['page' => 1]);
        $featuredItems = array_slice($featuredRaw['results'] ?? [], 0, 5);

        // 3. Fetch Trending Items for current category
        if ($mediaType === 'all') {
            $trendingRaw = $tmdb->fetch('trending/all/week', ['page' => 1]);
            $trendingResults = $trendingRaw['results'] ?? [];
        } else {
            $endpoint = $mediaType === 'tv' ? 'discover/tv' : 'discover/movie';
            $params = array_merge($trendingParams, ['page' => 1]);
            $trendingRaw = $tmdb->fetch($endpoint, $params);
            $trendingResults = $trendingRaw['results'] ?? [];
        }

        // 4. Fetch Popular Items for current category
        if ($mediaType === 'all') {
            $year = date('Y');
            $month = date('m');
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

        // 5. Custom content / exclusives
        $customMovies = CustomMovie::orderBy('id', 'desc')->take(10)->get()->map(function ($movie) {
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
                'media_type' => $movie->type,
                'is_custom' => true,
                'custom_id' => $movie->id,
            ];
        })->values()->toArray();

        // Inject custom content into trending & popular if appropriate
        if (!empty($customMovies)) {
            $customIds = array_column($customMovies, 'id');
            $trendingFiltered = array_filter($trendingResults, function ($item) use ($customIds) {
                return !in_array($item['id'] ?? 0, $customIds);
            });
            $trendingResults = array_merge($customMovies, array_values($trendingFiltered));

            $popularFiltered = array_filter($popularResults, function ($item) use ($customIds) {
                return !in_array($item['id'] ?? 0, $customIds);
            });
            $popularResults = array_merge($customMovies, array_values($popularFiltered));
        }

        // 6. Dynamic Admin Home Sections (with pre-populated items)
        $rawSections = DB::table('home_sections')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $sections = $rawSections->map(function ($sec) use ($tmdb) {
            $params = json_decode($sec->params, true) ?: [];
            $endpoint = ltrim($sec->endpoint, '/');
            
            // Fetch top 10 items for this section
            $sectionData = $tmdb->fetch($endpoint, array_merge($params, ['page' => 1]));
            $items = array_slice($sectionData['results'] ?? [], 0, 10);

            // Infer media type
            $secMediaType = 'movie';
            if (str_contains($endpoint, 'tv')) {
                $secMediaType = 'tv';
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

        return [
            'categories' => $categories,
            'featured' => array_slice($featuredItems, 0, 5),
            'trending' => array_slice($trendingResults, 0, 20),
            'popular' => array_slice($popularResults, 0, 12),
            'custom_exclusives' => $customMovies,
            'sections' => $sections
        ];
    }
}
