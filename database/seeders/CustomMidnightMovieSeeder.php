<?php

namespace Database\Seeders;

use App\Models\CustomMovie;
use App\Models\CustomMovieStream;
use App\Models\MidnightSection;
use Illuminate\Database\Seeder;

class CustomMidnightMovieSeeder extends Seeder
{
    public function run(): void
    {
        $sections = MidnightSection::orderBy('sort_order')->get()->keyBy('id');

        $sampleItems = [
            // 🍾 VIP Nightclub Exclusives
            [
                'tmdb_id'              => 82023,
                'title'                => 'Hotel Desire: VIP Red Velvet',
                'type'                 => 'movie',
                'genre_ids'            => [18, 10749],
                'poster_path'          => '/47XRWH95ATv4szxdWHl723guWXP.jpg',
                'backdrop_path'        => '/wcUohmHc9oDZXarXDp905TYVui4.jpg',
                'overview'             => 'An exclusive late-night romance set in a luxury Berlin penthouse suite during a scorching summer heatwave.',
                'language'             => 'English',
                'rating'               => 8.4,
                'year'                 => '2024',
                'runtime'              => '98 min',
                'is_active'            => true,
                'is_midnight'          => true,
                'midnight_section_id'  => 1,
                'streams'              => [
                    ['server_name' => 'VIP Club 4K Stream', 'server_icon' => '🍾', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'],
                    ['server_name' => 'Ultra HD 1080p', 'server_icon' => '⚡', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4'],
                ],
            ],
            [
                'tmdb_id'              => 456931,
                'title'                => 'Midnight Reverie: Private Cabaret',
                'type'                 => 'movie',
                'genre_ids'            => [18, 14, 10749],
                'poster_path'          => '/sRINgACuZN3lNfREPBDpySvT1jY.jpg',
                'backdrop_path'        => '/bsqJUJ2psI0NwOFbhdQ8IPoavMj.jpg',
                'overview'             => 'A sensual reverie unfolds over one surreal night inside an underground cabaret theater of dreams.',
                'language'             => 'French',
                'rating'               => 7.9,
                'year'                 => '2023',
                'runtime'              => '105 min',
                'is_active'            => true,
                'is_midnight'          => true,
                'midnight_section_id'  => 1,
                'streams'              => [
                    ['server_name' => 'VIP VIP Cabaret Master', 'server_icon' => '🎭', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'],
                ],
            ],

            // 🌙 Neon Noir & Nightlife Crime
            [
                'tmdb_id'              => 6479,
                'title'                => 'Neon Syndicate: Tokyo Drift Noir',
                'type'                 => 'movie',
                'genre_ids'            => [80, 53, 28],
                'poster_path'          => '/w46Vw536HwNnEzOa7J24YH9DPRS.jpg',
                'backdrop_path'        => '/oz4U9eA6ilYf1tyiVuGmkftdLac.jpg',
                'overview'             => 'Rain-soaked neon alleys, high-stakes midnight heists, and lethal underground syndicates.',
                'language'             => 'Japanese',
                'rating'               => 8.8,
                'year'                 => '2024',
                'runtime'              => '118 min',
                'is_active'            => true,
                'is_midnight'          => true,
                'midnight_section_id'  => 2,
                'streams'              => [
                    ['server_name' => 'Tokyo Neon Ultra Server', 'server_icon' => '🏎️', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4'],
                ],
            ],
            [
                'tmdb_id'              => 950028,
                'title'                => 'After Hours Heist: Penthouse Intrigue',
                'type'                 => 'movie',
                'genre_ids'            => [80, 53],
                'poster_path'          => '/b7Dr8Chzse8VagexAporUu2RtLx.jpg',
                'backdrop_path'        => '/lEwqBGNR65KZv6Ej5ufcmhZu2y2.jpg',
                'overview'             => 'When a dinner invitation from enigmatic upstairs neighbors spirals into a high-stakes midnight extortion.',
                'language'             => 'English',
                'rating'               => 7.7,
                'year'                 => '2024',
                'runtime'              => '110 min',
                'is_active'            => true,
                'is_midnight'          => true,
                'midnight_section_id'  => 2,
                'streams'              => [
                    ['server_name' => 'Direct Master 4K', 'server_icon' => '💎', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4'],
                ],
            ],

            // 💋 After Hours & Passion
            [
                'tmdb_id'              => 741110,
                'title'                => 'Midnight Velvet: Passionate Rendezvous',
                'type'                 => 'movie',
                'genre_ids'            => [18, 10749],
                'poster_path'          => '/mdLNmqIehGximKCHZre65hCcYxb.jpg',
                'backdrop_path'        => '/7yYR8ulZdXBXFKmxGw1Uk28jnyH.jpg',
                'overview'             => 'An electric, forbidden romance that ignites deep in the quiet hours after the city goes dark.',
                'language'             => 'French',
                'rating'               => 8.2,
                'year'                 => '2023',
                'runtime'              => '102 min',
                'is_active'            => true,
                'is_midnight'          => true,
                'midnight_section_id'  => 3,
                'streams'              => [
                    ['server_name' => 'Velvet Stream HD', 'server_icon' => '💋', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4'],
                ],
            ],

            // 💀 Midnight Madness & Horror
            [
                'tmdb_id'              => 1010581,
                'title'                => 'Sinister Manor: 3 AM Watch',
                'type'                 => 'movie',
                'genre_ids'            => [27, 53],
                'poster_path'          => '/sd0RKOpnqESIWxU3sZwZhBsgAHl.jpg',
                'backdrop_path'        => '/vEMiPLEXfzF9NUzUkROtyDVZ4Ln.jpg',
                'overview'             => 'Disturbing screams and macabre secrets await inside a secluded mountain chateau after the clock strikes midnight.',
                'language'             => 'Spanish',
                'rating'               => 8.1,
                'year'                 => '2024',
                'runtime'              => '96 min',
                'is_active'            => true,
                'is_midnight'          => true,
                'midnight_section_id'  => 4,
                'streams'              => [
                    ['server_name' => 'Horror Chamber HD', 'server_icon' => '💀', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyBlazes.mp4'],
                ],
            ],

            // 🔮 Late Night Mindbenders
            [
                'tmdb_id'              => 1325734,
                'title'                => 'Lucid Labyrinth: The Midnight Paradox',
                'type'                 => 'movie',
                'genre_ids'            => [9648, 53, 878],
                'poster_path'          => '/rnIOUhzwJDfgQakx8EjoNyItKgs.jpg',
                'backdrop_path'        => '/1oKLEA9JOhvaBwLpqjROisvWMy7.jpg',
                'overview'             => 'A twisted psychological mindbender questioning memory, lucid dreams, and identity at 2 AM.',
                'language'             => 'English',
                'rating'               => 8.9,
                'year'                 => '2024',
                'runtime'              => '124 min',
                'is_active'            => true,
                'is_midnight'          => true,
                'midnight_section_id'  => 5,
                'streams'              => [
                    ['server_name' => 'Mindbender Quantum Stream', 'server_icon' => '🔮', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/WeAreGoingOnBullrun.mp4'],
                ],
            ],

            // Regular items (is_midnight = false) to demonstrate searching & adding to Midnight in Admin
            [
                'tmdb_id'              => 372058,
                'title'                => 'Cyber City: Neo Tokyo Nights',
                'type'                 => 'tv',
                'genre_ids'            => [16, 18, 878],
                'poster_path'          => '/vfJFJPepRKapMd5G2ro7klIRysq.jpg',
                'backdrop_path'        => '/mMtUybQ6hL24FXo0F3Z4j2KG7kZ.jpg',
                'overview'             => 'A futuristic cyberpunk anime series chronicling nocturnal street racers and rogue rogue androids.',
                'language'             => 'Japanese',
                'rating'               => 9.1,
                'year'                 => '2024',
                'runtime'              => '24 min',
                'is_active'            => true,
                'is_midnight'          => false,
                'midnight_section_id'  => null,
                'streams'              => [
                    ['server_name' => 'Anime Server 1', 'server_icon' => '⛩️', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/WhatCarCanYouGetForAGrand.mp4'],
                ],
            ],
            [
                'tmdb_id'              => 402431,
                'title'                => 'Nightfall Chronicle: Midnight Season',
                'type'                 => 'tv',
                'genre_ids'            => [18, 80],
                'poster_path'          => '/xDGbZ0JJ3mYaGKy4Nzd9Kph6M9L.jpg',
                'backdrop_path'        => '/fyZ6SDUS4o9jp2EHxfZa3qS9ean.jpg',
                'overview'             => 'An intense drama following a detective navigating the nocturnal criminal underbelly of Chicago.',
                'language'             => 'English',
                'rating'               => 8.3,
                'year'                 => '2024',
                'runtime'              => '45 min',
                'is_active'            => true,
                'is_midnight'          => false,
                'midnight_section_id'  => null,
                'streams'              => [
                    ['server_name' => 'Prime Stream HD', 'server_icon' => '📺', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'],
                ],
            ],
        ];

        foreach ($sampleItems as $item) {
            $streams = $item['streams'] ?? [];
            unset($item['streams']);

            $movie = CustomMovie::create($item);

            foreach ($streams as $idx => $s) {
                CustomMovieStream::create([
                    'custom_movie_id' => $movie->id,
                    'server_name'     => $s['server_name'],
                    'server_icon'     => $s['server_icon'] ?? '⚡',
                    'stream_url'      => $s['stream_url'],
                    'sort_order'      => $idx,
                ]);
            }
        }
    }
}
