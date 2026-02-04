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
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn([
                'monday_hours',
                'tuesday_hours',
                'wednesday_hours',
                'thursday_hours',
                'friday_hours',
                'saturday_hours',
                'sunday_hours',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->string('monday_hours')->nullable();
            $table->string('tuesday_hours')->nullable();
            $table->string('wednesday_hours')->nullable();
            $table->string('thursday_hours')->nullable();
            $table->string('friday_hours')->nullable();
            $table->string('saturday_hours')->nullable();
            $table->string('sunday_hours')->nullable();
        });
    }
};
