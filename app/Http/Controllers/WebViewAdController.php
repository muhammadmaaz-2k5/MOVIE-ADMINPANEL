<?php

namespace App\Http\Controllers;

use App\Models\WebViewAd;
use Illuminate\Http\Request;

class WebViewAdController extends Controller
{
    public function managerView()
    {
        return view('admin.webview-ad-manager');
    }

    public function index()
    {
        $ads = WebViewAd::orderBy('sort_order')->get();
        return response()->json($ads);
    }

    public function publicIndex()
    {
        $ads = WebViewAd::where('is_enabled', true)
            ->orderBy('sort_order')
            ->get();
        return response()->json($ads);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|in:top,bottom,both',
            'ad_code' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_enabled' => 'nullable|boolean'
        ]);

        $ad = WebViewAd::create($validated);
        return response()->json($ad, 201);
    }

    public function update(Request $request, $id)
    {
        $ad = WebViewAd::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|in:top,bottom,both',
            'ad_code' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_enabled' => 'nullable|boolean'
        ]);

        $ad->update($validated);
        return response()->json($ad);
    }

    public function destroy($id)
    {
        $ad = WebViewAd::findOrFail($id);
        $ad->delete();
        return response()->json(['message' => 'Deleted']);
    }
}