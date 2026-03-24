# JodTod - Deployment Guide (Web + Mobile)

## Table of Contents
1. [Requirements](#requirements)
2. [Web App Deployment (Laravel)](#web-app-deployment)
3. [Mobile App Deployment (React Native)](#mobile-app-deployment)
4. [Post-Deployment Checklist](#post-deployment-checklist)

---

## Requirements

### Server Requirements (for Web App)
- **VPS/Cloud Server** (DigitalOcean, AWS, Hostinger VPS, etc.)
  - Minimum: 1 vCPU, 1GB RAM, 25GB SSD
  - Recommended: 2 vCPU, 2GB RAM, 50GB SSD
  - Cost: ~$5-12/month
- **OS:** Ubuntu 22.04 or 24.04 LTS
- **PHP:** 8.2+ with extensions: mbstring, xml, curl, mysql, gd, zip, bcmath, openssl
- **MySQL:** 8.0+
- **Nginx** or Apache
- **Composer** (PHP dependency manager)
- **Node.js** 18+ and npm (for building frontend assets)
- **SSL Certificate** (free via Let's Encrypt / Certbot)
- **Domain Name** (e.g., jodtod.com)
- **SMTP Email Service** (Gmail SMTP free, or Resend/Mailgun for production)

### Mobile App Requirements
- **Expo Account** (free - already have: jasscodetools)
- **EAS CLI** (already installed)
- **Google Play Developer Account** ($25 one-time fee) - for Play Store
- **Apple Developer Account** ($99/year) - for App Store (optional, Android first)
- **Firebase Project** (free - already have: jodtod-jv)

### Cost Summary
| Item | Cost | Frequency |
|------|------|-----------|
| VPS Server | $5-12 | Monthly |
| Domain (.com) | $10-15 | Yearly |
| SSL Certificate | Free | Auto-renew |
| Google Play Developer | $25 | One-time |
| Apple Developer | $99 | Yearly (optional) |
| Firebase | Free | Free tier enough |
| Expo/EAS | Free | 30 builds/month free |
| **Total (without iOS)** | **~$40 first month, ~$7/month after** | |

---

## Web App Deployment

### Step 1: Buy Domain & Server

1. **Domain:** Buy from Namecheap, GoDaddy, or Cloudflare (~$10/year)
2. **Server:** Buy VPS from DigitalOcean/Hostinger/AWS
3. Point domain DNS to server IP (A record: `@` → server IP, `www` → server IP)

### Step 2: Server Setup (SSH into server)

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.3 + extensions
sudo add-apt-repository ppa:ondrej/php -y
sudo apt install -y php8.3 php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl php8.3-gd php8.3-zip php8.3-bcmath php8.3-intl php8.3-redis

# Install MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Install Nginx
sudo apt install -y nginx

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Install Certbot (SSL)
sudo apt install -y certbot python3-certbot-nginx

# Install Supervisor (for queue worker)
sudo apt install -y supervisor
```

### Step 3: Create MySQL Database

```bash
sudo mysql
```
```sql
CREATE DATABASE jodtod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'jodtod'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON jodtod.* TO 'jodtod'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 4: Deploy Laravel Code

```bash
# Create web directory
sudo mkdir -p /var/www/jodtod
sudo chown $USER:$USER /var/www/jodtod

# Clone repository
cd /var/www/jodtod
git clone https://github.com/YOUR_REPO/jodtod.git .

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies & build frontend
npm install
npm run build

# Setup environment
cp .env.example .env
php artisan key:generate
```

### Step 5: Configure .env (Production)

Edit `/var/www/jodtod/.env`:
```env
APP_NAME=JodTod
APP_ENV=production
APP_KEY=base64:xxxxx (already generated)
APP_DEBUG=false
APP_URL=https://jodtod.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jodtod
DB_USERNAME=jodtod
DB_PASSWORD=YOUR_STRONG_PASSWORD_HERE

QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@jodtod.com
MAIL_FROM_NAME="JodTod"

SESSION_DRIVER=database
CACHE_STORE=file

# reCAPTCHA v3 - Bot protection on registration
# Get keys from: https://www.google.com/recaptcha/admin
# Select "reCAPTCHA v3", add your domain
RECAPTCHA_SITE_KEY=your_site_key_here
RECAPTCHA_SECRET_KEY=your_secret_key_here

# Firebase FCM - Push notifications
# Service account file should be at: storage/app/firebase-service-account.json
# Download from: Firebase Console → Project Settings → Service Accounts → Generate New Private Key
```

### Step 6: Run Migrations & Setup

```bash
cd /var/www/jodtod

# Run database migrations
php artisan migrate --force

# Seed categories
php artisan db:seed --class=CategorySeeder --force

# Create storage link
php artisan storage:link

# Cache config for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
sudo chown -R www-data:www-data /var/www/jodtod/storage /var/www/jodtod/bootstrap/cache
sudo chmod -R 775 /var/www/jodtod/storage /var/www/jodtod/bootstrap/cache
```

### Step 7: Nginx Configuration

Create `/etc/nginx/sites-available/jodtod`:
```nginx
server {
    listen 80;
    server_name jodtod.com www.jodtod.com;
    root /var/www/jodtod/public;

    index index.php;

    charset utf-8;
    client_max_body_size 10M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/jodtod /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl restart nginx

# Install SSL
sudo certbot --nginx -d jodtod.com -d www.jodtod.com
```

### Step 8: Queue Worker (Supervisor)

Create `/etc/supervisor/conf.d/jodtod-worker.conf`:
```ini
[program:jodtod-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/jodtod/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/jodtod/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start jodtod-worker:*
```

### Step 9: Cron Job (Scheduled Tasks)

```bash
sudo crontab -e -u www-data
```
Add:
```
* * * * * cd /var/www/jodtod && php artisan schedule:run >> /dev/null 2>&1
```

### Step 10: Firebase Service Account (for Push Notifications)

```bash
# Copy service account file to server
scp storage/app/firebase-service-account.json user@server:/var/www/jodtod/storage/app/

# Make sure .env has:
# FCM_SERVICE_ACCOUNT_PATH is auto-detected from storage/app/firebase-service-account.json
```

---

## Mobile App Deployment

### Step 1: Update API URL

In `D:\JodTodApp\.env`:
```env
EXPO_PUBLIC_API_URL=https://jodtod.com
```
(Change from local IP to production domain)

### Step 2: Update app.json for Production

In `D:\JodTodApp\app.json`, verify:
```json
{
  "expo": {
    "name": "JodTod",
    "version": "1.0.0",
    "android": {
      "package": "com.jodtod.app",
      "versionCode": 1
    },
    "ios": {
      "bundleIdentifier": "com.jodtod.app",
      "buildNumber": "1"
    }
  }
}
```

### Step 3: Create eas.json (Build Profiles)

Create `D:\JodTodApp\eas.json`:
```json
{
  "cli": {
    "version": ">= 15.0.0"
  },
  "build": {
    "development": {
      "developmentClient": true,
      "distribution": "internal"
    },
    "preview": {
      "distribution": "internal",
      "android": {
        "buildType": "apk"
      }
    },
    "production": {
      "android": {
        "buildType": "app-bundle"
      },
      "ios": {
        "autoIncrement": true
      }
    }
  },
  "submit": {
    "production": {
      "android": {
        "serviceAccountKeyPath": "./docs/google-play-service-account.json",
        "track": "production"
      }
    }
  }
}
```

### Step 4: Build Android APK/AAB

```powershell
# Preview APK (for testing / direct install)
cd D:\JodTodApp
eas build --profile preview --platform android

# Production AAB (for Play Store)
eas build --profile production --platform android
```

Build happens on Expo cloud servers (~10-15 min). Download link provided when done.

### Step 5: Google Play Store Submission

#### One-time Setup:
1. Go to [play.google.com/console](https://play.google.com/console)
2. Pay $25 registration fee
3. Complete developer profile (name, address, etc.)
4. Create new app → "JodTod"

#### App Listing:
- **App name:** JodTod - Expense Tracker & Splitter
- **Short description:** Track expenses, split bills with friends, settle debts easily
- **Full description:** Write 200-300 words about features
- **Screenshots:** Need minimum 2 phone screenshots (take from app)
- **Feature graphic:** 1024x500 banner image
- **App icon:** 512x512 (already have)
- **Category:** Finance
- **Content rating:** Fill questionnaire (Everyone)
- **Privacy policy URL:** https://jodtod.com/privacy (already exists)

#### Upload & Release:
1. Go to **Production** → **Create new release**
2. Upload the `.aab` file (from EAS build)
3. Add release notes: "Initial release - Track expenses, split bills, manage groups"
4. **Review** → Submit for review
5. Google reviews in 1-7 days (first app takes longer)

### Step 6: iOS Build (Optional - Apple Developer Required)

```powershell
# Build for iOS
eas build --profile production --platform ios

# Submit to App Store
eas submit --platform ios
```

#### App Store Connect:
1. [appstoreconnect.apple.com](https://appstoreconnect.apple.com)
2. Create new app
3. Fill similar listing info as Play Store
4. Upload build via EAS submit
5. Apple reviews in 1-3 days

---

## Post-Deployment Checklist

### Server (After Deploy)
- [ ] SSL certificate working (https://)
- [ ] .env APP_DEBUG=false
- [ ] .env APP_ENV=production
- [ ] Database migrations run
- [ ] Storage link created
- [ ] Queue worker running (supervisor)
- [ ] Cron job running (scheduler)
- [ ] Firebase service account file uploaded
- [ ] Email sending working (test forgot password)
- [ ] File uploads working (test avatar upload)
- [ ] CORS configured if needed for mobile API

### Mobile (After Build)
- [ ] API URL points to production domain (https)
- [ ] Push notifications working with production server
- [ ] Login/Register working
- [ ] All features tested on production API
- [ ] App icon and splash screen correct
- [ ] Deep linking working (jodtod://join/CODE)

### Updates (Future Deployments)

#### Web Update:
```bash
cd /var/www/jodtod
git pull origin master
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo supervisorctl restart jodtod-worker:*
```

#### Mobile Update:
```powershell
# Update version in app.json (bump version + versionCode)
# Then build and submit
eas build --profile production --platform android
eas submit --platform android
```

### Monitoring
- **Server logs:** `/var/www/jodtod/storage/logs/laravel.log`
- **Queue worker logs:** `/var/www/jodtod/storage/logs/worker.log`
- **Nginx logs:** `/var/log/nginx/error.log`
- **Supervisor:** `sudo supervisorctl status`

---

## Temporarily Disabled Features (Enable When Ready)

These features are disabled for initial launch because they require an SMS provider (paid service). They can be enabled in ~5 minutes when you're ready.

### What's Disabled:
| Feature | Platform | Status |
|---------|----------|--------|
| OTP Login (phone login) | Web + Mobile | Disabled with "Coming Soon" badge |
| Phone Verification (profile) | Web + Mobile | Disabled with "Coming Soon" badge |
| Phone required for groups | Web | `phone.verified` middleware commented out |

### How to Enable (When SMS Provider Ready):

#### Step 1: Choose & Configure SMS Provider
Add to `.env`:
```env
# MSG91 (recommended for India, ~₹0.15/SMS)
MSG91_AUTH_KEY=your_auth_key
MSG91_SENDER_ID=JODTOD
MSG91_TEMPLATE_ID=your_template_id

# OR Twilio (international, ~₹0.50/SMS)
TWILIO_SID=your_sid
TWILIO_TOKEN=your_token
TWILIO_FROM=+1234567890
```

#### Step 2: Enable Phone Verification Middleware
File: `routes/web.php` (line ~102)
```php
// Change this:
Route::middleware(['auth', 'verified'/*, 'phone.verified'*/])->group(function () {

// To this:
Route::middleware(['auth', 'verified', 'phone.verified'])->group(function () {
```

#### Step 3: Enable OTP Login Tab (Web)
File: `resources/js/Pages/Auth/Login.vue`
- Find the disabled OTP button and restore the `@click="activeTab = 'otp'"` handler
- Remove `disabled` attribute and "Soon" badge

#### Step 4: Enable OTP Login Tab (Mobile)
File: `JodTodApp/app/(auth)/login.tsx`
- Restore the original tab code with both `email` and `otp` tabs clickable
- Remove the "SOON" badge View

#### Step 5: Enable Phone Section (Web Profile)
File: `resources/js/Pages/Profile/Partials/UpdateProfileInformationForm.vue`
- Remove "Coming Soon" badge from Phone Number heading
- Restore original description text
- Enable the "Add Phone Number" button (remove `disabled` + restore `@click`)

#### Step 6: Enable Phone Section (Mobile Profile)
File: `JodTodApp/app/(tabs)/profile/index.tsx`
- Add the phone verification OTP flow (sendOtp, verifyOtp handlers)
- Already implemented in a previous session, just needs to be re-added

#### Step 7: Restart & Test
```bash
# Rebuild frontend
npm run build

# Restart queue worker
sudo supervisorctl restart jodtod-worker:*

# Test OTP flow end-to-end
```

---

## Local Development Setup (For Reference)

When developing locally after production is live:

### .env (Local - Web)
```env
APP_URL=http://localhost:8000
DB_DATABASE=jodtod_local
```

### .env (Local - Mobile)
```env
EXPO_PUBLIC_API_URL=http://YOUR_WIFI_IP:8000
```

### Commands (Local)
```bash
# Web
php artisan serve --host=0.0.0.0 --port=8000
php artisan queue:work --tries=3

# Mobile (Expo Go for dev, build for push notification testing)
cd D:\JodTodApp
npx expo start

# Mobile build (for push notification testing)
cd D:\JodTodApp\android
.\gradlew assembleDebug
```
