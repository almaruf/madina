<?php

namespace App\Services;

use App\Models\Otp;
use Twilio\Rest\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OtpService
{
    protected $twilio;

    public function __construct()
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        
        if ($sid && $token) {
            $this->twilio = new Client($sid, $token);
        }
    }

    public function sendOtp(string $phone): Otp
    {
        // Generate 6-digit OTP
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Get current shop info
        $shopId = ShopContext::getShopId();
        $shopName = $shopId ? \App\Models\Shop::find($shopId)?->name : app(ShopConfigService::class)->name();

        // Create OTP record
        $otp = Otp::create([
            'phone' => $phone,
            'otp' => $otpCode,
            'shop_id' => $shopId,
            'expires_at' => Carbon::now()->addMinutes(config('services.otp.expiry_minutes')),
            'attempts' => 0,
            'verified' => false,
        ]);

        // Send SMS via Twilio
        try {
            if ($this->twilio) {
                $this->twilio->messages->create(
                    $phone,
                    [
                        'from' => config('services.twilio.from'),
                        'body' => "Your {$shopName} verification code is: {$otpCode}. Valid for " . config('services.otp.expiry_minutes') . " minutes."
                    ]
                );
            } else {
                // Log OTP for development
                Log::info("OTP for {$phone}: {$otpCode}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send OTP: " . $e->getMessage());
            // In production, you might want to throw an exception here
        }

        return $otp;
    }

    public function verifyOtp(string $phone, string $otpCode): bool
    {
        $otp = Otp::where('phone', $phone)
            ->where('verified', false)
            ->latest()
            ->first();

        if (!$otp || !$otp->isValid()) {
            return false;
        }

        // Increment attempts
        $otp->increment('attempts');

        // Verify OTP
        if ($otp->otp === $otpCode) {
            $otp->update(['verified' => true]);
            return true;
        }

        return false;
    }
}
