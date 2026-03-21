# JodTod - Security Audit & Hardening Report

**Last Audit Date:** 2026-03-20
**Audited By:** Security review of full codebase (controllers, models, routes, middleware, config, frontend)

---

## Security Measures In Place

### Authentication & Authorization
| Control | Status | Details |
|---------|--------|---------|
| Password hashing | SECURE | bcrypt with 12 rounds |
| API auth | SECURE | Laravel Sanctum token-based |
| Token expiration | SECURE | 7-day expiration configured |
| CSRF protection | SECURE | Inertia handles web CSRF, API uses token auth |
| Email verification | ENFORCED | `verified` middleware on protected routes |
| Phone verification | ENFORCED | `phone.verified` middleware on group routes |
| Banned user check | ENFORCED | CheckBanned middleware on both web and API routes |
| Rate limiting (auth) | ENFORCED | Login: 5/min, OTP: 3/min |
| Session security | SECURE | Database driver, HTTP-only cookies |

### Data Protection
| Control | Status | Details |
|---------|--------|---------|
| SQL injection | PROTECTED | All queries use Eloquent parameterized bindings |
| XSS | PROTECTED | Vue.js auto-escapes, Blade `{{ }}` escapes |
| Mass assignment | PROTECTED | `role` and `banned_at` removed from User $fillable, admin uses `forceFill()` |
| Hidden fields | SECURE | `password`, `remember_token`, `google_id` in User `$hidden` |
| File uploads | VALIDATED | Image type validation, 5MB max, WebP conversion (no fallback to raw storage) |
| OTP logging | SAFE | OTP codes only logged in `local` environment |

### IDOR Protection (Insecure Direct Object Reference)
| Resource | Protection Method |
|----------|-------------------|
| Expenses | `where('user_id', Auth::id())` on all CRUD |
| Incomes | `where('user_id', Auth::id())` on all CRUD |
| Todos | `authorizeTodoOwner()` / `authorizeTodoOrAssignee()` checks |
| Contacts | `where('user_id', Auth::id())` on all operations |
| Groups | `ensureMember()` check, `isAdmin()` for admin actions |
| Group Expenses | `ensureMember()` + `ensureExpenseBelongsToGroup()` |
| Settlements | Group membership + role-based access (from_user/to_user/admin) |
| Todo Categories | User ownership verified on CRUD |
| Profile | `$request->user()` - always operates on authenticated user |

### Settlement Security
| Control | Status |
|---------|--------|
| Race condition prevention | DB transaction with `lockForUpdate()` |
| Double completion prevention | Idempotency check (`status === 'completed'`) |
| Admin-only settle up | `isAdmin()` check enforced |
| Zero-amount splits blocked | Validation `min:0.01` on share_amount |

---

## Fixes Applied (2026-03-20)

### Critical Fixes
1. **Mass Assignment Privilege Escalation** - Removed `role` and `banned_at` from User `$fillable`. Admin operations use `forceFill()`. Previously, a crafted API request could elevate user to admin.

2. **Rate Limiting on Auth Endpoints** - Added `throttle:5,1` on login/register/forgot-password and `throttle:3,1` on OTP send/verify. Prevents brute force attacks.

3. **OTP Code Exposure in Logs** - OTP codes now only logged in `local` environment. Production logs won't contain plain-text OTPs.

4. **Sanctum Token Expiration** - Set to 7 days (was: never expires). Leaked tokens now have limited lifetime.

5. **Settlement Double Completion** - Added idempotency check. Same settlement can't be marked completed twice (prevents duplicate personal expenses).

6. **File Upload RCE Risk** - Removed fallback that stored original file on WebP conversion failure. Now throws exception instead of storing potentially executable files.

7. **Zero-Amount Splits** - Changed validation from `min:0` to `min:0.01`. Prevents users from adding themselves to splits with zero debt.

8. **Todo Assignee Privilege Escalation** - Only todo owner can change `assigned_to` field. Previously, assignees could reassign todos.

---

## Production Deployment Checklist

### Environment Variables (MUST SET)
```env
APP_DEBUG=false
APP_ENV=production
APP_URL=https://yourdomain.com

SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true

SANCTUM_EXPIRATION_MINUTES=10080

LOG_LEVEL=warning
```

### Recommended Security Headers
Add to web server (nginx/apache) or middleware:
```
X-Frame-Options: DENY
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Strict-Transport-Security: max-age=31536000; includeSubDomains
Referrer-Policy: strict-origin-when-cross-origin
```

### HTTPS
- Force HTTPS on all routes
- Set `FORCE_HTTPS=true` or configure web server redirect
- Sanctum cookies must be secure-only in production

---

## Known Limitations & Future Improvements

### Should Fix Before Production
| Item | Priority | Description |
|------|----------|-------------|
| CORS configuration | HIGH | Create explicit `config/cors.php` with allowed origins list |
| File MIME validation | MEDIUM | Add `mimetypes:image/jpeg,image/png,image/webp` to all upload validations |
| Search input length | LOW | Add `max:50` validation on all search/filter inputs |
| Admin audit logging | MEDIUM | Log admin actions (role changes, bans, deletions) to separate audit table |

### Nice to Have (Post-Launch)
| Item | Description |
|------|-------------|
| 2FA for admin | Two-factor authentication for admin panel access |
| API key rotation | Allow users to regenerate API tokens |
| Account lockout | Lock account after 10 failed login attempts (30 min cooldown) |
| Security headers middleware | Laravel middleware for security headers |
| CSP (Content Security Policy) | Strict CSP headers to prevent inline script injection |
| Subresource Integrity | SRI hashes on external scripts/styles |

---

## Threat Model Summary

| Threat | Mitigated? | How |
|--------|------------|-----|
| Brute force login | YES | Rate limiting (5 attempts/min) |
| Password cracking | YES | bcrypt 12 rounds |
| Session hijacking | YES | HTTP-only, secure cookies, database sessions |
| CSRF attacks | YES | Inertia + Sanctum token auth |
| SQL injection | YES | Eloquent ORM parameterized queries |
| XSS | YES | Vue.js auto-escaping, Blade escaping |
| IDOR | YES | Ownership checks on every resource |
| Privilege escalation | YES | role/banned_at not mass-assignable |
| File upload RCE | YES | Image validation + WebP-only storage |
| Token theft | PARTIAL | 7-day expiry, no rotation yet |
| Man-in-the-middle | REQUIRES | HTTPS enforcement in production |
| Data breach (DB) | PARTIAL | Passwords hashed, sessions encrypted (if enabled) |
