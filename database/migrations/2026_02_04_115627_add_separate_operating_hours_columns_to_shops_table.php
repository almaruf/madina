<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            // Add separate columns for each day's operating hours
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            
            foreach ($days as $day) {
                $table->time("{$day}_open")->nullable()->after("{$day}_hours");
                $table->time("{$day}_close")->nullable()->after("{$day}_open");
                $table->boolean("{$day}_closed")->default(false)->after("{$day}_close");
            }
        });

        // Migrate existing data from string format to separate columns
        $this->migrateExistingData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            
            foreach ($days as $day) {
                $table->dropColumn(["{$day}_open", "{$day}_close", "{$day}_closed"]);
            }
        });
    }

    /**
     * Migrate existing hours data to new format
     */
    private function migrateExistingData(): void
    {
        $shops = DB::table('shops')->get();
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($shops as $shop) {
            $updates = [];
            
            foreach ($days as $day) {
                $hoursField = "{$day}_hours";
                $hoursValue = $shop->$hoursField;
                
                if (empty($hoursValue) || strtolower($hoursValue) === 'closed') {
                    $updates["{$day}_closed"] = true;
                    $updates["{$day}_open"] = null;
                    $updates["{$day}_close"] = null;
                } else {
                    // Parse formats like "9:00 AM - 6:00 PM" or "09:00 - 18:00"
                    $pattern = '/(\d{1,2}):(\d{2})\s*(AM|PM)?\s*-\s*(\d{1,2}):(\d{2})\s*(AM|PM)?/i';
                    if (preg_match($pattern, $hoursValue, $matches)) {
                        list(, $openHour, $openMin, $openPeriod, $closeHour, $closeMin, $closePeriod) = $matches;
                        
                        // Convert to 24-hour format
                        if ($openPeriod) {
                            $openHour = (int)$openHour;
                            if (strtoupper($openPeriod) === 'PM' && $openHour !== 12) {
                                $openHour += 12;
                            }
                            if (strtoupper($openPeriod) === 'AM' && $openHour === 12) {
                                $openHour = 0;
                            }
                        }
                        
                        if ($closePeriod) {
                            $closeHour = (int)$closeHour;
                            if (strtoupper($closePeriod) === 'PM' && $closeHour !== 12) {
                                $closeHour += 12;
                            }
                            if (strtoupper($closePeriod) === 'AM' && $closeHour === 12) {
                                $closeHour = 0;
                            }
                        }
                        
                        $updates["{$day}_open"] = sprintf('%02d:%02d:00', $openHour, $openMin);
                        $updates["{$day}_close"] = sprintf('%02d:%02d:00', $closeHour, $closeMin);
                        $updates["{$day}_closed"] = false;
                    }
                }
            }
            
            if (!empty($updates)) {
                DB::table('shops')->where('id', $shop->id)->update($updates);
            }
        }
    }
};
