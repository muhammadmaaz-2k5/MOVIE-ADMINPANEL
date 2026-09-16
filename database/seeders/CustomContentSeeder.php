<?php

namespace Database\Seeders;

use App\Models\CustomMovie;
use App\Models\CustomMovieStream;
use App\Http\Controllers\HomeFeedController;
use Illuminate\Database\Seeder;

class CustomContentSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // ══════════════════════════════════════════════════════════════════════
            // 🎬 MUST WATCH MOVIES (Non-Midnight)
            // ══════════════════════════════════════════════════════════════════════
            [
                'tmdb_id'       => 693134,
                'title'         => 'Dune: Part Two',
                'type'          => 'movie',
                'genre_ids'     => [878, 12],
                'poster_path'   => '/1pdfLvkbY9ohJlCjQH2CZjjYVvJ.jpg',
                'backdrop_path' => '/xOMo8BRK7PfcJv9JCnx7s520b22.jpg',
                'overview'      => 'Follow the mythic journey of Paul Atreides as he unites with Chani and the Fremen while on a path of revenge against the conspirators who destroyed his family.',
                'language'      => 'English',
                'rating'        => 8.2,
                'year'          => '2024',
                'runtime'       => '166 min',
                'is_active'     => true,
                'is_midnight'   => false,
                'midnight_section_id' => null,
                'streams'       => [
                    ['server_name' => 'FastCloud 4K', 'server_icon' => '⚡', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'],
                    ['server_name' => 'Ultra HD 1080p', 'server_icon' => '🎬', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4'],
                ],
            ],
            [
                'tmdb_id'       => 872585,
                'title'         => 'Oppenheimer',
                'type'          => 'movie',
                'genre_ids'     => [18, 36],
                'poster_path'   => '/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg',
                'backdrop_path' => '/nb3xI8XI3w4pMVZ38VijbsyBqP4.jpg',
                'overview'      => 'The story of J. Robert Oppenheimer’s role in the development of the atomic bomb during World War II.',
                'language'      => 'English',
                'rating'        => 8.1,
                'year'          => '2023',
                'runtime'       => '180 min',
                'is_active'     => true,
                'is_midnight'   => false,
                'midnight_section_id' => null,
                'streams'       => [
                    ['server_name' => 'Prime Master 4K', 'server_icon' => '🔥', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'],
                    ['server_name' => 'HD Stream', 'server_icon' => '▶', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/WeAreGoingOnBullrun.mp4'],
                ],
            ],
            [
                'tmdb_id'       => 157336,
                'title'         => 'Interstellar',
                'type'          => 'movie',
                'genre_ids'     => [12, 18, 878],
                'poster_path'   => '/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
                'backdrop_path' => '/rAiYTsqAlzkPkXSmPuSVipnh9nv.jpg',
                'overview'      => 'The adventures of a group of explorers who make use of a newly discovered wormhole to surpass the limitations on human space travel and conquer the vast distances involved in an interstellar voyage.',
                'language'      => 'English',
                'rating'        => 8.4,
                'year'          => '2014',
                'runtime'       => '169 min',
                'is_active'     => true,
                'is_midnight'   => false,
                'midnight_section_id' => null,
                'streams'       => [
                    ['server_name' => 'Cosmic 4K HDR', 'server_icon' => '🌌', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4'],
                ],
            ],
            [
                'tmdb_id'       => 603692,
                'title'         => 'John Wick: Chapter 4',
                'type'          => 'movie',
                'genre_ids'     => [28, 53, 80],
                'poster_path'   => '/vZloFAK7NKnMGKEHvY2ilRXYvhJ.jpg',
                'backdrop_path' => '/7I6VUdPj6tQECNHdviJkUHD2389.jpg',
                'overview'      => 'With the price on his head ever increasing, John Wick uncovers a path to defeating The High Table. But before he can earn his freedom, Wick must face off against a new enemy with powerful alliances across the globe.',
                'language'      => 'English',
                'rating'        => 7.8,
                'year'          => '2023',
                'runtime'       => '169 min',
                'is_active'     => true,
                'is_midnight'   => false,
                'midnight_section_id' => null,
                'streams'       => [
                    ['server_name' => 'Action Master 4K', 'server_icon' => '🎯', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4'],
                ],
            ],
            [
                'tmdb_id'       => 569094,
                'title'         => 'Spider-Man: Across the Spider-Verse',
                'type'          => 'movie',
                'genre_ids'     => [28, 12, 878],
                'poster_path'   => '/8Vt6mWEReuy4Of61Lnj5Xj704m8.jpg',
                'backdrop_path' => '/4HodYYKEIsGOdinkGi2Ucz6X9i0.jpg',
                'overview'      => 'After reuniting with Gwen Stacy, Brooklyn’s full-time, friendly neighborhood Spider-Man is catapulted across the Multiverse, where he encounters the Spider Society, a team of Spider-People charged with protecting the Multiverse’s very existence.',
                'language'      => 'English',
                'rating'        => 8.4,
                'year'          => '2023',
                'runtime'       => '140 min',
                'is_active'     => true,
                'is_midnight'   => false,
                'midnight_section_id' => null,
                'streams'       => [
                    ['server_name' => 'Web Slinger 4K', 'server_icon' => '🕷️', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'],
                ],
            ],

            // ══════════════════════════════════════════════════════════════════════
            // 📺 MUST WATCH TV SHOWS (Non-Midnight)
            // ══════════════════════════════════════════════════════════════════════
            [
                'tmdb_id'       => 126308,
                'title'         => 'Shōgun',
                'type'          => 'tv',
                'genre_ids'     => [18, 10768],
                'poster_path'   => '/7O4iVfOMQmdCSxhOg1WNzG1AgYT.jpg',
                'backdrop_path' => '/5zmiBoMw7TN0V36Y5hX1E6zTj1m.jpg',
                'overview'      => 'In Japan in the year 1600, Lord Yoshii Toranaga is fighting for his life as his enemies on the Council of Regents unite against him, when a mysterious European ship is found stranded in a nearby fishing village.',
                'language'      => 'English',
                'rating'        => 8.5,
                'year'          => '2024',
                'runtime'       => '60 min',
                'is_active'     => true,
                'is_midnight'   => false,
                'midnight_section_id' => null,
                'streams'       => [
                    ['server_name' => 'Shogun 4K Master', 'server_icon' => '⚔️', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'],
                    ['server_name' => 'HD Stream', 'server_icon' => '📺', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4'],
                ],
            ],
            [
                'tmdb_id'       => 100088,
                'title'         => 'The Last of Us',
                'type'          => 'tv',
                'genre_ids'     => [18, 10759, 10765],
                'poster_path'   => '/uKvVjHNqB5VmOrdxqAt2V7J78ED.jpg',
                'backdrop_path' => '/uDgy6hyPd82kOHh6I95FLtLnj6p.jpg',
                'overview'      => 'Twenty years after modern civilization has been destroyed, Joel, a hardened survivor, is hired to smuggle Ellie, a 14-year-old girl, out of an oppressive quarantine zone.',
                'language'      => 'English',
                'rating'        => 8.6,
                'year'          => '2023',
                'runtime'       => '55 min',
                'is_active'     => true,
                'is_midnight'   => false,
                'midnight_section_id' => null,
                'streams'       => [
                    ['server_name' => 'Survivor 4K Stream', 'server_icon' => '🍄', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'],
                ],
            ],
            [
                'tmdb_id'       => 66732,
                'title'         => 'Stranger Things',
                'type'          => 'tv',
                'genre_ids'     => [18, 10765, 9648],
                'poster_path'   => '/49WJfeN0moxb9IPfGn8AIqMGskD.jpg',
                'backdrop_path' => '/56v2KjBlU4XaOv9rVYEQypROD7P.jpg',
                'overview'      => 'When a young boy vanishes, a small town uncovers a mystery involving secret experiments, terrifying supernatural forces and one strange little girl.',
                'language'      => 'English',
                'rating'        => 8.6,
                'year'          => '2022',
                'runtime'       => '50 min',
                'is_active'     => true,
                'is_midnight'   => false,
                'midnight_section_id' => null,
                'streams'       => [
                    ['server_name' => 'Hawkins 4K Stream', 'server_icon' => '⚡', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4'],
                ],
            ],
            [
                'tmdb_id'       => 1396,
                'title'         => 'Breaking Bad',
                'type'          => 'tv',
                'genre_ids'     => [18, 80],
                'poster_path'   => '/ztkUQFLlC19CCMYHW9o1zWhJRNq.jpg',
                'backdrop_path' => '/tsRy63Mu5cu8etL1X7ZLyf7UP1M.jpg',
                'overview'      => 'Walter White, a New Mexico chemistry teacher, is diagnosed with Stage III cancer and given a prognosis of two years to live. He decides to enter the dangerous world of drugs to secure his family’s financial future.',
                'language'      => 'English',
                'rating'        => 8.9,
                'year'          => '2008',
                'runtime'       => '47 min',
                'is_active'     => true,
                'is_midnight'   => false,
                'midnight_section_id' => null,
                'streams'       => [
                    ['server_name' => 'Albuquerque 4K', 'server_icon' => '🧪', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/WeAreGoingOnBullrun.mp4'],
                ],
            ],
            [
                'tmdb_id'       => 84958,
                'title'         => 'Loki',
                'type'          => 'tv',
                'genre_ids'     => [18, 10765, 10759],
                'poster_path'   => '/voHUAt6Cuq2n9GQXZWq79NTq1Ij.jpg',
                'backdrop_path' => '/kCGlIMHnOm8JPXq3rXM6c5wMxcT.jpg',
                'overview'      => 'After stealing the Tesseract during the events of “Avengers: Endgame,” an alternate version of Loki is brought to the mysterious Time Variance Authority.',
                'language'      => 'English',
                'rating'        => 8.2,
                'year'          => '2023',
                'runtime'       => '52 min',
                'is_active'     => true,
                'is_midnight'   => false,
                'midnight_section_id' => null,
                'streams'       => [
                    ['server_name' => 'TVA Timeline 4K', 'server_icon' => '⏳', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4'],
                ],
            ],

            // ══════════════════════════════════════════════════════════════════════
            // ⛩️ MUST WATCH ANIME (Non-Midnight, Genre 16)
            // ══════════════════════════════════════════════════════════════════════
            [
                'tmdb_id'       => 202411,
                'title'         => 'Solo Leveling',
                'type'          => 'tv',
                'genre_ids'     => [16, 10759, 10765],
                'poster_path'   => '/geCRueV3ElhRTr0xtJuPxJ8PGzs.jpg',
                'backdrop_path' => '/4MCKNAc6AbWjEsM2cr7hJnZ9vsm.jpg',
                'overview'      => 'They say whatever doesn’t kill you makes you stronger, but that’s not the case for the world’s weakest hunter, Sung Jinwoo. After being brutally slaughtered by monsters in a high-ranking dungeon, Jinwoo came back with the System.',
                'language'      => 'Japanese',
                'rating'        => 8.6,
                'year'          => '2024',
                'runtime'       => '24 min',
                'is_active'     => true,
                'is_midnight'   => false,
                'midnight_section_id' => null,
                'streams'       => [
                    ['server_name' => 'Shadow Monarch 4K', 'server_icon' => '🗡️', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'],
                    ['server_name' => 'Anime Fast HD', 'server_icon' => '⛩️', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'],
                ],
            ],
            [
                'tmdb_id'       => 85937,
                'title'         => 'Demon Slayer: Kimetsu no Yaiba',
                'type'          => 'tv',
                'genre_ids'     => [16, 10759, 10765],
                'poster_path'   => '/xUfRZu2mi8jH6SzQEJGP6tjBuYj.jpg',
                'backdrop_path' => '/3IhA0bF1kG6kC8rN7T4K4zV1i0m.jpg',
                'overview'      => 'It is the Taisho Period in Japan. Tanjiro, a kindhearted boy who sells charcoal for a living, finds his family slaughtered by a demon. To make matters worse, his younger sister Nezuko, the sole survivor, has been transformed into a demon.',
                'language'      => 'Japanese',
                'rating'        => 8.7,
                'year'          => '2019',
                'runtime'       => '24 min',
                'is_active'     => true,
                'is_midnight'   => false,
                'midnight_section_id' => null,
                'streams'       => [
                    ['server_name' => 'Nichirin Blade 4K', 'server_icon' => '⚔️', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4'],
                ],
            ],
            [
                'tmdb_id'       => 1429,
                'title'         => 'Attack on Titan',
                'type'          => 'tv',
                'genre_ids'     => [16, 10759, 10765],
                'poster_path'   => '/hTP1DtLGFamjfu8WqjnuQdP1n4i.jpg',
                'backdrop_path' => '/m03jul0ygolv9KjNpPgKMb19wsi.jpg',
                'overview'      => 'Several hundred years ago, humans were nearly exterminated by Titans. Titans are typically several stories tall, seem to have no intelligence, devour human beings and, worst of all, seem to do it for the pleasure rather than as a food source.',
                'language'      => 'Japanese',
                'rating'        => 8.7,
                'year'          => '2013',
                'runtime'       => '24 min',
                'is_active'     => true,
                'is_midnight'   => false,
                'midnight_section_id' => null,
                'streams'       => [
                    ['server_name' => 'Scout Regiment 4K', 'server_icon' => '🛡️', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4'],
                ],
            ],
            [
                'tmdb_id'       => 95479,
                'title'         => 'Jujutsu Kaisen',
                'type'          => 'tv',
                'genre_ids'     => [16, 10759, 10765],
                'poster_path'   => '/fHpKW597KMfMhhL58AkyOQvjC4c.jpg',
                'backdrop_path' => '/hVMo7sDk1zU6iVvS8E3Z71q32yF.jpg',
                'overview'      => 'Yuji Itadori is a boy with tremendous physical strength, though he lives a completely ordinary high school life. One day, to save a classmate who has been attacked by curses, he eats the finger of Ryomen Sukuna, taking the curse into his own soul.',
                'language'      => 'Japanese',
                'rating'        => 8.6,
                'year'          => '2020',
                'runtime'       => '24 min',
                'is_active'     => true,
                'is_midnight'   => false,
                'midnight_section_id' => null,
                'streams'       => [
                    ['server_name' => 'Domain Expansion 4K', 'server_icon' => '👁️', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'],
                ],
            ],
            [
                'tmdb_id'       => 13916,
                'title'         => 'Death Note',
                'type'          => 'tv',
                'genre_ids'     => [16, 9648, 10765],
                'poster_path'   => '/iigTJJskR1PcjjA1T0W0ev4qBsv.jpg',
                'backdrop_path' => '/3sU288pQ6N9U1fC1a4u80D4WvW3.jpg',
                'overview'      => 'Light Yagami is an ace student with great prospects—and he’s bored out of his mind. But all that changes when he finds the Death Note, a notebook dropped by a rogue Shinigami death god.',
                'language'      => 'Japanese',
                'rating'        => 8.6,
                'year'          => '2006',
                'runtime'       => '23 min',
                'is_active'     => true,
                'is_midnight'   => false,
                'midnight_section_id' => null,
                'streams'       => [
                    ['server_name' => 'Kira 1080p Master', 'server_icon' => '📓', 'stream_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/WeAreGoingOnBullrun.mp4'],
                ],
            ],
        ];

        foreach ($items as $item) {
            $streams = $item['streams'] ?? [];
            unset($item['streams']);

            $existing = CustomMovie::where('title', $item['title'])
                ->where('is_midnight', false)
                ->first();

            if ($existing) {
                $existing->update($item);
                $movie = $existing;
            } else {
                $movie = CustomMovie::create($item);
            }

            // Sync streams
            if (!empty($streams) && $movie->streams()->count() === 0) {
                foreach ($streams as $idx => $streamData) {
                    CustomMovieStream::create([
                        'custom_movie_id' => $movie->id,
                        'server_name'     => $streamData['server_name'],
                        'server_icon'     => $streamData['server_icon'] ?? '▶',
                        'stream_url'      => $streamData['stream_url'],
                        'season_number'   => null,
                        'episode_number'  => null,
                        'sort_order'      => $idx + 1,
                    ]);
                }
            }
        }

        // Flush all feed caches so the new custom content is instantly live
        HomeFeedController::clearHomeFeedCaches();
    }
}
