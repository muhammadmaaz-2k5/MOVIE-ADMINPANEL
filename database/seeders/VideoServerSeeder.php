<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VideoServerSeeder extends Seeder
{
    public function run(): void
    {
        try {
            DB::table('video_servers')->truncate();
        } catch (\Exception $e) {
            DB::table('video_servers')->delete();
        }

        $servers = [
            [
                'name' => 'vidfast',
                'label' => 'VidFast',
                'icon' => '⚡',
                'movie_url_template' => 'https://vidfast.pro/movie/{id}?autoPlay=true&theme=6C5CE7',
                'tv_url_template' => 'https://vidfast.pro/tv/{id}/{season}/{episode}?autoPlay=true&theme=6C5CE7&nextButton=true&autoNext=true'
            ],
            [
                'name' => 'vidsrc',
                'label' => 'Vidsrc',
                'icon' => '🌍',
                'movie_url_template' => 'https://vidsrc.to/embed/movie/{id}',
                'tv_url_template' => 'https://vidsrc.to/embed/tv/{id}/{season}/{episode}'
            ]
        ];

        foreach ($servers as $srv) {
            DB::table('video_servers')->insert($srv);
        }
    }
}
