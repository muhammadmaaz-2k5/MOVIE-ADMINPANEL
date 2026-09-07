<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEXES FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Optimize custom_movies
        Schema::table('custom_movies', function (Blueprint $table) {
            if (!$this->hasIndex('custom_movies', 'uq_custom_movies_tmdb_type')) {
                $table->unique(['tmdb_id', 'type'], 'uq_custom_movies_tmdb_type');
            }
            if (!$this->hasIndex('custom_movies', 'idx_custom_movies_active_type')) {
                $table->index(['is_active', 'type'], 'idx_custom_movies_active_type');
            }
            if (!$this->hasIndex('custom_movies', 'idx_custom_movies_active_created')) {
                $table->index(['is_active', 'created_at'], 'idx_custom_movies_active_created');
            }
        });

        // 2. Optimize custom_movie_streams
        Schema::table('custom_movie_streams', function (Blueprint $table) {
            if (!$this->hasIndex('custom_movie_streams', 'idx_stream_movie_episode_sort')) {
                $table->index(
                    ['custom_movie_id', 'season_number', 'episode_number', 'sort_order'],
                    'idx_stream_movie_episode_sort'
                );
            }
        });

        // 3. Optimize download_links
        Schema::table('download_links', function (Blueprint $table) {
            if (!$this->hasIndex('download_links', 'idx_download_content_episode')) {
                $table->index(
                    ['content_type', 'content_id', 'season_number', 'episode_number'],
                    'idx_download_content_episode'
                );
            }
            if (!$this->hasIndex('download_links', 'idx_download_active_sort')) {
                $table->index(['is_active', 'sort_order'], 'idx_download_active_sort');
            }
        });

        // 4. Optimize home_sections
        Schema::table('home_sections', function (Blueprint $table) {
            if (!$this->hasIndex('home_sections', 'idx_home_sections_active_sort')) {
                $table->index(['is_active', 'sort_order'], 'idx_home_sections_active_sort');
            }
        });

        // 5. Expand & Optimize articles
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'title')) {
                $table->string('title')->after('id');
                $table->string('slug')->unique()->after('title');
                $table->longText('content')->nullable()->after('slug');
                $table->string('image_url')->nullable()->after('content');
                $table->boolean('is_published')->default(true)->after('image_url');
            }
            if (!$this->hasIndex('articles', 'idx_articles_published_created')) {
                $table->index(['is_published', 'created_at'], 'idx_articles_published_created');
            }
        });

        // 6. Upgrade scheduled_notifications from template store to true scheduler
        Schema::table('scheduled_notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('scheduled_notifications', 'scheduled_at')) {
                $table->timestamp('scheduled_at')->nullable()->after('episode_number');
                $table->string('status', 20)->default('pending')->after('scheduled_at');
                $table->timestamp('sent_at')->nullable()->after('status');
                $table->timestamp('failed_at')->nullable()->after('sent_at');
                $table->text('failure_reason')->nullable()->after('failed_at');
            }
            if (!$this->hasIndex('scheduled_notifications', 'idx_notification_status_schedule')) {
                $table->index(['status', 'scheduled_at'], 'idx_notification_status_schedule');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheduled_notifications', function (Blueprint $table) {
            if ($this->hasIndex('scheduled_notifications', 'idx_notification_status_schedule')) {
                $table->dropIndex('idx_notification_status_schedule');
            }
            if (Schema::hasColumn('scheduled_notifications', 'scheduled_at')) {
                $table->dropColumn(['scheduled_at', 'status', 'sent_at', 'failed_at', 'failure_reason']);
            }
        });

        Schema::table('articles', function (Blueprint $table) {
            if ($this->hasIndex('articles', 'idx_articles_published_created')) {
                $table->dropIndex('idx_articles_published_created');
            }
            if (Schema::hasColumn('articles', 'is_published')) {
                $table->dropColumn(['title', 'slug', 'content', 'image_url', 'is_published']);
            }
        });

        Schema::table('home_sections', function (Blueprint $table) {
            if ($this->hasIndex('home_sections', 'idx_home_sections_active_sort')) {
                $table->dropIndex('idx_home_sections_active_sort');
            }
        });

        Schema::table('download_links', function (Blueprint $table) {
            if ($this->hasIndex('download_links', 'idx_download_content_episode')) {
                $table->dropIndex('idx_download_content_episode');
            }
            if ($this->hasIndex('download_links', 'idx_download_active_sort')) {
                $table->dropIndex('idx_download_active_sort');
            }
        });

        Schema::table('custom_movie_streams', function (Blueprint $table) {
            if ($this->hasIndex('custom_movie_streams', 'idx_stream_movie_episode_sort')) {
                $table->dropIndex('idx_stream_movie_episode_sort');
            }
        });

        Schema::table('custom_movies', function (Blueprint $table) {
            if ($this->hasIndex('custom_movies', 'uq_custom_movies_tmdb_type')) {
                $table->dropUnique('uq_custom_movies_tmdb_type');
            }
            if ($this->hasIndex('custom_movies', 'idx_custom_movies_active_type')) {
                $table->dropIndex('idx_custom_movies_active_type');
            }
            if ($this->hasIndex('custom_movies', 'idx_custom_movies_active_created')) {
                $table->dropIndex('idx_custom_movies_active_created');
            }
        });
    }
};
