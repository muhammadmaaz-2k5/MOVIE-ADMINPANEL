<?php

namespace App\Http\Controllers;

use App\Models\PromotedApp;
use Illuminate\Http\Request;

class PromotedAppController extends Controller
{
    /**
     * Public API for Android / Mobile clients
     */
    public function publicIndex()
    {
        $apps = PromotedApp::where('is_active', true)
            ->orderBy('is_featured', 'desc')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($apps);
    }

    /**
     * Admin view
     */
    public function managerView()
    {
        $apps = PromotedApp::orderBy('is_featured', 'desc')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.promoted-apps', compact('apps'));
    }

    /**
     * Admin API: List all apps
     */
    public function adminIndex()
    {
        $apps = PromotedApp::orderBy('is_featured', 'desc')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($apps);
    }

    /**
     * Admin API: Store a new promoted app
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:191',
            'tagline'        => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'category'       => 'required|string|max:100',
            'package_name'   => 'nullable|string|max:191',
            'play_store_url' => 'nullable|string|max:255',
            'icon_url'       => 'nullable|string|max:255',
            'banner_url'     => 'nullable|string|max:255',
            'rating'         => 'nullable|numeric|min:1|max:5',
            'downloads'      => 'nullable|string|max:50',
            'badge'          => 'nullable|string|max:50',
            'sort_order'     => 'nullable|integer',
            'is_featured'    => 'nullable|boolean',
            'is_active'      => 'nullable|boolean',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active']   = $request->has('is_active') ? $request->boolean('is_active') : true;
        $validated['rating']      = $validated['rating'] ?? 4.8;
        $validated['downloads']   = $validated['downloads'] ?? '100K+';
        $validated['sort_order']  = $validated['sort_order'] ?? 0;

        $app = PromotedApp::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'App added successfully!',
            'app'     => $app
        ]);
    }

    /**
     * Admin API: Update promoted app
     */
    public function update(Request $request, $id)
    {
        $app = PromotedApp::findOrFail($id);

        $validated = $request->validate([
            'name'           => 'required|string|max:191',
            'tagline'        => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'category'       => 'required|string|max:100',
            'package_name'   => 'nullable|string|max:191',
            'play_store_url' => 'nullable|string|max:255',
            'icon_url'       => 'nullable|string|max:255',
            'banner_url'     => 'nullable|string|max:255',
            'rating'         => 'nullable|numeric|min:1|max:5',
            'downloads'      => 'nullable|string|max:50',
            'badge'          => 'nullable|string|max:50',
            'sort_order'     => 'nullable|integer',
            'is_featured'    => 'nullable|boolean',
            'is_active'      => 'nullable|boolean',
        ]);

        if ($request->has('is_featured')) {
            $validated['is_featured'] = $request->boolean('is_featured');
        }
        if ($request->has('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        $app->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'App updated successfully!',
            'app'     => $app
        ]);
    }

    /**
     * Admin API: Toggle active status
     */
    public function toggleStatus($id)
    {
        $app = PromotedApp::findOrFail($id);
        $app->is_active = !$app->is_active;
        $app->save();

        return response()->json([
            'success'   => true,
            'is_active' => $app->is_active,
            'message'   => $app->is_active ? 'App activated' : 'App deactivated'
        ]);
    }

    /**
     * Admin API: Toggle featured status
     */
    public function toggleFeatured($id)
    {
        $app = PromotedApp::findOrFail($id);
        $app->is_featured = !$app->is_featured;
        $app->save();

        return response()->json([
            'success'     => true,
            'is_featured' => $app->is_featured,
            'message'     => $app->is_featured ? 'App marked as Featured Spotlight' : 'Featured spotlight removed'
        ]);
    }

    /**
     * Admin API: Delete promoted app
     */
    public function destroy($id)
    {
        $app = PromotedApp::findOrFail($id);
        $app->delete();

        return response()->json([
            'success' => true,
            'message' => 'App deleted successfully'
        ]);
    }
}
