# User Consent Feature Documentation

## Overview
This document describes the user consent feature for marketing communications. The feature allows customers to manage their preferences for receiving promotional emails and SMS messages while ensuring GDPR compliance.

## Database Schema

### Table: `user_consents`

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `user_id` | bigint | Foreign key to users table (cascade on delete) |
| `consent_type` | string | Type of consent: `email_marketing` or `sms_marketing` |
| `is_granted` | boolean | Whether consent is granted (default: false) |
| `granted_at` | timestamp | When consent was granted |
| `revoked_at` | timestamp | When consent was revoked |
| `created_at` | timestamp | Record creation timestamp |
| `updated_at` | timestamp | Record update timestamp |

**Indexes:**
- Unique constraint on `(user_id, consent_type)` - ensures one consent record per type per user
- Index on `(user_id, consent_type, is_granted)` - optimizes consent queries

**Cascade Deletion:**
- When a user is deleted, all their consent records are automatically deleted via database cascade

## Models

### UserConsent Model (`app/Models/UserConsent.php`)

**Constants:**
- `TYPE_EMAIL_MARKETING` = 'email_marketing'
- `TYPE_SMS_MARKETING` = 'sms_marketing'

**Methods:**
- `getValidTypes()` - Returns array of valid consent types
- `scopeGranted($query)` - Query scope for granted consents only
- `scopeByType($query, $type)` - Query scope for specific consent type

**Relationships:**
- `user()` - belongsTo User

### User Model Updates (`app/Models/User.php`)

**New Methods:**
- `consents()` - hasMany relationship with UserConsent
- `hasConsent(string $type): bool` - Check if user has granted a specific consent
- `getConsent(string $type)` - Get user's consent record for a specific type

## API Endpoints

### Customer-Facing Endpoints (authenticated)

#### GET /api/consents
Get current user's consent preferences.

**Response:**
```json
{
  "consents": [
    {
      "type": "email_marketing",
      "is_granted": true,
      "granted_at": "2026-02-05T10:30:00.000000Z",
      "revoked_at": null,
      "updated_at": "2026-02-05T10:30:00.000000Z"
    },
    {
      "type": "sms_marketing",
      "is_granted": false,
      "granted_at": null,
      "revoked_at": "2026-02-05T11:00:00.000000Z",
      "updated_at": "2026-02-05T11:00:00.000000Z"
    }
  ]
}
```

#### POST /api/consents
Update multiple consent preferences at once.

**Request:**
```json
{
  "consents": [
    {"type": "email_marketing", "is_granted": true},
    {"type": "sms_marketing", "is_granted": false}
  ]
}
```

**Response:**
```json
{
  "message": "Consent preferences updated successfully",
  "consents": [...]
}
```

#### POST /api/consents/single
Update a single consent preference.

**Request:**
```json
{
  "type": "email_marketing",
  "is_granted": true
}
```

**Response:**
```json
{
  "message": "Consent updated successfully",
  "consent": {
    "type": "email_marketing",
    "is_granted": true,
    "granted_at": "2026-02-05T10:30:00.000000Z",
    "revoked_at": null,
    "updated_at": "2026-02-05T10:30:00.000000Z"
  }
}
```

### Admin Endpoints

#### GET /api/admin/customers
**Enhanced:** Now includes consent information for each customer.

**Response fields added:**
- `consent_email` (boolean) - Whether customer has granted email marketing consent
- `consent_sms` (boolean) - Whether customer has granted SMS marketing consent

#### GET /api/admin/customers/removal-requests
**Enhanced:** Includes consent information for customers requesting deletion.

## User Interface

### Customer Account Page (`/account`)

**New Tab:** "Privacy & Consents"

Features:
- Toggle switches for email and SMS marketing consent
- Clear descriptions of what each consent means
- Important notice about transactional messages
- Save button to update preferences
- Success/error feedback messages

**UI Components:**
- Email Marketing Consent checkbox with description
- SMS Marketing Consent checkbox with description
- Information banner explaining transactional messages are unaffected
- Save Preferences button

### Admin Customer Listing (`/admin/customers`)

**Enhanced Table Columns:**
- Email Consent column with Yes/No badges
- SMS Consent column with Yes/No badges
- Visual indicators:
  - Green badge with checkmark for granted consent
  - Gray badge with X for not granted

## Implementation Details

### Consent Timestamps

- `granted_at` is set when consent is granted
- `revoked_at` is set when consent is revoked
- Both are nullable and mutually exclusive based on `is_granted` status

### Default Behavior

- By default, all consents are **not granted** (`is_granted = false`)
- Users must explicitly opt-in to marketing communications
- Consent records are created on first interaction

### Data Privacy & GDPR Compliance

1. **Explicit Consent:** Users must actively opt-in
2. **Easy Management:** Users can change preferences anytime via account page
3. **Cascade Deletion:** Consent records deleted when user is deleted
4. **Audit Trail:** Timestamps track when consent was granted/revoked
5. **Granular Control:** Separate consents for email and SMS

### User Deletion Flow

When a customer is permanently deleted via admin panel:
1. User consents are deleted (database cascade handles this automatically)
2. User addresses are force deleted
3. Order notes are anonymized
4. User record is hard deleted

### Transactional Messages

Important: Marketing consent **does not affect** transactional messages:
- Order confirmations
- Delivery notifications
- Account notifications
- Password resets
- Order status updates

These messages are always sent regardless of marketing consent preferences.

## Usage Examples

### Checking Consent in Code

```php
$user = auth()->user();

// Check if user has email marketing consent
if ($user->hasConsent(UserConsent::TYPE_EMAIL_MARKETING)) {
    // Send marketing email
}

// Get consent record
$emailConsent = $user->getConsent(UserConsent::TYPE_EMAIL_MARKETING);
if ($emailConsent && $emailConsent->is_granted) {
    // Send marketing email
}
```

### Updating Consent Programmatically

```php
use App\Models\UserConsent;

UserConsent::updateOrCreate(
    [
        'user_id' => $user->id,
        'consent_type' => UserConsent::TYPE_EMAIL_MARKETING,
    ],
    [
        'is_granted' => true,
        'granted_at' => now(),
        'revoked_at' => null,
    ]
);
```

### Querying Users with Consent

```php
// Get all users who granted email marketing consent
$users = User::whereHas('consents', function($query) {
    $query->where('consent_type', UserConsent::TYPE_EMAIL_MARKETING)
          ->where('is_granted', true);
})->get();
```

## Testing

### Manual Testing Steps

1. **Customer Account Page:**
   - Login as a customer
   - Navigate to Account → Privacy & Consents tab
   - Toggle email and SMS consent preferences
   - Click Save Preferences
   - Verify success message
   - Refresh page and verify preferences are saved

2. **Admin Customer Listing:**
   - Login as admin
   - Navigate to Customers page
   - Verify Email Consent and SMS Consent columns appear
   - Check that badges show correct consent status
   - Filter by different shops (if multi-tenant)

3. **API Testing:**
   - Use Postman/Insomnia to test API endpoints
   - Verify authentication requirements
   - Test consent updates
   - Verify cascade deletion

## Future Enhancements

Potential improvements to consider:

1. **Consent History:** Track all consent changes over time
2. **Additional Consent Types:** Phone calls, push notifications, etc.
3. **Consent Source Tracking:** Record where consent was obtained
4. **Bulk Consent Management:** Admin ability to view/export consent data
5. **Consent Expiry:** Optional expiration dates for consents
6. **Double Opt-In:** Confirmation emails for email marketing consent
7. **Unsubscribe Links:** One-click unsubscribe in marketing emails
8. **Consent Analytics:** Dashboard showing consent rates over time

## Compliance Notes

This implementation provides a foundation for GDPR compliance:

✅ Explicit opt-in required  
✅ Easy to withdraw consent  
✅ Granular control over different communication types  
✅ Audit trail with timestamps  
✅ Data deletion includes consent records  
✅ Clear information about data usage  

**Important:** Consult with legal counsel to ensure full compliance with applicable regulations in your jurisdiction (GDPR, CCPA, etc.).

## Files Modified/Created

### Created:
- `database/migrations/2026_02_05_000000_create_user_consents_table.php`
- `app/Models/UserConsent.php`
- `app/Http/Controllers/Api/ConsentController.php`
- `docs/USER_CONSENT_FEATURE.md` (this file)

### Modified:
- `app/Models/User.php` - Added consents relationship and helper methods
- `app/Http/Controllers/Admin/CustomerController.php` - Added consent data to customer listings
- `routes/api.php` - Added consent endpoints
- `resources/views/shop/account.blade.php` - Added Privacy & Consents tab
- `resources/views/admin/customers.blade.php` - Added consent columns
- `resources/js/admin/customers.js` - Enhanced rendering for consent badges

## Support

For questions or issues related to the consent feature, refer to:
- This documentation
- Model files for data structure
- Controller files for business logic
- View files for UI implementation
