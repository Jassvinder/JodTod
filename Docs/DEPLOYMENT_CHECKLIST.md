# JodTod - Deployment & Build Checklist

## APK Build (Testing on Same WiFi)

### Before Building APK:
1. **Mobile `.env`** (`D:\JodTodApp\.env`):
   ```env
   EXPO_PUBLIC_API_URL=http://<YOUR-PC-IP>:8000
   ```
   Run `ipconfig` to get current WiFi IP.

2. **PC pe Laravel server chalao**:
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

3. **Build APK** (EAS Build):
   ```bash
   cd D:\JodTodApp
   eas build --platform android --profile preview
   ```
   Ya local build:
   ```bash
   eas build --platform android --profile preview --local
   ```

4. **APK download karo** aur phone me install karo
5. **Same WiFi** pe PC aur phone connect karo
6. App chalegi + push notifications bhi kaam karengi

### Note:
- APK me IP hardcoded hota hai - WiFi/IP badle to naya APK banana padega
- Ya phir production server pe deploy karo to koi IP issue nahi

---

## Production Deployment Checklist

### Laravel Backend (.env changes):
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

QUEUE_CONNECTION=database
SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true

LOG_LEVEL=warning

# Sanctum
SANCTUM_STATEFUL_DOMAINS=yourdomain.com
SANCTUM_EXPIRATION_MINUTES=10080
```

### Laravel Commands:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
php artisan storage:link
```

### Queue Worker (must run as daemon):
```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```
Use Supervisor or systemd to keep it running.

### Mobile App (.env for production):
```env
EXPO_PUBLIC_API_URL=https://yourdomain.com
```

### EAS Build for Production:
```bash
# Android
eas build --platform android --profile production

# iOS
eas build --platform ios --profile production
```

### Security Headers (add to nginx/apache):
```
X-Frame-Options: DENY
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Strict-Transport-Security: max-age=31536000; includeSubDomains
Referrer-Policy: strict-origin-when-cross-origin
```

### Post-Deploy Verification:
- [ ] Login/Register works
- [ ] Email verification sends
- [ ] OTP sends via SMS provider
- [ ] Push notifications arrive
- [ ] File uploads work (storage linked)
- [ ] Queue worker processing notifications
- [ ] HTTPS enforced
- [ ] APP_DEBUG is false
- [ ] No sensitive data in logs
