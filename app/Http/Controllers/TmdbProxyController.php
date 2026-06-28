<?php

namespace App\Http\Controllers;

use App\Models\CustomMovie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TmdbProxyController extends Controller
{
    private const OFFSET = 1000000000; // 1 Billion Offset for Custom Movies

    public function proxy(Request $request, $path)
    {
        $token = env('TMDB_BEARER_TOKEN');
        $baseUrl = 'https://api.themoviedb.org/3';
        $queryParams = $request->query();

        // 1. Intercept Details requests for Custom Movies (ID > 1 Billion)
        if (preg_match('/^(movie|tv)\/(\d+)(.*)$/', $path, $matches)) {
            $type = $matches[1];
            $id = (int)$matches[2];
            $suffix = $matches[3]; // e.g. "/credits", "/videos", "/similar"

            if ($id >= self::OFFSET) {
                $customId = $id - self::OFFSET;
                $customMovie = CustomMovie::find($customId);

                if ($customMovie) {
                    // If it is a sub-resource request (e.g. /credits, /videos, /similar, /reviews, /season/X)
                    if (!empty($suffix)) {
                        // Forward request using original TMDB ID
                        $newPath = "{$type}/{$customMovie->tmdb_id}{$suffix}";
                        try {
                            $response = Http::withoutVerifying()
                                ->withToken($token)
                                ->timeout(15)
                                ->get("{$baseUrl}/{$newPath}", $queryParams);
                            return response($response->body(), $response->status())
                                ->header('Content-Type', 'application/json');
                        } catch (\Exception $e) {
                            return response()->json(['error' => 'Failed to reach TMDB API'], 500);
                        }
                    }

                    // Main details request: load real TMDB data and override with custom values
                    try {
                        $tmdbResponse = Http::withoutVerifying()
                            ->withToken($token)
                            ->timeout(15)
                            ->get("{$baseUrl}/{$type}/{$customMovie->tmdb_id}", $queryParams);

                        if ($tmdbResponse->successful()) {
                            $data = $tmdbResponse->json();
                            
                            // Apply custom metadata overrides
                            $data['id'] = $id; // Keep the offset ID!
                            $data['title'] = $customMovie->title;
                            $data['name'] = $customMovie->title; // for TV Shows
                            $data['overview'] = $customMovie->overview ?: ($data['overview'] ?? '');
                            
                            if ($customMovie->poster_path) {
                                $data['poster_path'] = $customMovie->poster_path;
                            }
                            if ($customMovie->backdrop_path) {
                                $data['backdrop_path'] = $customMovie->backdrop_path;
                            }
                            if ($customMovie->rating) {
                                $data['vote_average'] = (float)$customMovie->rating;
                            }
                            if ($customMovie->year) {
                                if ($type === 'movie') {
                                    $data['release_date'] = "{$customMovie->year}-01-01";
                                } else {
                                    $data['first_air_date'] = "{$customMovie->year}-01-01";
                                }
                            }
                            $data['is_custom'] = true;
                            $data['custom_language'] = $customMovie->language;

                            return response()->json($data);
                        }
                    } catch (\Exception $e) {
                        // Fallback to local DB-only details if TMDB fails
                    }

                    // Fallback local response
                    return response()->json([
                        'id' => $id,
                        'title' => $customMovie->title,
                        'name' => $customMovie->title,
                        'overview' => $customMovie->overview,
                        'poster_path' => $customMovie->poster_path,
                        'backdrop_path' => $customMovie->backdrop_path,
                        'vote_average' => $customMovie->rating,
                        'release_date' => $customMovie->year ? "{$customMovie->year}-01-01" : null,
                        'first_air_date' => $customMovie->year ? "{$customMovie->year}-01-01" : null,
                        'genres' => [],
                        'is_custom' => true,
                        'custom_language' => $customMovie->language
                    ]);
                }
            }
        }

        // 2. Intercept Search requests to inject Custom Movies
        if (preg_match('/^search\/(multi|movie|tv)$/', $path, $matches)) {
            $searchType = $matches[1]; // 'multi', 'movie', or 'tv'
            $query = $request->query('query');

            if (!empty($query)) {
                // Fetch TMDB results first
                $tmdbResults = [];
                try {
                    $response = Http::withoutVerifying()
                        ->withToken($token)
                        ->timeout(15)
                        ->get("{$baseUrl}/{$path}", $queryParams);
                    
                    if ($response->successful()) {
                        $tmdbResults = $response->json();
                    }
                } catch (\Exception $e) {
                    // Ignore and use empty
                }

                // Fetch matching custom movies from DB
                $dbQuery = CustomMovie::where('is_active', true)
                    ->where('title', 'like', "%{$query}%");
                
                if ($searchType === 'movie') {
                    $dbQuery->where('type', 'movie');
                } elseif ($searchType === 'tv') {
                    $dbQuery->where('type', 'tv');
                }
                
                $customMovies = $dbQuery->get();
                
                // Format custom movies to match TMDB search structure
                $customResults = $customMovies->map(function ($m) {
                    return [
                        'id' => self::OFFSET + $m->id, // Offset ID
                        'title' => $m->title,
                        'name' => $m->title, // for multi/tv
                        'media_type' => $m->type,
                        'type' => $m->type,
                        'poster_path' => $m->poster_path,
                        'backdrop_path' => $m->backdrop_path,
                        'overview' => $m->overview,
                        'release_date' => $m->year ? "{$m->year}-01-01" : null,
                        'first_air_date' => $m->year ? "{$m->year}-01-01" : null,
                        'vote_average' => $m->rating,
                        'is_custom' => true,
                        'language' => $m->language
                    ];
                })->toArray();

                // Merge: custom movies first, then TMDB results
                $resultsList = isset($tmdbResults['results']) ? $tmdbResults['results'] : [];
                $mergedResults = array_merge($customResults, $resultsList);

                $tmdbResults['results'] = $mergedResults;
                $tmdbResults['total_results'] = (isset($tmdbResults['total_results']) ? $tmdbResults['total_results'] : 0) + count($customResults);

                return response()->json($tmdbResults);
            }
        }

        // 3. Intercept Discover requests to inject Custom Movies based on applied filters
        if (preg_match('/^discover\/(movie|tv)$/', $path, $matches)) {
            $type = $matches[1]; // 'movie' or 'tv'
            $page = (int)$request->query('page', 1);

            if ($page === 1) {
                $langCode = $request->query('with_original_language');
                $genreId = $request->query('with_genres');
                $releaseGte = $request->query('release_date.gte') ?: $request->query('first_air_date.gte');
                
                // Start building DB query
                $dbQuery = CustomMovie::where('is_active', true)->where('type', $type);

                // Language filtering
                if ($langCode) {
                    $langMap = [
                        'hi' => 'Hindi',
                        'pa' => 'Punjabi',
                        'ta' => 'Tamil',
                        'te' => 'Telugu',
                        'bn' => 'Bengali',
                        'ur' => 'Urdu',
                        'ar' => 'Arabic',
                        'es' => 'Spanish',
                        'fr' => 'French',
                        'en' => 'English',
                    ];
                    $langName = $langMap[strtolower($langCode)] ?? null;
                    if ($langName) {
                        $dbQuery->where('language', 'like', "%{$langName}%");
                    } else {
                        // If language requested is not custom, don't return custom movies
                        $dbQuery->whereRaw('1 = 0');
                    }
                }

                // Genre filtering
                if ($genreId) {
                    // Extract first genre ID if comma-separated
                    $gid = (int)head(explode(',', $genreId));
                    if ($gid > 0) {
                        $dbQuery->whereJsonContains('genre_ids', $gid);
                    }
                }

                // Year filtering
                if ($releaseGte) {
                    $year = substr($releaseGte, 0, 4);
                    if (is_numeric($year)) {
                        $dbQuery->where('year', $year);
                    }
                }

                $customMovies = $dbQuery->get();

                if ($customMovies->isNotEmpty()) {
                    // Fetch TMDB discover results first
                    $tmdbResults = [];
                    try {
                        $response = Http::withoutVerifying()
                            ->withToken($token)
                            ->timeout(15)
                            ->get("{$baseUrl}/{$path}", $queryParams);
                        
                        if ($response->successful()) {
                            $tmdbResults = $response->json();
                        }
                    } catch (\Exception $e) {
                        // Ignore
                    }

                    // Merge results
                    $resultsList = isset($tmdbResults['results']) ? $tmdbResults['results'] : [];
                    
                    // Format custom movies to match TMDB structure with base popularity mapping
                    $customResults = $customMovies->map(function ($m) use ($resultsList) {
                        // Find base movie in results to copy popularity
                        $popularity = 50.0;
                        foreach ($resultsList as $r) {
                            if (isset($r['id']) && $r['id'] === $m->tmdb_id) {
                                $popularity = isset($r['popularity']) ? (float)$r['popularity'] : 50.0;
                                break;
                            }
                        }

                        return [
                            'id' => self::OFFSET + $m->id, // Offset ID
                            'title' => $m->title,
                            'name' => $m->title, // for tv
                            'media_type' => $m->type,
                            'type' => $m->type,
                            'poster_path' => $m->poster_path,
                            'backdrop_path' => $m->backdrop_path,
                            'overview' => $m->overview,
                            'release_date' => $m->year ? "{$m->year}-01-01" : null,
                            'first_air_date' => $m->year ? "{$m->year}-01-01" : null,
                            'vote_average' => $m->rating ? (float)$m->rating : 0.0,
                            'popularity' => $popularity,
                            'is_custom' => true,
                            'language' => $m->language,
                            'genre_ids' => $m->genre_ids ?: []
                        ];
                    })->toArray();

                    $mergedResults = array_merge($customResults, $resultsList);

                    // Unified Sorting Algorithm
                    $sortBy = $request->query('sort_by', 'popularity.desc');
                    usort($mergedResults, function($a, $b) use ($sortBy) {
                        if ($sortBy === 'vote_average.desc') {
                            $valA = isset($a['vote_average']) ? (float)$a['vote_average'] : 0.0;
                            $valB = isset($b['vote_average']) ? (float)$b['vote_average'] : 0.0;
                            return $valB <=> $valA;
                        } elseif ($sortBy === 'release_date.desc' || $sortBy === 'first_air_date.desc') {
                            $valA = isset($a['release_date']) ? $a['release_date'] : (isset($a['first_air_date']) ? $a['first_air_date'] : '');
                            $valB = isset($b['release_date']) ? $b['release_date'] : (isset($b['first_air_date']) ? $b['first_air_date'] : '');
                            return strcmp($valB, $valA);
                        } else {
                            // Default: sort by popularity descending
                            $valA = isset($a['popularity']) ? (float)$a['popularity'] : 0.0;
                            $valB = isset($b['popularity']) ? (float)$b['popularity'] : 0.0;
                            // Boost custom movie slightly if equal or to give them priority
                            if (isset($a['is_custom']) && isset($b['is_custom'])) {
                                return $valB <=> $valA;
                            }
                            if (isset($a['is_custom'])) {
                                return ($valB <=> $valA) ?: -1;
                            }
                            if (isset($b['is_custom'])) {
                                return ($valB <=> $valA) ?: 1;
                            }
                            return $valB <=> $valA;
                        }
                    });

                    $tmdbResults['results'] = $mergedResults;
                    $tmdbResults['total_results'] = (isset($tmdbResults['total_results']) ? $tmdbResults['total_results'] : 0) + count($customResults);

                    return response()->json($tmdbResults);
                }
            }
        }

        // Default proxy flow
        try {
            $response = Http::withoutVerifying()
                ->withToken($token)
                ->timeout(15)
                ->get("{$baseUrl}/{$path}", $queryParams);
                
            return response($response->body(), $response->status())
                ->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to reach TMDB API',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
