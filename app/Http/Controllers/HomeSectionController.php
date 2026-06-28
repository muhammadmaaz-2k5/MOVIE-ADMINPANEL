<?php

namespace App\Http\Controllers;

use App\Models\HomeSection;
use Illuminate\Http\Request;

class HomeSectionController extends Controller
{
    public function managerView()
    {
        return view('admin.home-section-manager');
    }

    public function index()
    {
        $sections = HomeSection::orderBy('sort_order')->get();
        return response()->json($sections);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'emoji' => 'nullable|string|max:10',
            'title' => 'required|string|max:255',
            'endpoint' => 'required|string|max:255',
            'params' => 'nullable|array',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $section = HomeSection::create($validated);
        return response()->json($section, 201);
    }

    public function update(Request $request, $id)
    {
        $section = HomeSection::findOrFail($id);
        
        $validated = $request->validate([
            'emoji' => 'nullable|string|max:10',
            'title' => 'required|string|max:255',
            'endpoint' => 'required|string|max:255',
            'params' => 'nullable|array',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $section->update($validated);
        return response()->json($section);
    }

    public function destroy($id)
    {
        $section = HomeSection::findOrFail($id);
        $section->delete();
        return response()->json(['message' => 'Deleted']);
    }
}