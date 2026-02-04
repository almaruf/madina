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
        Schema::table('categories', function (Blueprint $table) {
            // Rename image column to path
            $table->renameColumn('image', 'path');
            
            // Add S3 URL columns
            $table->string('url')->nullable()->after('path');
            $table->string('thumbnail_path')->nullable()->after('url');
            $table->string('thumbnail_url')->nullable()->after('thumbnail_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('path', 'image');
            $table->dropColumn(['url', 'thumbnail_path', 'thumbnail_url']);
        });
    }
};
