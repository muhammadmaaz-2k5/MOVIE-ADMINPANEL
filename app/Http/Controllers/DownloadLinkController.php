<?php

namespace App\Http\Controllers;

use App\Models\DownloadLink;
use Illuminate\Http\Request;

class DownloadLinkController extends Controller
{
    // ── Public API ────────────────────────────────────────────────────────────

    /** GET /api/download-links/{type}/{id}  — fetch active links for one title */
    public function index(string $type, int $id)
    {
        if ($id >= 1000000000) {
            $customId = $id - 1000000000;
            $customMovie = \App\Models\CustomMovie::find($customId);

            if ($customMovie) {
                // Fetch links for the custom movie version specifically
                $customLinks = DownloadLink::forContent($type, $id)
                    ->active()
                    ->orderBy('sort_order')
                    ->orderBy('quality')
                    ->get();

                // Fetch links for the base movie version
                $baseLinks = DownloadLink::forContent($type, $customMovie->tmdb_id)
                    ->active()
                    ->orderBy('sort_order')
                    ->orderBy('quality')
                    ->get();

                $links = $customLinks->merge($baseLinks);
                return response()->json($links);
            }
        }

        $links = DownloadLink::forContent($type, $id)
            ->active()
            ->orderBy('sort_order')
            ->orderBy('quality')
            ->get();

        return response()->json($links);
    }

    // ── Admin API ─────────────────────────────────────────────────────────────

    /** GET /admin/api/download-links  — list all (optionally filtered) */
    public function adminIndex(Request $request)
    {
        $query = DownloadLink::query();

        if ($search = $request->query('search')) {
            $query->where('content_title', 'like', "%{$search}%");
        }
        if ($type = $request->query('type')) {
            $query->where('content_type', $type);
        }

        $links = $query->orderByDesc('updated_at')->paginate(20);
        return response()->json($links);
    }

    /** POST /admin/api/download-links — create a new link */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content_type'   => 'required|in:movie,tv',
            'content_id'     => 'required|integer',
            'content_title'  => 'required|string|max:255',
            'content_poster' => 'nullable|string',
            'server_name'    => 'required|string|max:100',
            'server_icon'    => 'nullable|string|max:10',
            'quality'        => 'required|in:360p,480p,720p,1080p,2160p,4K,Blu-ray,CAM',
            'language'       => 'nullable|string|max:50',
            'file_size'      => 'nullable|string|max:30',
            'download_url'   => 'required|url',
            'notes'          => 'nullable|string|max:255',
            'is_active'      => 'boolean',
            'sort_order'     => 'integer',
        ]);

        $link = DownloadLink::create($validated);
        return response()->json($link, 201);
    }

    /** PUT /admin/api/download-links/{id} — update */
    public function update(Request $request, int $id)
    {
        $link = DownloadLink::findOrFail($id);

        $validated = $request->validate([
            'content_type'   => 'sometimes|in:movie,tv',
            'content_id'     => 'sometimes|integer',
            'content_title'  => 'sometimes|string|max:255',
            'content_poster' => 'nullable|string',
            'server_name'    => 'sometimes|string|max:100',
            'server_icon'    => 'nullable|string|max:10',
            'quality'        => 'sometimes|in:360p,480p,720p,1080p,2160p,4K,Blu-ray,CAM',
            'language'       => 'nullable|string|max:50',
            'file_size'      => 'nullable|string|max:30',
            'download_url'   => 'sometimes|url',
            'notes'          => 'nullable|string|max:255',
            'is_active'      => 'boolean',
            'sort_order'     => 'integer',
        ]);

        $link->update($validated);
        return response()->json($link);
    }

    /** DELETE /admin/api/download-links/{id} */
    public function destroy(int $id)
    {
        DownloadLink::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    /** GET /admin/download-manager — page view */
    public function managerView()
    {
        return view('admin.download-manager');
    }
}
