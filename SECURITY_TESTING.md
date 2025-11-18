# Security Testing Guide for ImageBoard

This guide helps you test the security of your imageboard application.

## 🔴 Critical Vulnerability Fixed

**IDOR (Insecure Direct Object Reference)** - Fixed in:
- `ThreadController::show()`
- `AdminController::deleteThread()`
- `AdminController::togglePinThread()`
- `AdminController::toggleLockThread()`
- `AdminController::deletePost()`
- `PostController::store()`

## Security Test Checklist

### 1. SQL Injection Testing

**Status:** ✅ Protected by Laravel Eloquent ORM

**Test Cases:**
```
Subject: Test'; DROP TABLE threads; --
Content: ' OR '1'='1
Name: '; DELETE FROM posts; --
```

**Expected Result:** Input should be escaped and stored as plain text, not executed.

**How to Test:**
1. Go to `/b/thread/create`
2. Enter the test strings above
3. Submit and verify they're stored as text
4. Check database to confirm no queries were executed

---

### 2. XSS (Cross-Site Scripting) Testing

**Status:** ✅ Protected by Blade `e()` helper

**Test Cases:**
```html
<script>alert('XSS')</script>
<img src=x onerror="alert('XSS')">
<svg onload="alert('XSS')">
<iframe src="javascript:alert('XSS')">
```

**Expected Result:** Scripts should display as text, NOT execute.

**How to Test:**
1. Create a new thread with XSS payload in content
2. View the thread
3. Verify no alert popup appears
4. Inspect HTML - should see escaped characters like `&lt;script&gt;`

---

### 3. CSRF Protection Testing

**Status:** ✅ Protected by Laravel CSRF middleware

**Test Cases:**
- Remove `@csrf` token from form
- Submit form from external site
- Replay old form submission

**How to Test:**
1. Open browser DevTools (F12)
2. Find any form and remove `<input type="hidden" name="_token">`
3. Try to submit
4. Should receive 419 error page

---

### 4. File Upload Security Testing

**Status:** ✅ Validated (images only, 5MB limit)

**Test Cases:**

#### 4.1 Upload Malicious File Types
```
- .php file renamed to .jpg
- .exe file
- .zip file
- Double extension: malicious.php.jpg
```

**Expected Result:** Should be rejected by validation

**How to Test:**
1. Go to `/b/thread/create`
2. Try uploading non-image files
3. Should fail with validation error

#### 4.2 Upload Oversized Files
- Upload a 10MB image file

**Expected Result:** Should fail with "max:5120" validation error

#### 4.3 Image Bomb Attack
- Upload extremely large resolution image
- Upload malformed image file

**Expected Result:** Should be handled by GD library or rejected

---

### 5. IDOR (Insecure Direct Object Reference) Testing

**Status:** ✅ FIXED - Now validates relationships

**Test Cases:**

#### 5.1 Cross-Board Thread Access
1. Create thread in `/b/` (note thread ID, e.g., 1)
2. Try accessing: `/tech/thread/1`
3. Should return 404 error

#### 5.2 Cross-Thread Post Deletion
1. Login as admin
2. Try deleting post from thread A using thread B's URL
3. Should return 404 error

**How to Test:**
```bash
# Create thread in /b/
POST /b/thread

# Try accessing from different board
GET /tech/thread/1  # Should 404
```

---

### 6. Authentication & Authorization Testing

**Status:** ✅ Protected by auth middleware

**Test Cases:**

#### 6.1 Unauthorized Admin Access
```
/admin/dashboard
/admin/boards/create
/admin/boards/1/edit
```

**Expected Result:** Redirect to `/admin/login`

**How to Test:**
1. Logout from admin
2. Try accessing admin URLs directly
3. Should redirect to login page

#### 6.2 Privilege Escalation
1. Login as regular user (if applicable)
2. Try accessing admin endpoints
3. Should be denied

---

### 7. Input Validation Testing

**Test Cases:**

#### 7.1 Length Limits
- Subject: 101 characters (max 100)
- Content: 2001 characters (max 2000)
- Name: 101 characters (max 100)

**Expected Result:** Validation error

#### 7.2 Required Fields
- Submit thread without subject
- Submit thread without content
- Submit thread without image

**Expected Result:** Validation errors for missing required fields

---

### 8. Session Security Testing

**Test Cases:**

#### 8.1 Session Fixation
1. Get session cookie before login
2. Login
3. Check if session ID changed

**Expected Result:** Session ID should regenerate after login

#### 8.2 Session Hijacking
1. Login as admin
2. Copy session cookie
3. Open incognito window
4. Set the copied cookie
5. Try accessing admin panel

**Expected Result:** Should work (this is expected behavior, protect against XSS to prevent cookie theft)

---

### 9. Rate Limiting Testing

**Status:** ⚠️ NOT IMPLEMENTED

**Recommendation:** Add rate limiting to prevent:
- Spam posting
- Brute force login attempts
- Image upload flooding

**How to Implement:**
```php
// In routes/web.php
Route::post('/{board}/thread', [ThreadController::class, 'store'])
    ->middleware('throttle:10,1'); // 10 requests per minute
```

---

### 10. Security Headers Testing

**Check these headers in browser DevTools:**
```
X-Frame-Options: Should be set (prevents clickjacking)
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Content-Security-Policy: Check if needed
```

**How to Test:**
1. Open browser DevTools
2. Go to Network tab
3. Load any page
4. Check Response Headers

---

## Automated Security Testing Tools

### 1. OWASP ZAP
```bash
# Install ZAP, then:
zap-cli quick-scan http://localhost:88
```

### 2. SQLMap (SQL Injection)
```bash
sqlmap -u "http://localhost:88/b/thread" --forms --crawl=2
```

### 3. XSStrike (XSS Detection)
```bash
python3 xsstrike.py -u "http://localhost:88/b/thread/create"
```

## Security Best Practices Checklist

- [x] SQL Injection Prevention (Eloquent ORM)
- [x] XSS Prevention (Blade escaping)
- [x] CSRF Protection (Laravel middleware)
- [x] File Upload Validation
- [x] IDOR Prevention (Added in fixes)
- [x] Authentication Guards
- [ ] Rate Limiting
- [ ] Security Headers
- [ ] Logging and Monitoring
- [ ] Input Sanitization
- [ ] Password Strength Requirements (for admin)

## Reporting Security Issues

If you find a security vulnerability:
1. **DO NOT** open a public GitHub issue
2. Email: [your-email@example.com]
3. Include:
   - Description of vulnerability
   - Steps to reproduce
   - Potential impact
   - Suggested fix (if any)

## Production Security Checklist

Before deploying to production:

- [ ] Change default admin credentials
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Use HTTPS only
- [ ] Configure proper file permissions
- [ ] Enable rate limiting
- [ ] Set up security headers
- [ ] Configure proper CORS policy
- [ ] Use strong session encryption
- [ ] Set up automated backups
- [ ] Configure proper logging
- [ ] Set up monitoring and alerts

## Additional Resources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel Security Best Practices](https://laravel.com/docs/security)
- [Web Security Testing Guide](https://owasp.org/www-project-web-security-testing-guide/)
