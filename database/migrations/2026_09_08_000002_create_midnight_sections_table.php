<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('midnight_sections', function (Blueprint $table) {
            $table->id();
            $table->string('emoji')->default('🍸');
            $table->string('title');
            $table->string('tagline')->nullable();
            $table->string('endpoint')->default('discover/movie');
            $table->json('params')->nullable();
            $table->string('media_type')->default('movie');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('midnight_sections');
    }
};
