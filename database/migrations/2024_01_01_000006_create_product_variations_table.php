<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            
            // Variation details
            $table->string('sku')->unique()->nullable();
            $table->string('name'); // e.g., "500g", "1kg", "Small", "Medium"
            
            // Size/Weight
            $table->decimal('size', 10, 2)->nullable();
            $table->enum('size_unit', ['g', 'kg', 'ml', 'l', 'oz', 'lb', 'piece', 'pack'])->nullable();
            
            // Pricing
            $table->decimal('price', 10, 2);
            $table->decimal('compare_at_price', 10, 2)->nullable(); // Original price for sales
            $table->decimal('cost_price', 10, 2)->nullable(); // For profit calculation
            
            // Stock
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_threshold')->default(10);
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'pre_order'])->default('in_stock');
            
            // Physical attributes
            $table->decimal('weight', 10, 2)->nullable(); // For shipping calculation
            $table->enum('weight_unit', ['g', 'kg', 'oz', 'lb'])->nullable();
            
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('product_id');
            $table->index('sku');
            $table->index('is_active');
            $table->index('stock_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};
