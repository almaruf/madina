# Blade Files Update Instructions

This document contains the exact replacements needed to update all 12 admin blade files to use the newly extracted JavaScript modules.

## Summary of Changes
- Remove all `<script>` tags containing inline JavaScript
- Add `@section('scripts')` with `@vite` references to the new JS modules
- Preserve all HTML structure and PHP/Blade directives
- Some files need special attributes added (e.g., data-user-id for users/edit)

## Files to Update

### 1. products/edit.blade.php
**File**: `resources/views/admin/products/edit.blade.php`
**Action**: Remove the entire `<script>` block at the end (after the closing `</div>` of content-wrapper) and replace with:

```blade
@endsection

@section('scripts')
    @vite(['resources/js/admin/products/edit.js'])
@endsection
```

---

### 2. users/edit.blade.php
**File**: `resources/views/admin/users/edit.blade.php`
**Action 1**: Add data attribute to body tag. Find the line with `@extends('admin.layout')` and add after the @section('content'):

```blade
@section('content')
<div class="content-wrapper" data-user-id="{{ $userId }}">
```

**Action 2**: Remove the entire `<script>` block at the end and replace with:

```blade
@endsection

@section('scripts')
    <script>
        // Pass userId to JS module
        window.userId = {{ $userId }};
    </script>
    @vite(['resources/js/admin/users/edit.js'])
@endsection
```

---

### 3. categories/edit.blade.php
**File**: `resources/views/admin/categories/edit.blade.php`
**Action**: Remove the entire `<script>` block at the end and replace with:

```blade
@endsection

@section('scripts')
    @vite(['resources/js/admin/categories/edit.js'])
@endsection
```

---

### 4. shops/edit.blade.php
**File**: `resources/views/admin/shops/edit.blade.php`
**Action**: Remove the entire `<script>` block at the end and replace with:

```blade
@endsection

@section('scripts')
    @vite(['resources/js/admin/shops/edit.js'])
@endsection
```

---

### 5. shops/create.blade.php
**File**: `resources/views/admin/shops/create.blade.php`
**Action**: Remove the entire `<script>` block at the end and replace with:

```blade
@endsection

@section('scripts')
    @vite(['resources/js/admin/shops/create.js'])
@endsection
```

---

### 6. offers/create.blade.php
**File**: `resources/views/admin/offers/create.blade.php`
**Action**: Remove the entire `<script>` block at the end and replace with:

```blade
@endsection

@section('scripts')
    @vite(['resources/js/admin/offers/create.js'])
@endsection
```

---

### 7. offers/create-percentage-discount.blade.php
**File**: `resources/views/admin/offers/create-percentage-discount.blade.php`
**Action**: Remove the entire `<script>` block at the end and replace with:

```blade
@endsection

@section('scripts')
    @vite(['resources/js/admin/offers/create-percentage-discount.js'])
@endsection
```

---

### 8. offers/create-bxgy.blade.php
**File**: `resources/views/admin/offers/create-bxgy.blade.php`
**Action**: Remove the entire `<script>` block at the end and replace with:

```blade
@endsection

@section('scripts')
    @vite(['resources/js/admin/offers/create-bxgy.js'])
@endsection
```

---

### 9. offers/edit.blade.php
**File**: `resources/views/admin/offers/edit.blade.php`
**Action**: Remove the entire `<script>` block at the end and replace with:

```blade
@endsection

@section('scripts')
    @vite(['resources/js/admin/offers/edit.js'])
@endsection
```

---

### 10. offers/edit-percentage-discount.blade.php
**File**: `resources/views/admin/offers/edit-percentage-discount.blade.php`
**Action**: Remove the entire `<script>` block at the end and replace with:

```blade
@endsection

@section('scripts')
    @vite(['resources/js/admin/offers/edit-percentage-discount.js'])
@endsection
```

---

### 11. offers/edit-bxgy.blade.php
**File**: `resources/views/admin/offers/edit-bxgy.blade.php`
**Action**: Remove the entire `<script>` block at the end and replace with:

```blade
@endsection

@section('scripts')
    @vite(['resources/js/admin/offers/edit-bxgy.js'])
@endsection
```

---

### 12. queue/index.blade.php
**File**: `resources/views/admin/queue/index.blade.php`
**Action**: Remove the entire `<script>` block at the end and replace with:

```blade
@endsection

@section('scripts')
    @vite(['resources/js/admin/queue/index.js'])
@endsection
```

---

## Verification Steps

After making all the changes:

1. **Rebuild assets**:
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

2. **Clear Laravel cache**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

3. **Test each page** in the browser:
   - Check browser console for any JavaScript errors
   - Verify all forms submit correctly
   - Test all buttons and interactive elements
   - Verify modals open and close properly
   - Check toast notifications appear

4. **Common issues to check**:
   - Ensure all onclick handlers in HTML call window-exposed functions
   - Verify axios is making authenticated requests (Authorization header)
   - Check that window.toast is working for notifications
   - Confirm DOMContentLoaded fires before accessing DOM elements

## Notes

- **users/edit.blade.php** requires special handling because it needs to pass `userId` to the JavaScript module
- All other pages work by reading URL parameters (e.g., `?id=123`)
- The postcodes.io integration in users/edit still works - it's in the JS module now
- All product selection modals in offer pages are fully functional
- Queue page includes auto-refresh every 30 seconds
- All inline onclick handlers now call window-exposed functions (e.g., `window.openProductModal()`)

## Rollback Plan

If you need to rollback:
1. Keep the original blade files backed up
2. Remove the new entries from `vite.config.js`
3. Optionally delete the JS modules from `resources/js/admin/`
4. Run `npm run build` again
