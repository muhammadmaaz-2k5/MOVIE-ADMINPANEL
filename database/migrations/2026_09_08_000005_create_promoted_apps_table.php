<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promoted_apps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('category')->default('Entertainment');
            $table->string('package_name')->nullable();
            $table->string('play_store_url')->nullable();
            $table->string('icon_url')->nullable();
            $table->string('banner_url')->nullable();
            $table->decimal('rating', 3, 1)->default(4.8);
            $table->string('downloads')->default('100K+');
            $table->string('badge')->nullable(); // e.g. "FEATURED", "HOT", "NEW"
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promoted_apps');
    }
};
