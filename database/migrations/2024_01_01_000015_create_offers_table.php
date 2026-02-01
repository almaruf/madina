<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            
            // Offer details
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            // Offer type
            $table->enum('type', [
                'percentage_discount',
                'fixed_discount',
                'bxgy_free',
                'multibuy',
                'bxgy_discount',
                'flash_sale',
                'bundle'
            ]);
            
            // Discount values
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->integer('buy_quantity')->nullable();
            $table->integer('get_quantity')->nullable();
            $table->decimal('get_discount_percentage', 5, 2)->nullable();
            $table->decimal('bundle_price', 10, 2)->nullable();
            
            // Conditions
            $table->decimal('min_purchase_amount', 10, 2)->nullable();
            $table->integer('max_uses_per_customer')->nullable();
            $table->integer('total_usage_limit')->nullable();
            $table->integer('current_usage_count')->default(0);
            
            // Validity
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            
            // Display
            $table->string('badge_text')->nullable();
            $table->string('badge_color')->default('#DC2626');
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['shop_id', 'is_active', 'starts_at', 'ends_at']);
        });
        
        Schema::create('offer_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['offer_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_product');
        Schema::dropIfExists('offers');
    }
};
