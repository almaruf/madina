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
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('offer_id')->nullable()->after('product_variation_id')->constrained()->onDelete('set null');
            $table->string('offer_type')->nullable()->after('offer_id');
            $table->string('offer_name')->nullable()->after('offer_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['offer_id']);
            $table->dropColumn(['offer_id', 'offer_type', 'offer_name']);
        });
    }
};
