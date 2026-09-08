<?php

namespace App\Http\Controllers;

use App\Models\MidnightSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MidnightSectionController extends Controller
{
    public function managerView()
    {
        return view('admin.midnight-manager');
    }

    public function index()
    {
        $sections = MidnightSection::orderBy('sort_order')->get();
        return response()->json($sections);
    }

    public function store(Request $request)
    {
        if ($request->has('params') && is_string($request->input('params'))) {
            $decoded = json_decode($request->input('params'), true);
            if (is_array($decoded)) {
                $request->merge(['params' => $decoded]);
            }
        }

        $validated = $request->validate([
            'emoji'       => 'nullable|string|max:10',
            'title'       => 'required|string|max:255',
            'tagline'     => 'nullable|string|max:255',
            'endpoint'    => 'required|string|max:255',
            'params'      => 'nullable|array',
            'media_type'  => 'nullable|string|in:movie,tv',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'nullable|boolean',
        ]);

        if (empty($validated['media_type'])) {
            $validated['media_type'] = 'movie';
        }

        $section = MidnightSection::create($validated);
        self::clearMidnightCaches();

        return response()->json([
            'message' => 'Midnight section created successfully',
            'section' => $section,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $section = MidnightSection::findOrFail($id);

        if ($request->has('params') && is_string($request->input('params'))) {
            $decoded = json_decode($request->input('params'), true);
            if (is_array($decoded)) {
                $request->merge(['params' => $decoded]);
            }
        }

        $validated = $request->validate([
            'emoji'       => 'nullable|string|max:10',
            'title'       => 'required|string|max:255',
            'tagline'     => 'nullable|string|max:255',
            'endpoint'    => 'required|string|max:255',
            'params'      => 'nullable|array',
            'media_type'  => 'nullable|string|in:movie,tv',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'nullable|boolean',
        ]);

        $section->update($validated);
        self::clearMidnightCaches();

        return response()->json([
            'message' => 'Midnight section updated successfully',
            'section' => $section,
        ]);
    }

    public function destroy($id)
    {
        $section = MidnightSection::findOrFail($id);
        $section->delete();
        self::clearMidnightCaches();

        return response()->json(['message' => 'Midnight section deleted']);
    }

    public static function clearMidnightCaches(): void
    {
        Cache::forget('api_midnight_feed_v1');
    }
}
