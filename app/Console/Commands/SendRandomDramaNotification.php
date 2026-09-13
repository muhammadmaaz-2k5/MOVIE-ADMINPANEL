<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScheduledNotification;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SendRandomDramaNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-random-drama-notification
                            {--type= : Content type filter (movie, tv)}
                            {--id= : Send a specific scheduled notification by ID}
                            {--title= : Custom notification title to send immediately}
                            {--body= : Custom notification body to send immediately}
                            {--image= : Custom image URL or path}
                            {--screen=home : Target app screen (home, details, player)}
                            {--drama_slug= : Drama or series slug identifier}
                            {--episode= : Episode number}
                            {--tmdb_id= : Associated TMDB ID for deep-linking}
                            {--dry-run : Simulate and display payload without contacting Firebase}
                            {--force : Force send dynamic fallback if database has no templates}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a push notification from templates, custom inputs, or dynamic trending content to all app users via FCM';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🚀 Starting FCM Push Notification Dispatcher...");

        $isDryRun = $this->option('dry-run');
        $customTitle = $this->option('title');
        $customBody = $this->option('body');
        $templateId = $this->option('id');
        $filterType = $this->option('type');

        $notificationData = null;
        $dbRecord = null;

        // 1. Direct custom CLI input
        if ($customTitle && $customBody) {
            $this->comment("📝 Using custom ad-hoc notification parameters.");
            $customImage = $this->option('image') ?: 'https://image.tmdb.org/t/p/w780/c6BPbkO5Npt1OdwttAxCFo06wtH.jpg';
            $notificationData = [
                'title'          => $customTitle,
                'body'           => $customBody,
                'image_path'     => $customImage,
                'screen'         => $this->option('screen') ?: 'home',
                'drama_slug'     => $this->option('drama_slug') ?: '',
                'episode_number' => $this->option('episode') ?: '',
                'type'           => $filterType ?: 'movie',
                'tmdb_id'        => $this->option('tmdb_id') ?: '',
            ];
        }

        // 2. Specific Template ID
        elseif ($templateId) {
            $this->comment("🔍 Looking up scheduled notification template ID: {$templateId}");
            try {
                $dbRecord = ScheduledNotification::find($templateId);
                if (!$dbRecord) {
                    $this->error("❌ Template with ID {$templateId} not found in database.");
                    return 1;
                }
                $notificationData = $this->formatModelData($dbRecord);
            } catch (\Exception $e) {
                $this->error("❌ Database query error: " . $e->getMessage());
                return 1;
            }
        }

        // 3. Random template from database (prioritizing unsent or least-recently sent)
        else {
            try {
                $query = ScheduledNotification::query();

                if ($filterType) {
                    $query->where('type', $filterType);
                }

                // Filter out disabled templates
                $query->where(function ($q) {
                    $q->whereNull('status')
                      ->orWhere('status', '!=', 'disabled');
                });

                // Prioritize templates never sent first, then least-recently sent
                $dbRecord = (clone $query)->whereNull('sent_at')->inRandomOrder()->first();
                if (!$dbRecord) {
                    $dbRecord = $query->orderBy('sent_at', 'asc')->first();
                }

                if ($dbRecord) {
                    $this->comment("🎲 Selected database template ID #{$dbRecord->id} ('{$dbRecord->title}')");
                    $notificationData = $this->formatModelData($dbRecord);
                }
            } catch (\Exception $e) {
                $this->warn("⚠️ Could not query database for templates: " . $e->getMessage());
            }

            // 4. Dynamic fallback if no templates exist in database
            if (!$notificationData) {
                $this->comment("🌐 Generating dynamic trending notification from TMDB...");
                $notificationData = $this->getDynamicFallback($filterType);
            }
        }

        // Display summary
        $this->table(
            ['Field', 'Value'],
            [
                ['Target Topic', 'all'],
                ['Title', $notificationData['title']],
                ['Body', $notificationData['body']],
                ['Screen', $notificationData['screen'] ?? 'home'],
                ['Item Type', $notificationData['type'] ?? 'movie'],
                ['TMDB ID', $notificationData['tmdb_id'] ?? 'N/A'],
                ['Image', $notificationData['image_path'] ?? 'None'],
                ['Dry Run', $isDryRun ? 'YES (Simulated)' : 'NO (Live FCM)'],
            ]
        );

        if ($isDryRun) {
            $this->info("✅ Dry-run completed. Notification payload verified successfully.");
            return 0;
        }

        // Dispatch via FCM
        try {
            $controller = new NotificationController();
            $result = $controller->sendFCMNotification(
                $notificationData['title'],
                $notificationData['body'],
                $notificationData['image_path'],
                $notificationData['screen'],
                $notificationData['drama_slug'],
                $notificationData['episode_number'],
                $notificationData['type'],
                $notificationData['tmdb_id']
            );

            $messageId = $result['name'] ?? 'Sent';
            $this->info("🎉 Notification successfully broadcasted to topic 'all'!");
            $this->line("<fg=cyan>FCM Message ID: {$messageId}</>");

            // Update database record if applicable
            if ($dbRecord) {
                try {
                    $dbRecord->update([
                        'sent_at'        => now(),
                        'status'         => 'sent',
                        'failed_at'      => null,
                        'failure_reason' => null,
                    ]);
                } catch (\Exception $e) {
                    Log::warning("Could not update notification record sent_at: " . $e->getMessage());
                }
            }

            Log::info("SendRandomDramaNotification: Broadcasted '{$notificationData['title']}' via FCM. ID: {$messageId}");
            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Failed to send FCM notification: " . $e->getMessage());
            Log::error("SendRandomDramaNotification error: " . $e->getMessage());

            if ($dbRecord) {
                try {
                    $dbRecord->update([
                        'failed_at'      => now(),
                        'status'         => 'failed',
                        'failure_reason' => substr($e->getMessage(), 0, 500),
                    ]);
                } catch (\Exception $ex) {
                    // Ignore db update failure
                }
            }

            return 1;
        }
    }

    /**
     * Format a ScheduledNotification model into an array
     */
    private function formatModelData(ScheduledNotification $model): array
    {
        return [
            'title'          => (string) $model->title,
            'body'           => (string) $model->body,
            'image_path'     => $model->image_path,
            'screen'         => $model->screen ?: 'home',
            'drama_slug'     => $model->drama_slug ?: '',
            'episode_number' => $model->episode_number ?: '',
            'type'           => $model->type ?: 'movie',
            'tmdb_id'        => $model->tmdb_id ?: '',
        ];
    }

    /**
     * Generate engaging dynamic content from TMDB trending or curated fallbacks
     */
    private function getDynamicFallback(?string $type): array
    {
        $mediaType = $type ?: 'all';
        $token = env('TMDB_BEARER_TOKEN');
        $apiKey = env('TMDB_API_KEY');

        // Attempt to fetch fresh daily trending movies/shows from TMDB
        try {
            $req = Http::withoutVerifying()->timeout(10);
            if ($token) {
                $req->withToken($token);
            }
            $url = "https://api.themoviedb.org/3/trending/{$mediaType}/day";
            if (!$token && $apiKey) {
                $url .= "?api_key={$apiKey}";
            }

            $response = $req->get($url);
            if ($response->successful()) {
                $results = $response->json()['results'] ?? [];
                // Filter items that have title and an image
                $validItems = array_values(array_filter($results, function ($item) {
                    return (!empty($item['title']) || !empty($item['name'])) &&
                           (!empty($item['backdrop_path']) || !empty($item['poster_path']));
                }));

                if (!empty($validItems)) {
                    $slice = array_slice($validItems, 0, 15);
                    $selected = $slice[array_rand($slice)];

                    $title = $selected['title'] ?? $selected['name'] ?? 'Trending Pick';
                    $itemType = $selected['media_type'] ?? ($type ?: 'movie');
                    $tmdbId = (string)($selected['id'] ?? '');
                    $imagePath = !empty($selected['backdrop_path'])
                        ? 'https://image.tmdb.org/t/p/w780' . $selected['backdrop_path']
                        : 'https://image.tmdb.org/t/p/w780' . $selected['poster_path'];

                    $overview = trim($selected['overview'] ?? '');
                    if (strlen($overview) > 130) {
                        $overview = substr($overview, 0, 127) . '...';
                    }

                    $emojis = ['🔥 Trending Now: ', '🍿 Must Watch: ', '✨ Recommended: ', '⚡ Popular on ENGORA: ', '🎬 Top Pick: '];
                    $prefix = $emojis[array_rand($emojis)];

                    return [
                        'title'          => $prefix . $title,
                        'body'           => $overview ?: "Watch {$title} now streaming on ENGORA with HD quality and fast streaming!",
                        'image_path'     => $imagePath,
                        'screen'         => 'home',
                        'drama_slug'     => '',
                        'episode_number' => '',
                        'type'           => $itemType,
                        'tmdb_id'        => $tmdbId,
                    ];
                }
            }
        } catch (\Exception $e) {
            $this->warn("⚠️ TMDB dynamic fetch notice: " . $e->getMessage());
        }

        // Curated static fallback pool if TMDB API is offline or unreachable
        $fallbacks = [
            [
                'title'          => '🔥 Trending Today on ENGORA',
                'body'           => 'Discover the most-watched blockbusters, popular series, and top-rated entertainment right now!',
                'image_path'     => 'https://image.tmdb.org/t/p/w780/c6BPbkO5Npt1OdwttAxCFo06wtH.jpg',
                'screen'         => 'home',
                'drama_slug'     => '',
                'episode_number' => '',
                'type'           => $type ?: 'movie',
                'tmdb_id'        => '912649',
            ],
            [
                'title'          => '🍿 Movie Night Pick',
                'body'           => 'Unwind with today\'s handpicked cinematic recommendation with verified ratings & official trailers!',
                'image_path'     => 'https://image.tmdb.org/t/p/w780/RMXG8myu1aGlNUsRjtxzmpdMK0.jpg',
                'screen'         => 'home',
                'drama_slug'     => '',
                'episode_number' => '',
                'type'           => 'movie',
                'tmdb_id'        => '693134',
            ],
            [
                'title'          => '✨ Top Web Series Waiting For You',
                'body'           => 'Binge the most talked-about drama series and trending episodes on ENGORA today.',
                'image_path'     => 'https://image.tmdb.org/t/p/w780/rZfmzpixLKLR3Hg2u0WgC7XLFl8.jpg',
                'screen'         => 'home',
                'drama_slug'     => '',
                'episode_number' => '',
                'type'           => 'tv',
                'tmdb_id'        => '94997',
            ],
            [
                'title'          => '⚡ Action & Thrillers Packed For You',
                'body'           => 'Get your adrenaline pumping with our hand-picked action movies and gripping suspense thrillers.',
                'image_path'     => 'https://image.tmdb.org/t/p/w780/xOmOoDJ5q9vFv61fSzaBrQgvm49.jpg',
                'screen'         => 'home',
                'drama_slug'     => '',
                'episode_number' => '',
                'type'           => 'movie',
                'tmdb_id'        => '823464',
            ],
            [
                'title'          => '🌟 Weekend Binge Picks',
                'body'           => 'Looking for something great to watch? Check out top-rated movies and popular series trending now.',
                'image_path'     => 'https://image.tmdb.org/t/p/w780/yDHYTfA3R0jFYba16jBB1ef8oIt.jpg',
                'screen'         => 'home',
                'drama_slug'     => '',
                'episode_number' => '',
                'type'           => 'movie',
                'tmdb_id'        => '533535',
            ],
        ];

        return $fallbacks[array_rand($fallbacks)];
    }

}
