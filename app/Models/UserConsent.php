<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserConsent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'consent_type',
        'is_granted',
        'granted_at',
        'revoked_at',
    ];

    protected $casts = [
        'is_granted' => 'boolean',
        'granted_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    // Consent types
    const TYPE_EMAIL_MARKETING = 'email_marketing';
    const TYPE_SMS_MARKETING = 'sms_marketing';

    /**
     * Valid consent types
     */
    public static function getValidTypes(): array
    {
        return [
            self::TYPE_EMAIL_MARKETING,
            self::TYPE_SMS_MARKETING,
        ];
    }

    /**
     * Relationship: consent belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: get only granted consents
     */
    public function scopeGranted($query)
    {
        return $query->where('is_granted', true);
    }

    /**
     * Scope: get consents by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('consent_type', $type);
    }
}
