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
        Schema::create('video_servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('label');
            $table->string('icon');
            $table->text('movie_url_template');
            $table->text('tv_url_template');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_servers');
    }
};
