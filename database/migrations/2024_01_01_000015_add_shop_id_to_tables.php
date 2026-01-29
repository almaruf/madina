<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add shop_id to users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('shop_id')->default(1)->after('id')->constrained()->onDelete('cascade');
        });

        // Add shop_id to products
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('shop_id')->default(1)->after('id')->constrained()->onDelete('cascade');
        });

        // Add shop_id to categories
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('shop_id')->default(1)->after('id')->constrained()->onDelete('cascade');
        });

        // Add shop_id to orders
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shop_id')->default(1)->after('id')->constrained()->onDelete('cascade');
        });

        // Add shop_id to addresses
        Schema::table('addresses', function (Blueprint $table) {
            $table->foreignId('shop_id')->default(1)->after('id')->constrained()->onDelete('cascade');
        });

        // Add shop_id to delivery_slots
        Schema::table('delivery_slots', function (Blueprint $table) {
            $table->foreignId('shop_id')->default(1)->after('id')->constrained()->onDelete('cascade');
        });

        // Add shop_id to otps
        Schema::table('otps', function (Blueprint $table) {
            $table->foreignId('shop_id')->default(1)->after('id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['shop_id']);
            $table->dropColumn('shop_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['shop_id']);
            $table->dropColumn('shop_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['shop_id']);
            $table->dropColumn('shop_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['shop_id']);
            $table->dropColumn('shop_id');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['shop_id']);
            $table->dropColumn('shop_id');
        });

        Schema::table('delivery_slots', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['shop_id']);
            $table->dropColumn('shop_id');
        });

        Schema::table('otps', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['shop_id']);
            $table->dropColumn('shop_id');
        });
    }
};
