<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automatically broadcast a random trending drama or movie push notification to all users every 4 hours
Schedule::command('app:send-random-drama-notification')
    ->everyFourHours()
    ->withoutOverlapping()
    ->runInBackground();
