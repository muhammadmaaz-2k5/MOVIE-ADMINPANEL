<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VideoServerController extends Controller
{
    public function index()
    {
        $servers = DB::table('video_servers')->get();
        return response()->json($servers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'label' => 'required|string|max:100',
            'icon' => 'nullable|string|max:2056',
            'movie_url_template' => 'required|string|max:2056',
            'tv_url_template' => 'required|string|max:2056'
        ]);

        try {
            DB::table('video_servers')->insert([
                'name' => $request->input('name'),
                'label' => $request->input('label'),
                'icon' => $request->input('icon') ?: '🔗',
                'movie_url_template' => $request->input('movie_url_template'),
                'tv_url_template' => $request->input('tv_url_template'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Video server created successfully.']);
        } catch (\Exception $e) {
            Log::error('VideoServerController@store error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to create server.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'label' => 'required|string|max:100',
            'icon' => 'nullable|string|max:2056',
            'movie_url_template' => 'required|string|max:2056',
            'tv_url_template' => 'required|string|max:2056'
        ]);

        try {
            DB::table('video_servers')->where('id', $id)->update([
                'name' => $request->input('name'),
                'label' => $request->input('label'),
                'icon' => $request->input('icon') ?: '🔗',
                'movie_url_template' => $request->input('movie_url_template'),
                'tv_url_template' => $request->input('tv_url_template'),
                'updated_at' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Video server updated successfully.']);
        } catch (\Exception $e) {
            Log::error('VideoServerController@update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update server.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::table('video_servers')->where('id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Video server deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('VideoServerController@destroy error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete server.'], 500);
        }
    }
}
