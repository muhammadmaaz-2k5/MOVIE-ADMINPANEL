<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('download_links', function (Blueprint $table) {
            $table->id();
            $table->string('content_type');          // 'movie' or 'tv'
            $table->unsignedBigInteger('content_id'); // TMDB ID
            $table->string('content_title');          // cached title
            $table->string('content_poster')->nullable(); // poster path
            $table->string('server_name');            // e.g. "Google Drive", "Mega.nz"
            $table->string('server_icon')->default('🔗'); // emoji or icon tag
            $table->string('quality');                // '480p','720p','1080p','2160p','4K'
            $table->string('language')->default('English');
            $table->string('file_size')->nullable();  // e.g. "2.1 GB"
            $table->text('download_url');             // the actual download link
            $table->string('notes')->nullable();      // optional notes
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['content_type', 'content_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('download_links');
    }
};
