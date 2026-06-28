<?php

namespace App\Http\Controllers;

use App\Models\CustomMovie;
use App\Models\CustomMovieStream;
use Illuminate\Http\Request;

class CustomMovieController extends Controller
{
    // ── Public Search / Info API ──────────────────────────────────────────────

    /** GET /api/search/custom — search custom movies from DB */
    public function search(Request $request)
    {
        $q = $request->query('query');
        if (empty($q)) {
            return response()->json([]);
        }

        $movies = CustomMovie::where('is_active', true)
            ->where('title', 'like', "%{$q}%")
            ->get();

        return response()->json($movies);
    }

    /** GET /api/custom-movie/{id} — get details of custom movie + streams */
    public function getDetails(int $id)
    {
        $movie = CustomMovie::with(['streams' => function ($query) {
            $query->orderBy('sort_order')->orderBy('server_name');
        }])->findOrFail($id);

        return response()->json($movie);
    }

    /** GET /api/custom-content — list active custom movies/shows */
    public function publicIndex(Request $request)
    {
        $query = CustomMovie::where('is_active', true);

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }
        
        if ($genre = $request->query('genre')) {
            $query->whereJsonContains('genre_ids', (int)$genre);
        }

        $limit = $request->query('limit', 20);
        $movies = $query->orderByDesc('updated_at')->limit((int)$limit)->get();

        $mapped = $movies->map(function($movie) {
            $genreIds = is_string($movie->genre_ids) ? json_decode($movie->genre_ids, true) : $movie->genre_ids;
            return [
                'id' => (int)$movie->tmdb_id, // real tmdb_id for mobile app
                'custom_id' => (int)$movie->id, // local database ID for web app custom details
                'tmdb_id' => (int)$movie->tmdb_id,
                'title' => $movie->title,
                'type' => $movie->type,
                'posterUrl' => $movie->poster_path ? (str_starts_with($movie->poster_path, 'http') ? $movie->poster_path : "https://image.tmdb.org/t/p/w342" . $movie->poster_path) : 'https://placehold.co/342x513/1E1E2E/FFF?text=No+Image',
                'backdropUrl' => $movie->backdrop_path ? (str_starts_with($movie->backdrop_path, 'http') ? $movie->backdrop_path : "https://image.tmdb.org/t/p/w780" . $movie->backdrop_path) : '',
                'rating' => $movie->rating ? (double)$movie->rating : 0.0,
                'year' => $movie->year,
                'genre_ids' => $genreIds ?: [],
                'is_custom' => true
            ];
        });

        return response()->json($mapped);
    }

    // ── Admin CRUD API ────────────────────────────────────────────────────────

    /** GET /admin/api/custom-movies — list custom movies for dashboard */
    public function adminIndex(Request $request)
    {
        $query = CustomMovie::withCount('streams');

        if ($search = $request->query('search')) {
            $query->where('title', 'like', "%{$search}%");
        }
        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }
        if ($genre = $request->query('genre')) {
            $query->whereJsonContains('genre_ids', (int)$genre);
        }

        $movies = $query->orderByDesc('updated_at')->paginate(20);
        return response()->json($movies);
    }

    /** POST /admin/api/custom-movies — create */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tmdb_id'       => 'required|integer',
            'title'         => 'required|string|max:255',
            'type'          => 'required|in:movie,tv',
            'genre_ids'     => 'nullable|array',
            'poster_path'   => 'nullable|string|max:255',
            'backdrop_path' => 'nullable|string|max:255',
            'overview'      => 'nullable|string',
            'language'      => 'nullable|string|max:100',
            'rating'        => 'nullable|numeric',
            'year'          => 'nullable|string|max:10',
            'runtime'       => 'nullable|string|max:20',
            'is_active'     => 'boolean'
        ]);

        $movie = CustomMovie::create($validated);
        return response()->json($movie, 201);
    }

    /** PUT /admin/api/custom-movies/{id} — update */
    public function update(Request $request, int $id)
    {
        $movie = CustomMovie::findOrFail($id);

        $validated = $request->validate([
            'title'         => 'sometimes|required|string|max:255',
            'genre_ids'     => 'nullable|array',
            'poster_path'   => 'nullable|string|max:255',
            'backdrop_path' => 'nullable|string|max:255',
            'overview'      => 'nullable|string',
            'language'      => 'nullable|string|max:100',
            'rating'        => 'nullable|numeric',
            'year'          => 'nullable|string|max:10',
            'runtime'       => 'nullable|string|max:20',
            'is_active'     => 'boolean'
        ]);

        $movie->update($validated);
        return response()->json($movie);
    }

    /** DELETE /admin/api/custom-movies/{id} */
    public function destroy(int $id)
    {
        CustomMovie::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    // ── Streams API for Custom Movies ─────────────────────────────────────────

    /** GET /admin/api/custom-movies/{id}/streams */
    public function getStreams(int $id)
    {
        $streams = CustomMovieStream::where('custom_movie_id', $id)
            ->orderBy('sort_order')
            ->get();
        return response()->json($streams);
    }

    /** POST /admin/api/custom-movies/{id}/streams */
    public function storeStream(Request $request, int $id)
    {
        $validated = $request->validate([
            'server_name'    => 'required|string|max:255',
            'server_icon'    => 'nullable|string|max:10',
            'stream_url'     => 'required|url',
            'season_number'  => 'nullable|integer',
            'episode_number' => 'nullable|integer',
            'sort_order'     => 'integer'
        ]);

        $validated['custom_movie_id'] = $id;

        $stream = CustomMovieStream::create($validated);
        return response()->json($stream, 201);
    }

    /** PUT /admin/api/custom-streams/{id} */
    public function updateStream(Request $request, int $id)
    {
        $stream = CustomMovieStream::findOrFail($id);

        $validated = $request->validate([
            'server_name'    => 'sometimes|required|string|max:255',
            'server_icon'    => 'nullable|string|max:10',
            'stream_url'     => 'sometimes|required|url',
            'season_number'  => 'nullable|integer',
            'episode_number' => 'nullable|integer',
            'sort_order'     => 'integer'
        ]);

        $stream->update($validated);
        return response()->json($stream);
    }

    /** DELETE /admin/api/custom-streams/{id} */
    public function destroyStream(int $id)
    {
        CustomMovieStream::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    // ── View Renderer ─────────────────────────────────────────────────────────

    public function managerView()
    {
        return view('admin.movie-manager');
    }

    public function tvManagerView()
    {
        return view('admin.tv-manager');
    }

    public function animeManagerView()
    {
        return view('admin.anime-manager');
    }
}
