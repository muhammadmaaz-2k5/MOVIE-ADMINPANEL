<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scheduled_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->string('type'); // 'movie' or 'tv'
            $table->string('image_type'); // 'manual' or 'tmdb'
            $table->string('image_path')->nullable(); // Local WebP path or TMDB path
            $table->string('tmdb_id')->nullable();
            $table->string('screen')->nullable();
            $table->string('drama_slug')->nullable();
            $table->string('episode_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_notifications');
    }
};
