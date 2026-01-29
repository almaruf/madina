<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_slots', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('type', ['delivery', 'collection'])->default('delivery');
            $table->integer('max_orders')->default(10);
            $table->integer('current_orders')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['date', 'type']);
            $table->index('is_active');
            $table->unique(['date', 'start_time', 'end_time', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_slots');
    }
};
