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
        Schema::table('shop_banners', function (Blueprint $table) {
            // Change image column to path and make nullable for migration
            $table->renameColumn('image', 'path');
            
            // Add S3 URL columns
            $table->string('url')->nullable()->after('path');
            $table->string('thumbnail_path')->nullable()->after('url');
            $table->string('thumbnail_url')->nullable()->after('thumbnail_path');
            
            // Add is_primary flag
            $table->boolean('is_primary')->default(false)->after('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_banners', function (Blueprint $table) {
            $table->renameColumn('path', 'image');
            $table->dropColumn(['url', 'thumbnail_path', 'thumbnail_url', 'is_primary']);
        });
    }
};
