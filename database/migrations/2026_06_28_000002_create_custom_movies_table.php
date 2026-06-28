<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_movies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tmdb_id')->index();
            $table->string('title');
            $table->string('type')->default('movie'); // 'movie' or 'tv'
            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();
            $table->text('overview')->nullable();
            $table->string('language')->default('English');
            $table->double('rating', 3, 1)->default(0.0);
            $table->string('year')->nullable();
            $table->string('runtime')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_movies');
    }
};
