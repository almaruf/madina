<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->enum('type', ['standard', 'meat', 'frozen', 'fresh', 'perishable'])->default('standard');
            
            // Product attributes
            $table->string('brand')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('country_of_origin')->nullable();
            $table->text('ingredients')->nullable();
            $table->text('allergen_info')->nullable();
            $table->text('nutritional_info')->nullable();
            $table->text('storage_instructions')->nullable();
            
            // Meat specific fields
            $table->boolean('is_halal')->default(false);
            $table->string('cut_type')->nullable(); // e.g., whole, diced, minced, strips
            $table->enum('meat_type', ['chicken', 'beef', 'lamb', 'goat', 'fish', 'other'])->nullable();
            
            // Inventory and pricing
            $table->string('sku')->unique()->nullable();
            $table->string('barcode')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_on_sale')->default(false);
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_threshold')->default(10);
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'pre_order'])->default('in_stock');
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('sku');
            $table->index('type');
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('stock_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
