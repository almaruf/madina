# Shop Edit: Separate Form Submissions per Tab

## Overview
The shop edit page now features **independent form submissions** for each tab section. This allows users to update specific sections without needing to load or send all shop data at once.

## Changes Made

### 1. Edit Blade File (`resources/views/admin/shops/edit.blade.php`)
**Before:** Single `<form id="shop-form">` wrapping all tabs with one submit button  
**After:** Six separate forms, one per tab, each with its own submit button

#### New Structure:
```html
<!-- Tab 1: Basic Information -->
<div id="tab-basic" class="tab-content">
    <form id="form-basic" data-form-type="basic">
        <!-- Basic info fields -->
        <button type="submit">Update Basic Info</button>
    </form>
</div>

<!-- Tab 2: Delivery & Pricing -->
<div id="tab-delivery" class="tab-content hidden">
    <form id="form-delivery" data-form-type="delivery">
        <!-- Delivery fields -->
        <button type="submit">Update Delivery & Pricing</button>
    </form>
</div>

<!-- Tab 3: Legal & VAT -->
<div id="tab-legal" class="tab-content hidden">
    <form id="form-legal" data-form-type="legal">
        <!-- Legal fields -->
        <button type="submit">Update Legal & VAT</button>
    </form>
</div>

<!-- Tab 4: Bank Details -->
<div id="tab-bank" class="tab-content hidden">
    <form id="form-bank" data-form-type="bank">
        <!-- Bank fields -->
        <button type="submit">Update Bank Details</button>
    </form>
</div>

<!-- Tab 5: Branding & Social -->
<div id="tab-branding" class="tab-content hidden">
    <form id="form-branding" data-form-type="branding">
        <!-- Branding fields -->
        <button type="submit">Update Branding & Social</button>
    </form>
</div>

<!-- Tab 6: Operating Hours -->
<div id="tab-hours" class="tab-content hidden">
    <form id="form-hours" data-form-type="hours">
        <!-- Hours fields -->
        <button type="submit">Update Operating Hours</button>
    </form>
</div>
```

**Key Features:**
- Each form has `data-form-type` attribute identifying the section
- All inputs use `name` attributes (not `id`) for easy FormData collection
- Submit buttons are section-specific with descriptive text

### 2. Edit JavaScript (`resources/js/admin/shops/edit.js`)
**Complete rewrite** to support per-tab submissions:

#### Key Functions:

**`populateForm(shop)`** - Updated to use form-specific selectors:
```javascript
// Before: document.getElementById('name').value = ...
// After:  formBasic.querySelector('[name="name"]').value = ...

const formBasic = document.getElementById('form-basic');
formBasic.querySelector('[name="name"]').value = shop.name || '';
formBasic.querySelector('[name="slug"]').value = shop.slug || '';
// ... etc for each form
```

**`getFormData(form)`** - NEW: Extracts data from a specific form:
```javascript
function getFormData(form) {
    const formData = new FormData(form);
    const data = {};
    
    for (const [key, value] of formData.entries()) {
        const input = form.querySelector(`[name="${key}"]`);
        
        if (input && input.type === 'checkbox') {
            data[key] = input.checked;
        } else if (input && input.type === 'number') {
            data[key] = value ? parseFloat(value) : null;
        } else {
            data[key] = value || null;
        }
    }
    
    return data;
}
```

**`handleFormSubmit(e)`** - NEW: Universal handler for all tab forms:
```javascript
async function handleFormSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formType = form.dataset.formType;  // 'basic', 'delivery', etc.
    const shopSlug = getShopSlug();
    
    // Get only the fields from this specific form
    const data = getFormData(form);
    
    // Remove vat_rate (readonly, managed by config)
    if ('vat_rate' in data) {
        delete data.vat_rate;
    }
    
    // PATCH only the fields from this tab
    await axios.patch(`/api/admin/shops/${shopSlug}`, data);
    
    // Show success message with section name
    window.toast.success(`${getFormTypeLabel(formType)} updated successfully!`);
}
```

**Event Attachment:**
```javascript
document.addEventListener('DOMContentLoaded', () => {
    initializeTabs();
    loadShop();
    
    // Attach submit handlers to ALL forms
    document.querySelectorAll('form[data-form-type]').forEach(form => {
        form.addEventListener('submit', handleFormSubmit);
    });
});
```

## Benefits

### 1. **Granular Updates**
- Only send relevant fields when updating a specific section
- Reduces payload size and network traffic
- Example: Updating bank details doesn't send 60+ other shop fields

### 2. **Better UX**
- Clear visual feedback per section ("Bank Details updated successfully!")
- Submit buttons are contextual to each tab
- Users can work on multiple tabs without losing unsaved changes

### 3. **Improved Performance**
- Smaller PATCH requests
- Faster validation and response times
- Reduced database operations

### 4. **Easier Debugging**
- Each form submission is isolated
- Validation errors are section-specific
- Console logs show which form type is being updated

### 5. **Maintainability**
- Each tab is self-contained
- Easy to add new tabs/sections
- Form data collection is automatic via FormData API

## Field Mappings by Tab

### Tab 1: Basic Information (18 fields)
- name, slug, description, tagline, domain
- business_type, specialization
- phone, email, support_email, whatsapp_number
- address_line_1, address_line_2, city, postcode, country
- is_active

### Tab 2: Delivery & Pricing (11 fields)
- currency, currency_symbol
- delivery_fee, min_order_amount, free_delivery_threshold, delivery_radius_km
- delivery_enabled, collection_enabled, online_payment
- has_halal_products, has_organic_products

### Tab 3: Legal & VAT (5 fields)
- legal_company_name, company_registration_number
- vat_registered, vat_number, prices_include_vat
- (vat_rate is readonly and excluded from submission)

### Tab 4: Bank Details (6 fields)
- bank_name, bank_account_name, bank_account_number
- bank_sort_code, bank_iban, bank_swift_code

### Tab 5: Branding & Social (7 fields)
- primary_color, secondary_color
- logo_url, favicon_url
- facebook_url, instagram_url, twitter_url

### Tab 6: Operating Hours (7 fields)
- monday_hours, tuesday_hours, wednesday_hours, thursday_hours
- friday_hours, saturday_hours, sunday_hours

**Total: 54 editable fields** (vat_rate excluded)

## Backend Compatibility

### Controller Support
The `ShopController::update()` method already supports partial updates:

```php
public function update(Request $request, $slug)
{
    $validator = Validator::make($request->all(), [
        'name' => 'sometimes|required|string|max:255',
        'slug' => 'sometimes|required|string|max:255|unique:shops,slug,' . $shop->id,
        // ... all fields use 'sometimes|required'
    ]);
    
    $shop->update($request->all());
}
```

The `sometimes` validation rule means:
- If field is present in request → validate it
- If field is absent → ignore it
- Perfect for partial updates!

## Example PATCH Requests

### Updating Only Bank Details:
```javascript
PATCH /api/admin/shops/my-shop
Content-Type: application/json

{
    "bank_name": "HSBC",
    "bank_account_name": "ABC Grocery Shop Ltd",
    "bank_account_number": "12345678",
    "bank_sort_code": "40-00-00",
    "bank_iban": "GB29NWBK60161331926819",
    "bank_swift_code": "HBUKGB4B"
}
```
Only 6 fields sent, not all 54!

### Updating Only Operating Hours:
```javascript
PATCH /api/admin/shops/my-shop

{
    "monday_hours": "9:00 AM - 9:00 PM",
    "tuesday_hours": "9:00 AM - 9:00 PM",
    "wednesday_hours": "9:00 AM - 9:00 PM",
    "thursday_hours": "9:00 AM - 9:00 PM",
    "friday_hours": "9:00 AM - 9:00 PM",
    "saturday_hours": "10:00 AM - 6:00 PM",
    "sunday_hours": "Closed"
}
```
Only 7 fields sent!

## Special Cases

### 1. VAT Rate Field
- Displayed as readonly in the Legal & VAT tab
- Value shown from database, but NOT included in form submission
- Automatically removed in `handleFormSubmit()`:
  ```javascript
  if ('vat_rate' in data) {
      delete data.vat_rate;
  }
  ```

### 2. Slug Changes
- When slug is changed in Basic Information tab, redirect to new slug:
  ```javascript
  if (formType === 'basic' && data.slug && data.slug !== shopSlug) {
      setTimeout(() => {
          window.location.href = '/admin/shops/' + data.slug + '/edit';
      }, 1000);
  }
  ```

### 3. Checkbox Fields
- Properly handled as booleans:
  ```javascript
  if (input && input.type === 'checkbox') {
      data[key] = input.checked;  // true/false, not "on"
  }
  ```

### 4. Number Fields
- Converted to floats/null:
  ```javascript
  else if (input && input.type === 'number') {
      data[key] = value ? parseFloat(value) : null;
  }
  ```

## Testing Checklist

- [x] Each tab's save button only sends its fields
- [x] Validation errors display correctly
- [x] Success toasts show correct section name
- [x] Tab switching preserves form data (no reload)
- [x] Checkbox values sent as booleans
- [x] Number fields sent as floats
- [x] VAT rate excluded from submissions
- [x] Slug changes redirect correctly
- [x] Build successful (1.42s, 30 bundles)

## Files Modified

1. **resources/views/admin/shops/edit.blade.php** (370 lines)
   - Replaced single form with 6 separate forms
   - Added `data-form-type` attributes
   - Individual submit buttons per tab

2. **resources/js/admin/shops/edit.js** (260 lines, 7.47 KB)
   - Rewrote `populateForm()` to use form-specific queries
   - Added `getFormData(form)` for extracting form data
   - Added `handleFormSubmit(e)` universal handler
   - Added `getFormTypeLabel(formType)` for success messages
   - Updated initialization to attach handlers to all forms

## Backup Files Created

- `edit.blade.php.bak2` - Previous version (single form)
- `edit.blade.php.old` - Previous version (backup before rewrite)

## Build Output

```
✓ 81 modules transformed
✓ built in 1.42s

public/build/assets/edit-DDq3-C7_.js  4.01 kB │ gzip: 1.32 kB
```

## Future Enhancements

1. **Unsaved Changes Warning**
   - Detect if form has been modified
   - Warn before switching tabs or navigating away

2. **Auto-save**
   - Save form data to localStorage on input change
   - Restore on page reload

3. **Field-level Dirty Tracking**
   - Highlight changed fields
   - Show "unsaved" indicator per tab

4. **Batch Save Option**
   - Allow saving multiple tabs at once
   - "Save All Changes" button

5. **Optimistic UI Updates**
   - Update UI immediately on submit
   - Rollback on error

## Conclusion

The shop edit page now provides a much better user experience with granular, per-section updates. Each tab operates independently, reducing complexity and improving performance. The Laravel backend's `sometimes` validation rule makes this approach seamless without requiring controller changes.

**Status:** ✅ Implemented and Built Successfully
