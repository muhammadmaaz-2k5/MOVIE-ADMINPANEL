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
            $notificationData = [
                'title'          => $customTitle,
                'body'           => $customBody,
                'image_path'     => $this->option('image'),
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

                // Prioritize templates never sent, or sent furthest in the past
                $dbRecord = $query->orderByRaw('sent_at IS NULL DESC, sent_at ASC')
                                  ->inRandomOrder()
                                  ->first();

                if ($dbRecord) {
                    $this->comment("🎲 Selected database template ID #{$dbRecord->id} ('{$dbRecord->title}')");
                    $notificationData = $this->formatModelData($dbRecord);
                }
            } catch (\Exception $e) {
                $this->warn("⚠️ Could not query database for templates: " . $e->getMessage());
            }

            // 4. Fallback if no templates exist in database
            if (!$notificationData) {
                $this->warn("⚠️ No active notification templates found in database. Using dynamic fallback template.");
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
     * Generate engaging fallback content so cron job never fails
     */
    private function getDynamicFallback(?string $type): array
    {
        $fallbacks = [
            [
                'title'          => '🔥 Trending Today on ENGORA',
                'body'           => 'Discover the most-watched blockbusters, popular series, and top-rated entertainment right now!',
                'image_path'     => 'https://image.tmdb.org/t/p/w780/oBIQ5iqRcuTcm8iq23bLI46H22s.jpg',
                'screen'         => 'home',
                'drama_slug'     => '',
                'episode_number' => '',
                'type'           => $type ?: 'movie',
                'tmdb_id'        => '912649',
            ],
            [
                'title'          => '🍿 Movie Night Pick',
                'body'           => 'Unwind with today\'s handpicked cinematic recommendation with verified ratings & official trailers!',
                'image_path'     => 'https://image.tmdb.org/t/p/w780/xOMo8BRK7PfcJv9JCnx7s520b4q.jpg',
                'screen'         => 'home',
                'drama_slug'     => '',
                'episode_number' => '',
                'type'           => 'movie',
                'tmdb_id'        => '693134',
            ],
            [
                'title'          => '✨ Top Web Series Waiting For You',
                'body'           => 'Binge the most talked-about drama series and trending episodes on ENGORA today.',
                'image_path'     => 'https://image.tmdb.org/t/p/w780/7bWxLi59NX5nnURurKV55oFM029.jpg',
                'screen'         => 'home',
                'drama_slug'     => '',
                'episode_number' => '',
                'type'           => 'tv',
                'tmdb_id'        => '94997',
            ],
        ];

        return $fallbacks[array_rand($fallbacks)];
    }
}
