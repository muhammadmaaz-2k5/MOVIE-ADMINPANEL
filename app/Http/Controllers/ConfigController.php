<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    public function categories()
    {
        $cacheKey = 'api_config_categories';
        $isHit = Cache::has($cacheKey);

        $categories = Cache::remember($cacheKey, 86400, function () {
            return DB::table('categories')->get()->map(function($cat) {
                $trend = json_decode($cat->trending_params, true) ?: [];
                $pop = json_decode($cat->popular_params, true) ?: [];

                return [
                    'id' => $cat->id,
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
        });

        return response()->json($categories)
            ->header('X-Cache', $isHit ? 'HIT' : 'MISS')
            ->header('Cache-Control', 'public, max-age=86400');
    }

    public function servers()
    {
        if (Setting::isSafeReviewMode()) {
            return response()->json([], 200)
                ->header('X-App-Mode', 'safe_review');
        }

        // Shield server configurations from external web crawlers in production
        $appClient = request()->header('X-App-Client');
        $appSignature = request()->header('X-App-Signature');
        if ($appClient !== 'engora-android' && $appSignature !== 'nzbox-sec-token-2026') {
            if (!auth()->check() && !app()->environment('local')) {
                return response()->json([], 200);
            }
        }

        $id = request()->query('id');
        $season = request()->query('season');
        $episode = request()->query('episode');
        $type = request()->query('type');
        $customIdParam = request()->query('custom_id');
        $isCustom = filter_var(request()->query('is_custom'), FILTER_VALIDATE_BOOLEAN) || $type === 'custom' || !empty($customIdParam);

        $cacheKey = "api_config_servers_{$id}_{$type}_{$season}_{$episode}_" . ($isCustom ? 'custom' : 'std');
        $isHit = Cache::has($cacheKey);

        $result = Cache::remember($cacheKey, 3600, function () use ($id, $season, $episode, $type, $customIdParam, $isCustom) {
            $customMovie = null;

            if ($customIdParam && is_numeric($customIdParam)) {
                $customMovie = \App\Models\CustomMovie::with('streams')->find((int)$customIdParam);
            } elseif ($id && is_numeric($id)) {
                $numId = (int)$id;
                if ($numId >= 1000000000) {
                    $customMovie = \App\Models\CustomMovie::with('streams')->find($numId - 1000000000);
                } elseif ($isCustom) {
                    $customMovie = \App\Models\CustomMovie::with('streams')->find($numId);
                } else {
                    $maybeCustom = \App\Models\CustomMovie::with('streams')->find($numId);
                    if ($maybeCustom && $maybeCustom->streams->isNotEmpty()) {
                        $customMovie = $maybeCustom;
                    }
                }
            }

            if ($customMovie) {
                $allStreams = $customMovie->streams;

                if ($customMovie->type === 'tv' && $season && $episode) {
                    $streams = $allStreams->where('season_number', (int)$season)
                                          ->where('episode_number', (int)$episode);
                    
                    if ($streams->isEmpty()) {
                        $streams = $allStreams->whereNull('season_number');
                    }
                    if ($streams->isEmpty()) {
                        $streams = $allStreams;
                    }
                } else {
                    $streams = $allStreams;
                }

                $customServers = $streams->sortBy('sort_order')->map(function($stream) {
                    return [
                        'id' => $stream->id,
                        'name' => $stream->server_name,
                        'label' => $stream->server_name,
                        'icon' => $stream->server_icon ?: '🔗',
                        'movie_url_template' => $stream->stream_url,
                        'tv_url_template' => $stream->stream_url,
                        'stream_url' => $stream->stream_url,
                        'is_custom' => true,
                    ];
                })->values()->toArray();

                if (!empty($customServers)) {
                    return $customServers;
                }

                if ($customMovie->tmdb_id) {
                    return DB::table('video_servers')->get()->map(function($server) use ($customMovie) {
                        $movieTpl = str_replace('{id}', $customMovie->tmdb_id, $server->movie_url_template);
                        $tvTpl    = str_replace('{id}', $customMovie->tmdb_id, $server->tv_url_template);
                        return [
                            'id' => $server->id,
                            'name' => $server->name,
                            'label' => $server->label,
                            'icon' => $server->icon,
                            'movie_url_template' => $movieTpl,
                            'tv_url_template' => $tvTpl,
                            'is_custom' => true,
                        ];
                    })->values()->toArray();
                }

                return [];
            }

            return DB::table('video_servers')->get()->values()->toArray();
        });

        return response()->json($result)
            ->header('X-Cache', $isHit ? 'HIT' : 'MISS')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public static function clearServersCache(): void
    {
        Cache::flush();
    }

    public function homeSections()
    {
        $cacheKey = 'api_config_home_sections';
        $isHit = Cache::has($cacheKey);

        $sections = Cache::remember($cacheKey, 21600, function () {
            return DB::table('home_sections')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(function($section) {
                    return [
                        'id' => $section->id,
                        'emoji' => $section->emoji,
                        'title' => $section->title,
                        'endpoint' => $section->endpoint,
                        'params' => json_decode($section->params, true) ?: []
                    ];
                })->values()->toArray();
        });

        return response()->json($sections)
            ->header('X-Cache', $isHit ? 'HIT' : 'MISS')
            ->header('Cache-Control', 'public, max-age=21600');
    }

    public function globalSettings()
    {
        $cacheKey = 'api_config_settings';
        $isHit = Cache::has($cacheKey);

        $settings = Cache::remember($cacheKey, 21600, function () {
            return SettingsController::buildSettingsResponse();
        });

        return response()->json($settings)
            ->header('X-Cache', $isHit ? 'HIT' : 'MISS')
            ->header('Cache-Control', 'public, max-age=21600');
    }
}