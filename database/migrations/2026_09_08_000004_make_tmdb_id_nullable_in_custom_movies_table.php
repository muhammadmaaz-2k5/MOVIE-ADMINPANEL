<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_movies', function (Blueprint $table) {
            $table->unsignedBigInteger('tmdb_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('custom_movies', function (Blueprint $table) {
            $table->unsignedBigInteger('tmdb_id')->nullable(false)->change();
        });
    }
};
