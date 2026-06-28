<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_movies', function (Blueprint $table) {
            $table->text('genre_ids')->nullable()->after('type'); // Stores json array of genre IDs, e.g. [28, 12]
        });
    }

    public function down(): void
    {
        Schema::table('custom_movies', function (Blueprint $table) {
            $table->dropColumn('genre_ids');
        });
    }
};
