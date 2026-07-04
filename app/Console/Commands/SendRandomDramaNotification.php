<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\ScheduledNotification;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Log;

#[Signature('app:send-random-drama-notification')]
#[Description('Send a random push notification from templates library (drama or movie)')]
class SendRandomDramaNotification extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $notification = ScheduledNotification::inRandomOrder()->first();

        if (!$notification) {
            $this->error('No scheduled notification templates found.');
            return 1;
        }

        try {
            $controller = new NotificationController();
            $controller->sendFCMNotification(
                $notification->title,
                $notification->body,
                $notification->image_path,
                $notification->screen,
                $notification->drama_slug,
                $notification->episode_number,
                $notification->type,
                $notification->tmdb_id
            );
            $this->info("Successfully sent notification: '{$notification->title}'");
            Log::info("Command SendRandomDramaNotification: Sent template ID {$notification->id} successfully.");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error sending notification: " . $e->getMessage());
            Log::error("Command SendRandomDramaNotification error: " . $e->getMessage());
            return 1;
        }
    }
}
