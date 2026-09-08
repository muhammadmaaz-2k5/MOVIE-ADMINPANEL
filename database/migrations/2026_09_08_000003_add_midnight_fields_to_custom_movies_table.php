<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_movies', function (Blueprint $table) {
            $table->boolean('is_midnight')->default(false)->after('is_active')->index();
            $table->foreignId('midnight_section_id')
                ->nullable()
                ->after('is_midnight')
                ->constrained('midnight_sections')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('custom_movies', function (Blueprint $table) {
            $table->dropForeign(['midnight_section_id']);
            $table->dropColumn(['midnight_section_id', 'is_midnight']);
        });
    }
};
