<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('button_ads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('button_text');
            $table->string('button_link');
            $table->string('button_color')->default('#6C5CE7');
            $table->string('button_icon')->nullable();
            $table->string('target_screen')->default('home');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('button_ads');
    }
};