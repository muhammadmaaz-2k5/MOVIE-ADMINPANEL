<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('download_links', function (Blueprint $table) {
            $table->integer('season_number')->nullable()->after('content_poster');
            $table->integer('episode_number')->nullable()->after('season_number');
        });
    }

    public function down(): void
    {
        Schema::table('download_links', function (Blueprint $table) {
            $table->dropColumn(['season_number', 'episode_number']);
        });
    }
};
