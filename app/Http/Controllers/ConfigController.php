<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    public function categories()
    {
        $categories = DB::table('categories')->get()->map(function($cat) {
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
        });

        return response()->json($categories);
    }

    public function servers()
    {
        $id = request()->query('id');
        $season = request()->query('season');
        $episode = request()->query('episode');

        $customMovie = null;
        if ($id && is_numeric($id) && (int)$id >= 1000000000) {
            $customId = (int)$id - 1000000000;
            $customMovie = \App\Models\CustomMovie::with('streams')->find($customId);
        }

        if ($customMovie) {
            $allStreams = $customMovie->streams;

            if ($customMovie->type === 'tv' && $season && $episode) {
                // Try to find season/episode specific streams
                $streams = $allStreams->where('season_number', (int)$season)
                                      ->where('episode_number', (int)$episode);
                
                if ($streams->isEmpty()) {
                    // Fallback to generic streams with no season
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
                    'tv_url_template' => $stream->stream_url
                ];
            })->values();

            if ($customServers->isNotEmpty()) {
                return response()->json($customServers);
            }

            // If no custom streams configured, return default servers with pre-replaced tmdb_id
            $defaultServers = DB::table('video_servers')->get()->map(function($server) use ($customMovie) {
                $movieTpl = str_replace('{id}', $customMovie->tmdb_id, $server->movie_url_template);
                $tvTpl    = str_replace('{id}', $customMovie->tmdb_id, $server->tv_url_template);
                return [
                    'id' => $server->id,
                    'name' => $server->name,
                    'label' => $server->label,
                    'icon' => $server->icon,
                    'movie_url_template' => $movieTpl,
                    'tv_url_template' => $tvTpl
                ];
            });

            return response()->json($defaultServers);
        }

        $servers = DB::table('video_servers')->get();
        return response()->json($servers);
    }
}
