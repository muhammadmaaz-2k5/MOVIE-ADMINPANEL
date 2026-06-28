<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_movie_streams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_movie_id')->constrained('custom_movies')->onDelete('cascade');
            $table->string('server_name');
            $table->string('server_icon')->default('🔗');
            $table->text('stream_url');
            $table->integer('season_number')->nullable();
            $table->integer('episode_number')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_movie_streams');
    }
};
