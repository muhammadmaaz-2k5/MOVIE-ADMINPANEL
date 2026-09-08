<?php

namespace Database\Seeders;

use App\Models\MidnightSection;
use Illuminate\Database\Seeder;

class MidnightSectionSeeder extends Seeder
{
    public function run(): void
    {
        try {
            MidnightSection::truncate();
        } catch (\Exception $e) {
            MidnightSection::query()->delete();
        }

        $sections = [
            [
                'emoji' => '🍾',
                'title' => 'VIP Nightclub Exclusives',
                'tagline' => 'Hand-curated adult late-night streams',
                'endpoint' => 'custom',
                'params' => null,
                'media_type' => 'movie',
                'sort_order' => 0,
                'is_active' => true,
            ],
            [
                'emoji' => '🌙',
                'title' => 'Neon Noir & Nightlife Crime',
                'tagline' => 'Dark alleys, gritty undergrounds, and midnight heists',
                'endpoint' => 'discover/movie',
                'params' => ['with_genres' => '80,53', 'sort_by' => 'popularity.desc', 'include_adult' => 'true'],
                'media_type' => 'movie',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'emoji' => '💋',
                'title' => 'After Hours & Passion',
                'tagline' => 'Intense, sensual, and mature late-night romance',
                'endpoint' => 'discover/movie',
                'params' => ['with_genres' => '10749,18', 'sort_by' => 'popularity.desc', 'include_adult' => 'true'],
                'media_type' => 'movie',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'emoji' => '💀',
                'title' => 'Midnight Madness & Horror',
                'tagline' => 'Sinister chills and screams for the dead of night',
                'endpoint' => 'discover/movie',
                'params' => ['with_genres' => '27,53', 'sort_by' => 'popularity.desc', 'include_adult' => 'true'],
                'media_type' => 'movie',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'emoji' => '🔮',
                'title' => 'Late Night Mindbenders',
                'tagline' => 'Twisted psychological thrillers that keep you awake',
                'endpoint' => 'discover/movie',
                'params' => ['with_genres' => '9648,53', 'sort_by' => 'vote_average.desc', 'vote_count.gte' => '100', 'include_adult' => 'true'],
                'media_type' => 'movie',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($sections as $section) {
            MidnightSection::create($section);
        }
    }
}
