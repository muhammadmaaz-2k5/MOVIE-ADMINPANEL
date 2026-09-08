<?php

namespace App\Http\Controllers;

use App\Models\CustomMovie;
use App\Models\MidnightSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MidnightSectionController extends Controller
{
    public function managerView()
    {
        return view('admin.midnight-manager');
    }

    /**
     * GET /admin/api/midnight-sections
     * List all manual categories with counts of assigned midnight movies
     */
    public function index()
    {
        $sections = MidnightSection::withCount(['customMovies' => function ($q) {
            $q->where('is_midnight', true)->where('is_active', true);
        }])->orderBy('sort_order')->get();

        return response()->json($sections);
    }

    /**
     * POST /admin/api/midnight-sections
     * Create a manual Midnight category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'emoji'       => 'nullable|string|max:10',
            'title'       => 'required|string|max:255',
            'tagline'     => 'nullable|string|max:255',
            'endpoint'    => 'nullable|string|max:255',
            'params'      => 'nullable|array',
            'media_type'  => 'nullable|string|in:movie,tv',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'nullable|boolean',
        ]);

        if (empty($validated['endpoint'])) {
            $validated['endpoint'] = 'manual';
        }
        if (empty($validated['media_type'])) {
            $validated['media_type'] = 'movie';
        }

        $section = MidnightSection::create($validated);
        self::clearMidnightCaches();

        return response()->json([
            'message' => 'Midnight category created successfully',
            'section' => $section,
        ], 201);
    }

    /**
     * PUT /admin/api/midnight-sections/{id}
     * Update a manual Midnight category
     */
    public function update(Request $request, $id)
    {
        $section = MidnightSection::findOrFail($id);

        $validated = $request->validate([
            'emoji'       => 'nullable|string|max:10',
            'title'       => 'required|string|max:255',
            'tagline'     => 'nullable|string|max:255',
            'endpoint'    => 'nullable|string|max:255',
            'params'      => 'nullable|array',
            'media_type'  => 'nullable|string|in:movie,tv',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'nullable|boolean',
        ]);

        if (empty($validated['endpoint'])) {
            $validated['endpoint'] = 'manual';
        }

        $section->update($validated);
        self::clearMidnightCaches();

        return response()->json([
            'message' => 'Midnight category updated successfully',
            'section' => $section,
        ]);
    }

    /**
     * DELETE /admin/api/midnight-sections/{id}
     */
    public function destroy($id)
    {
        $section = MidnightSection::findOrFail($id);
        $section->delete();
        self::clearMidnightCaches();

        return response()->json(['message' => 'Midnight category deleted']);
    }

    /**
     * GET /admin/api/midnight-content
     * List all movies, TV shows, and anime currently included in Midnight
     */
    public function content(Request $request)
    {
        $query = CustomMovie::with('midnightSection')->where('is_midnight', true);

        if ($search = $request->query('search')) {
            $query->where('title', 'like', "%{$search}%");
        }
        if ($catId = $request->query('category_id')) {
            $query->where('midnight_section_id', $catId);
        }
        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        $items = $query->orderByDesc('updated_at')->paginate(30);
        return response()->json($items);
    }

    /**
     * POST /admin/api/midnight-content/toggle/{id}
     * Quick toggle inclusion in Midnight or change assigned category
     */
    public function toggleContent(Request $request, $id)
    {
        $movie = CustomMovie::findOrFail($id);

        if ($request->has('is_midnight')) {
            $movie->is_midnight = filter_var($request->input('is_midnight'), FILTER_VALIDATE_BOOLEAN);
        }
        if ($request->has('midnight_section_id')) {
            $secId = $request->input('midnight_section_id');
            $movie->midnight_section_id = !empty($secId) ? (int)$secId : null;
        }

        $movie->save();
        self::clearMidnightCaches();

        return response()->json([
            'message' => 'Midnight content updated successfully',
            'movie'   => $movie->load('midnightSection'),
        ]);
    }

    /**
     * GET /admin/api/midnight-content/search-available
     * Search all custom movies/shows/anime to add them into Midnight
     */
    public function searchAvailable(Request $request)
    {
        $q = $request->query('q', '');
        $query = CustomMovie::query();
        if (!empty($q)) {
            $query->where('title', 'like', "%{$q}%");
        }

        $items = $query->take(20)->get(['id', 'title', 'type', 'poster_path', 'year', 'is_midnight', 'midnight_section_id']);
        return response()->json($items);
    }

    public static function clearMidnightCaches(): void
    {
        Cache::forget('api_midnight_feed_v1');
    }
}
