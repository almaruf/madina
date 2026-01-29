<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('tagline')->nullable();

            // Location
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('postcode');
            $table->string('country')->default('United Kingdom');

            // Contact
            $table->string('phone');
            $table->string('email');
            $table->string('support_email')->nullable();

            // Business
            $table->string('business_type')->default('grocery');
            $table->string('specialization')->default('general'); // halal, organic, asian, african, caribbean, general
            $table->boolean('has_halal_products')->default(false);
            $table->boolean('has_organic_products')->default(false);
            $table->boolean('has_international_products')->default(false);

            // Features
            $table->boolean('delivery_enabled')->default(true);
            $table->boolean('collection_enabled')->default(true);
            $table->boolean('online_payment')->default(false);
            $table->boolean('loyalty_program')->default(false);
            $table->boolean('reviews_enabled')->default(false);

            // Delivery Settings
            $table->integer('delivery_radius_km')->default(10);
            $table->decimal('min_order_amount', 10, 2)->default(20.00);
            $table->decimal('delivery_fee', 10, 2)->default(3.99);
            $table->decimal('free_delivery_threshold', 10, 2)->default(50.00);

            // Currency
            $table->string('currency')->default('GBP');
            $table->string('currency_symbol')->default('£');

            // Branding
            $table->string('primary_color')->default('#10b981');
            $table->string('secondary_color')->default('#059669');
            $table->string('logo_url')->nullable();
            $table->string('favicon_url')->nullable();

            // Social Media
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('whatsapp_number')->nullable();

            // Operating Hours
            $table->string('monday_hours')->nullable();
            $table->string('tuesday_hours')->nullable();
            $table->string('wednesday_hours')->nullable();
            $table->string('thursday_hours')->nullable();
            $table->string('friday_hours')->nullable();
            $table->string('saturday_hours')->nullable();
            $table->string('sunday_hours')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('slug');
            $table->index('domain');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
