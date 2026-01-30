# Order Confirmation Email Setup

## Overview
The system now automatically sends order confirmation emails when an admin confirms an order. Emails are queued using Laravel's queue system for better performance.

## Components Created

### 1. Mailable Class: `app/Mail/OrderConfirmed.php`
- Defines the email structure and content
- Includes order details, items, delivery information
- Uses shop branding (name, phone, email)

### 2. Job Class: `app/Jobs/SendOrderConfirmationEmail.php`
- Implements `ShouldQueue` for asynchronous processing
- Loads order relationships (user, shop, items, address, delivery slot)
- Sends email only if customer has an email address

### 3. Email Template: `resources/views/emails/orders/confirmed.blade.php`
- Professional markdown email template
- Displays:
  - Order number and status
  - Items table with quantities and prices
  - Order summary (subtotal, delivery fee, total)
  - Delivery information (date, time, address)
  - Customer notes
  - "View Order" button
  - Shop contact information

### 4. Controller Integration: `app/Http/Controllers/Admin/OrderController.php`
- Dispatches `SendOrderConfirmationEmail` job when order status changes to "confirmed"
- Only sends once (checks if `confirmed_at` is null)

## Configuration

### Mail Settings (.env)
```env
MAIL_MAILER=ses                          # Using AWS SES
MAIL_FROM_ADDRESS="hello@example.com"    # Update with your shop email
MAIL_FROM_NAME="${APP_NAME}"             # Uses your shop name

# AWS SES Configuration
AWS_ACCESS_KEY_ID=your-access-key        # Add your AWS credentials
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=eu-west-2             # Your AWS region
```

### Queue Configuration (.env)
```env
QUEUE_CONNECTION=database                # Using database queue (already configured)
```

## How It Works

1. **Admin confirms order:**
   - Admin updates order status to "confirmed" via API: `PATCH /api/admin/orders/{id}/status`
   
2. **Job is queued:**
   - `SendOrderConfirmationEmail` job is dispatched to the queue
   - Job is stored in the `jobs` table
   
3. **Queue worker processes job:**
   - Worker picks up the job from the queue
   - Email is sent to the customer's email address
   - Job is removed from the queue

4. **Customer receives email:**
   - Professional order confirmation with all details
   - Shop branding and contact information
   - Link to view order (if frontend route exists)

## Running the Queue Worker

### For Development (foreground):
```bash
php artisan queue:work --verbose
```

### For Production (background):
```bash
# Using supervisor or systemd to keep queue worker running
php artisan queue:work --daemon

# Or use Laravel Horizon for better queue management
composer require laravel/horizon
php artisan horizon
```

## Testing

### 1. Test Email Configuration:
```bash
php artisan tinker

# Send a test email
Mail::raw('Test email', function($message) {
    $message->to('your-email@example.com')
            ->subject('Test Email');
});
```

### 2. Test Order Confirmation:
```bash
# Start queue worker
php artisan queue:work --verbose

# In another terminal, create and confirm an order via API
curl -X PATCH http://localhost:8000/api/admin/orders/1/status \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"status": "confirmed"}'
```

### 3. Check Queue Status:
```bash
# View pending jobs
php artisan queue:work --once

# View failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry {id}

# Retry all failed jobs
php artisan queue:retry all
```

## Email Preview (Development)

For development without AWS SES credentials, you can use:

### Option 1: Log Driver (emails saved to logs)
```env
MAIL_MAILER=log
```

### Option 2: Mailtrap (testing service)
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
```

### Option 3: MailHog (local SMTP server)
```bash
# Install and run MailHog
docker run -d -p 1025:1025 -p 8025:8025 mailhog/mailhog

# Configure .env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```

Then visit http://localhost:8025 to view sent emails.

## Important Notes

### User Email Requirement
- Customers MUST have an email address to receive order confirmations
- Currently, users are created via phone-based OTP authentication
- **TODO:** Add email field to user registration/profile update

### Queue Worker in Production
- Queue worker must be running continuously
- Use process managers like Supervisor or systemd
- Monitor failed jobs regularly
- Set up alerts for failed job queues

### AWS SES Setup
1. Verify your domain or email addresses in AWS SES
2. Move out of SES sandbox mode for production
3. Set up proper DKIM, SPF, and DMARC records
4. Monitor bounce and complaint rates

### Customization
- Edit `resources/views/emails/orders/confirmed.blade.php` for email content
- Modify `app/Mail/OrderConfirmed.php` for subject line or additional data
- Update `app/Jobs/SendOrderConfirmationEmail.php` for additional logic

## Troubleshooting

### Emails not sending
1. Check queue worker is running: `ps aux | grep queue:work`
2. Check jobs table: `SELECT * FROM jobs;`
3. Check failed jobs: `php artisan queue:failed`
4. Check Laravel logs: `tail -f storage/logs/laravel.log`
5. Verify AWS credentials are correct
6. Check SES sending limits in AWS console

### Queue worker stops
1. Use process manager (Supervisor recommended)
2. Set up automatic restart on failure
3. Monitor memory usage (use `--memory` option)
4. Check for PHP errors in logs

### Emails go to spam
1. Set up DKIM, SPF, and DMARC records
2. Verify domain in AWS SES
3. Monitor bounce and complaint rates
4. Use consistent from address
5. Ensure email content is not spammy

## Future Enhancements

- [ ] Add email field to user registration
- [ ] Send order status update emails (processing, shipped, delivered)
- [ ] Send order cancellation emails
- [ ] Add email templates for other notifications (password reset, account updates)
- [ ] Implement email preferences (allow users to opt-out of certain emails)
- [ ] Add attachments (invoice PDF, receipt)
- [ ] Implement email templates in multiple languages
- [ ] Set up Laravel Horizon for better queue management and monitoring
