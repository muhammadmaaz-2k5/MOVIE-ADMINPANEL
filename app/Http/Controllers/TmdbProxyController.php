<?php

namespace App\Http\Controllers;

use App\Models\CustomMovie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TmdbProxyController extends Controller
{
    private const OFFSET = 1000000000; // 1 Billion Offset for Custom Movies

    /**
     * Parse query string preserving dot-notation keys.
     * PHP's $_GET and parse_str() both convert dots (.) to underscores (_), breaking
     * TMDB date filters like release_date.gte. This function preserves them.
     */
    private function parseRawQuery(string $queryString): array
    {
        $params = [];
        if (empty($queryString)) return $params;
        foreach (explode('&', $queryString) as $pair) {
            if (str_contains($pair, '=')) {
                [$key, $val] = explode('=', $pair, 2);
                $params[urldecode($key)] = urldecode($val);
            } else {
                $params[urldecode($pair)] = '';
            }
        }
        return $params;
    }

    public function proxy(Request $request, $path)
    {
        $token = env('TMDB_BEARER_TOKEN');
        $baseUrl = 'https://api.themoviedb.org/3';
        // Parse the raw query string to preserve dot-notation params like release_date.gte
        // (PHP's $_GET and parse_str() both convert dots to underscores, breaking TMDB date filters)
        $queryParams = $this->parseRawQuery($request->getQueryString() ?? '');

        // Translate dubbed languages for TMDb to return both Hollywood (en) and original language
        $langCode = isset($queryParams['with_original_language']) ? $queryParams['with_original_language'] : null;
        if ($langCode && $langCode !== 'en') {
            $dubbedCodes = ['hi', 'bn', 'ur', 'pa', 'ta', 'te', 'ml', 'kn', 'ar', 'fr', 'es'];
            $cleanLang = strtolower(explode('-', $langCode)[0]);
            if (in_array($cleanLang, $dubbedCodes)) {
                $queryParams['with_original_language'] = "en|{$cleanLang}";
            }
        }

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
                
                $this->applyFilters($dbQuery, $queryParams);

                $customMovies = $dbQuery->get();
                
                // Format custom movies to match TMDB search structure
                $resultsList = isset($tmdbResults['results']) ? $tmdbResults['results'] : [];

                // Format custom movies to match TMDB search structure
                $customResults = $customMovies->map(function ($m) use ($resultsList) {
                    $popularity = 100.0;
                    foreach ($resultsList as $item) {
                        if (isset($item['id']) && $item['id'] == $m->tmdb_id) {
                            $popularity = isset($item['popularity']) ? (float)$item['popularity'] : 100.0;
                            // Add small boost to rank the custom version right above the original TMDB version
                            $popularity += 0.001;
                            break;
                        }
                    }

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
                        'popularity' => $popularity,
                        'is_custom' => true,
                        'language' => $m->language
                    ];
                })->toArray();

                // Merge: custom movies first, then TMDB results
                $mergedResults = array_merge($customResults, $resultsList);

                $tmdbResults['results'] = $mergedResults;
                $tmdbResults['total_results'] = (isset($tmdbResults['total_results']) ? $tmdbResults['total_results'] : 0) + count($customResults);

                return response()->json($tmdbResults);
            }
        }

        // 3. Intercept Discover requests to inject Custom Movies based on applied filters
        if (preg_match('/^discover\/(movie|tv)$/', $path, $matches)) {
            $type = $matches[1]; // 'movie' or 'tv'
            $page = (int)($queryParams['page'] ?? 1);

            if ($page === 1) {
                // Start building DB query
                $dbQuery = CustomMovie::where('is_active', true)->where('type', $type);
                $this->applyFilters($dbQuery, $queryParams);
                $customMovies = $dbQuery->get();

                // Fetch TMDB discover results
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

                // Post-filter TMDB results by year range
                // (TMDB can return out-of-range results when using OR language expressions like 'en|hi')
                $yearGte = $queryParams['release_date.gte'] ?? $queryParams['primary_release_date.gte'] ?? $queryParams['first_air_date.gte'] ?? null;
                $yearLte = $queryParams['release_date.lte'] ?? $queryParams['primary_release_date.lte'] ?? $queryParams['first_air_date.lte'] ?? null;
                $yearGteInt = $yearGte ? (int)substr($yearGte, 0, 4) : null;
                $yearLteInt = $yearLte ? (int)substr($yearLte, 0, 4) : null;
                $exactYear  = $queryParams['year'] ?? null;

                $resultsList = isset($tmdbResults['results']) ? $tmdbResults['results'] : [];

                if ($yearGteInt || $yearLteInt || $exactYear) {
                    $resultsList = array_values(array_filter($resultsList, function($r) use ($yearGteInt, $yearLteInt, $exactYear) {
                        $dateStr = $r['release_date'] ?? $r['first_air_date'] ?? '';
                        if (strlen($dateStr) < 4) return true; // no date, keep
                        $yr = (int)substr($dateStr, 0, 4);
                        if ($exactYear && (string)$yr !== (string)$exactYear) return false;
                        if ($yearGteInt && $yr < $yearGteInt) return false;
                        if ($yearLteInt && $yr > $yearLteInt) return false;
                        return true;
                    }));
                    $tmdbResults['results'] = $resultsList;
                }

                // Merge custom content at top
                if ($customMovies->isNotEmpty()) {
                    $customResults = $customMovies->map(function ($m) use ($resultsList) {
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
                    $sortBy = $queryParams['sort_by'] ?? 'popularity.desc';
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
                            $valA = isset($a['popularity']) ? (float)$a['popularity'] : 0.0;
                            $valB = isset($b['popularity']) ? (float)$b['popularity'] : 0.0;
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
                }

                return response()->json($tmdbResults);
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

    private function applyFilters($dbQuery, array $params)
    {

        // 1. Language Filter
        // If a specific language filter is applied, it takes full priority over country.
        // This prevents conflicts when e.g. country=US (English) + language=Hindi Dub
        // are combined — without this both would apply as AND, returning zero results.
        $langCode = $params['with_original_language'] ?? null;
        $hasLanguageFilter = false;
        if ($langCode) {
            // Strip any OR expression we added for TMDB (e.g. 'en|hi') — for custom DB use only the target lang
            if (str_contains($langCode, '|')) {
                $parts = explode('|', $langCode);
                // Take the non-English part (the dubbed language)
                $langCode = count($parts) > 1 ? end($parts) : $parts[0];
            }
            $langCode = strtolower(explode('-', $langCode)[0]);
            $langMap = [
                'hi' => 'Hindi',
                'pa' => 'Punjabi',
                'ta' => 'Tamil',
                'te' => 'Telugu',
                'ml' => 'Malayalam',
                'kn' => 'Kannada',
                'bn' => 'Bengali',
                'ur' => 'Urdu',
                'ar' => 'Arabic',
                'es' => 'Spanish',
                'fr' => 'French',
                'en' => 'English',
                'ja' => 'Japanese',
                'ko' => 'Korean',
                'zh' => 'Chinese',
            ];
            $langName = $langMap[$langCode] ?? null;
            if ($langName) {
                $dbQuery->where('language', 'like', "%{$langName}%");
                $hasLanguageFilter = true;
            } else {
                $dbQuery->whereRaw('1 = 0'); // force empty if language not supported
            }
        }

        // 2. Genre Filter
        if ($genreId = ($params['with_genres'] ?? null)) {
            $genres = explode(',', $genreId);
            $dbQuery->where(function($q) use ($genres) {
                foreach ($genres as $gid) {
                    $q->orWhereJsonContains('genre_ids', (int)$gid);
                }
            });
        }

        // 3. Year Filter
        $year = $params['year'] ?? $params['primary_release_year'] ?? $params['first_air_date_year'] ?? null;
        if ($year && is_numeric($year)) {
            $dbQuery->where('year', $year);
        } else {
            $releaseGte = $params['release_date.gte'] ?? $params['primary_release_date.gte'] ?? $params['first_air_date.gte'] ?? null;
            if ($releaseGte && preg_match('/^(\d{4})/', $releaseGte, $m)) {
                $dbQuery->where('year', '>=', $m[1]);
            }
            $releaseLte = $params['release_date.lte'] ?? $params['primary_release_date.lte'] ?? $params['first_air_date.lte'] ?? null;
            if ($releaseLte && preg_match('/^(\d{4})/', $releaseLte, $m)) {
                $dbQuery->where('year', '<=', $m[1]);
            }
        }

        // 4. Country Filter — skip if a specific language filter was already applied
        // (Language takes priority to avoid AND conflict, e.g. US=English AND Hindi Dub = no results)
        if (!$hasLanguageFilter) {
            $countryToLanguages = [
                'US' => ['English', 'en'],
                'GB' => ['English', 'en'],
                'JP' => ['Japanese', 'ja'],
                'KR' => ['Korean', 'ko'],
                'IN' => ['Hindi', 'hi', 'Punjabi', 'pa', 'Tamil', 'ta', 'Telugu', 'te', 'Bengali', 'bn', 'Urdu', 'ur', 'Malayalam', 'ml', 'Kannada', 'kn'],
                'PK' => ['Urdu', 'ur', 'Punjabi', 'pa'],
                'ES' => ['Spanish', 'es'],
                'FR' => ['French', 'fr'],
                'CN' => ['Chinese', 'zh'],
                'AR' => ['Arabic', 'ar']
            ];

            $countryCode = $params['with_origin_country'] ?? $params['region'] ?? null;
            if ($countryCode) {
                $countryCode = strtoupper($countryCode);
                if (isset($countryToLanguages[$countryCode])) {
                    $allowedLangs = $countryToLanguages[$countryCode];
                    $dbQuery->where(function($q) use ($allowedLangs) {
                        foreach ($allowedLangs as $lang) {
                            $q->orWhere('language', 'like', "%{$lang}%");
                        }
                    });
                } else {
                    $dbQuery->whereRaw('1 = 0'); // force empty if country has no mapped language
                }
            }
        }

        return $dbQuery;
    }
}
