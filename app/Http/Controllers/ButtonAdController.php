<?php

namespace App\Http\Controllers;

use App\Models\ButtonAd;
use Illuminate\Http\Request;

class ButtonAdController extends Controller
{
    public function managerView()
    {
        return view('admin.button-ad-manager');
    }

    public function index()
    {
        $ads = ButtonAd::orderBy('sort_order')->get();
        return response()->json($ads);
    }

    public function publicIndex()
    {
        $screen = request()->query('screen', 'home');
        
        $ads = ButtonAd::where('is_enabled', true)
            ->where('target_screen', $screen)
            ->orderBy('sort_order')
            ->get();
        return response()->json($ads);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'button_text' => 'required|string|max:255',
            'button_link' => 'required|string|max:2000',
            'button_color' => 'nullable|string|max:7',
            'button_icon' => 'nullable|string|max:10',
            'target_screen' => 'required|in:home,movies,tv,anime,downloads,search',
            'sort_order' => 'nullable|integer',
            'is_enabled' => 'nullable|boolean'
        ]);

        $ad = ButtonAd::create($validated);
        return response()->json($ad, 201);
    }

    public function update(Request $request, $id)
    {
        $ad = ButtonAd::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'button_text' => 'required|string|max:255',
            'button_link' => 'required|string|max:2000',
            'button_color' => 'nullable|string|max:7',
            'button_icon' => 'nullable|string|max:10',
            'target_screen' => 'required|in:home,movies,tv,anime,downloads,search',
            'sort_order' => 'nullable|integer',
            'is_enabled' => 'nullable|boolean'
        ]);

        $ad->update($validated);
        return response()->json($ad);
    }

    public function destroy($id)
    {
        $ad = ButtonAd::findOrFail($id);
        $ad->delete();
        return response()->json(['message' => 'Deleted']);
    }
}