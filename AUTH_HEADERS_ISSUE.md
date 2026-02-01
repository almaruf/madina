# 🔴 CRITICAL: Authentication Headers Issue - ALWAYS CHECK THIS

## THE PROBLEM

**401 Unauthorized errors in admin pages are almost ALWAYS caused by missing authentication headers in axios requests.**

This is a **RECURRING ISSUE** in this project. Every new admin page that makes API calls MUST explicitly configure axios with the auth token.

## WHY IT HAPPENS

1. **Layout interceptors are not enough**: While `admin/layout.blade.php` sets up axios interceptors globally, individual page scripts sometimes create their own axios instances or don't inherit the global configuration properly.

2. **Script execution order**: JavaScript in `@section('content')` may execute before or independently of the layout's script section.

3. **Axios defaults don't persist**: Setting `axios.defaults` in one script doesn't always propagate to all subsequent requests in other scripts.

## THE SOLUTION

**EVERY admin page that makes API calls MUST include this code block at the START of its `<script>` section:**

```javascript
<script>
// CRITICAL: Ensure axios is configured with auth token
// This MUST be at the top of every admin page that makes API calls
const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
if (!token) {
    console.error('No auth token found');
    window.location.href = '/admin/login';
} else {
    // Explicitly set auth headers for all axios requests from this page
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    axios.defaults.headers.common['Accept'] = 'application/json';
    axios.defaults.headers.common['Content-Type'] = 'application/json';
    console.log('Auth token configured for axios:', token.substring(0, 20) + '...');
}

// Your page-specific code starts here...
</script>
```

## CHECKLIST FOR NEW ADMIN PAGES

When creating a new admin page that makes API calls:

- [ ] Add the auth token configuration block at the START of the script section
- [ ] Verify token is logged in browser console: "Auth token configured for axios: ..."
- [ ] Test API calls and check Network tab for `Authorization: Bearer {token}` header
- [ ] If 401 error occurs, check:
  - [ ] Is the auth block present?
  - [ ] Is it BEFORE any axios calls?
  - [ ] Is token in localStorage? (check Application → Local Storage in DevTools)
  - [ ] Does Network tab show Authorization header in request?

## DEBUGGING 401 ERRORS

### Step 1: Check Browser Console
Look for:
```
Auth token configured for axios: 1|abc...
```

If missing, the auth block wasn't executed or token doesn't exist.

### Step 2: Check Network Tab
1. Open DevTools → Network
2. Find the failing request
3. Click on it → Headers tab
4. Look for `Request Headers` section
5. Verify `Authorization: Bearer 1|{token}` is present

**If missing**, the auth block isn't setting headers correctly.

### Step 3: Check Local Storage
1. DevTools → Application → Local Storage → your domain
2. Look for `auth_token` key
3. Value should be like `1|abc123...`

**If missing**, user isn't logged in or token was cleared.

### Step 4: Verify Token is Valid
```javascript
// In browser console:
axios.get('/api/auth/user')
  .then(r => console.log('Token valid:', r.data))
  .catch(e => console.log('Token invalid:', e.response));
```

## COMMON MISTAKES

### ❌ DON'T: Rely only on layout.blade.php interceptors
```javascript
// In layout.blade.php
axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

// In your page - assuming it will work (IT WON'T)
axios.get('/api/admin/offers'); // May fail with 401
```

### ✅ DO: Explicitly configure axios in each page
```javascript
// In your page script section
const token = localStorage.getItem('auth_token');
axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
axios.get('/api/admin/offers'); // Will work
```

### ❌ DON'T: Put axios calls before auth configuration
```javascript
<script>
async function loadData() {
    await axios.get('/api/admin/data'); // WILL FAIL
}

// Auth config comes too late
const token = localStorage.getItem('auth_token');
axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

loadData(); // 401 error
</script>
```

### ✅ DO: Configure auth FIRST, then define functions
```javascript
<script>
// Auth configuration FIRST
const token = localStorage.getItem('auth_token');
axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

// Then define functions
async function loadData() {
    await axios.get('/api/admin/data'); // Will work
}

loadData();
</script>
```

## PAGES THAT HAVE BEEN FIXED

Track which pages have the auth block implemented:

- [x] `/resources/views/admin/layout.blade.php` - Global interceptor (NOT enough alone)
- [x] `/resources/views/admin/offers.blade.php` - Has explicit auth block ✅
- [ ] `/resources/views/admin/products.blade.php` - CHECK THIS
- [ ] `/resources/views/admin/orders.blade.php` - CHECK THIS
- [ ] `/resources/views/admin/categories.blade.php` - CHECK THIS
- [ ] `/resources/views/admin/users.blade.php` - CHECK THIS
- [ ] `/resources/views/admin/delivery-slots.blade.php` - CHECK THIS
- [ ] `/resources/views/admin/shops.blade.php` - CHECK THIS
- [ ] `/resources/views/admin/admin-users.blade.php` - CHECK THIS

## TESTING TEMPLATE

Use this to test authentication on any admin page:

```javascript
// In browser console on the admin page
console.log('1. Token exists:', !!localStorage.getItem('auth_token'));
console.log('2. Axios default headers:', axios.defaults.headers.common);
console.log('3. Test request:');
axios.get('/api/auth/user')
    .then(() => console.log('✅ Auth working'))
    .catch(e => console.error('❌ Auth failed:', e.response?.status));
```

Expected output:
```
1. Token exists: true
2. Axios default headers: {Authorization: "Bearer 1|abc...", Accept: "application/json", ...}
3. Test request:
✅ Auth working
```

## QUICK FIX FOR ANY PAGE

If you get a 401 error on any admin page, add this immediately after the `<script>` tag:

```javascript
const token = localStorage.getItem('auth_token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}
```

## WHY THIS ISN'T IN A SHARED FILE

You might ask: "Why not put this in a shared JS file?"

**Answer**: We tried. The timing of when that file loads vs when page scripts execute causes race conditions. Explicit, inline configuration is the most reliable solution for this project's architecture.

## COPILOT INSTRUCTIONS

**For GitHub Copilot / AI Assistants working on this project:**

When creating ANY new admin page that makes API calls:

1. ALWAYS add the auth token configuration block at the START of the `<script>` section
2. Reference this file: `/workspaces/madina/AUTH_HEADERS_ISSUE.md`
3. Add a comment: `// See AUTH_HEADERS_ISSUE.md for why this is required`
4. Test the page and verify the Authorization header is present in Network tab
5. Update the "PAGES THAT HAVE BEEN FIXED" checklist in this document

## RELATED FILES

- `/resources/views/admin/layout.blade.php` - Global setup (interceptors)
- `/.github/copilot-instructions.md` - Project architecture
- `/AUTH_HEADERS_ISSUE.md` - This file

## LAST UPDATED

**Date**: February 1, 2026  
**Issue**: Offers page had 401 errors  
**Fix**: Added explicit auth configuration to offers.blade.php  
**Reported by**: User (recurring issue)

---

**Remember**: If you're getting 401 errors on admin pages, CHECK THIS FILE FIRST! 🔴
