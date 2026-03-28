# JodTod - Expense Tracker & Splitter

Dosto, roommates aur family ke saath kharcha split karo asaani se.

## Tech Stack
- **Backend:** Laravel (PHP) + MySQL
- **Frontend:** Blade (SEO pages) + Inertia.js + Vue.js (App pages)
- **Styling:** Tailwind CSS
- **PWA:** Responsive + Installable

---

## Quick Start

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env (see Database section below)

# Run migrations
php artisan migrate --seed

# Start development
npm run dev
php artisan serve
```

---

## Customization Guide

### Changing Colors (Theme)

All colors are defined in **one file**: `resources/css/app.css`

```css
@theme {
    /* Change these values to change the entire app theme */
    --color-primary-500: #6366f1;   /* Main brand color (Indigo) */
    --color-accent-500: #f43f5e;    /* Accent color (Rose) - also "dena hai" */
    --color-success-500: #10b981;   /* Success color (Green) - also "milna hai" */
}
```

**How it works:** The entire app uses `primary`, `accent`, and `success` color classes (e.g., `bg-primary-600`, `text-accent-500`). Changing the values in `app.css` will update colors everywhere automatically.

**Tip:** Use [Tailwind Color Generator](https://uicolors.app/create) to generate a full shade palette (50-900) from any single color.

---

### Changing Content (Text, Labels, Dummy Data)

All static content is in **one file**: `config/site.php`

| Section | What it controls |
|---------|-----------------|
| `site.app` | App name, tagline, description, currency |
| `site.landing.hero` | Landing page hero section text & buttons |
| `site.landing.features` | Feature cards (title, description, icon) |
| `site.landing.how_it_works` | "How it works" steps |
| `site.landing.cta` | Call-to-action section text |
| `site.nav.public` | Public page navigation links |
| `site.nav.app` | App navigation links (sidebar, bottom nav) |
| `site.footer` | Footer links and description |
| `site.seo` | Default SEO title, description, OG image |
| `site.categories` | Expense categories list |

**How to use in Blade:** `{{ config('site.app.name') }}`
**How to use in PHP:** `config('site.landing.hero.title_line1')`

---

### Changing Navigation Links

Edit `config/site.php` -> `nav.public` array for public pages, `nav.app` for app pages.

---

### Database Configuration

Edit `.env` file:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jodtod
DB_USERNAME=root
DB_PASSWORD=
```

---

## Project Structure

```
JodTod/
  config/
    site.php                  <-- All content & dummy data (CHANGE HERE)
  resources/
    css/
      app.css                 <-- All colors & theme (CHANGE HERE)
    js/
      Layouts/                <-- Reusable page layouts
        AppLayout.vue           (sidebar + header + bottom nav)
        GuestLayout.vue         (login/register pages)
      Components/
        Shared/               <-- Reusable UI components
          Header.vue
          Sidebar.vue
          BottomNav.vue
      Pages/                  <-- App pages (Inertia)
        Dashboard.vue
    views/
      app.blade.php           <-- Inertia root template
      layouts/
        public.blade.php      <-- Public pages layout (SEO)
      components/blade/
        header.blade.php      <-- Public header (nav)
        footer.blade.php      <-- Public footer
      pages/public/
        home.blade.php        <-- Landing page
  Docs/                       <-- Project documentation
    FLOWCHART.md
    INSTRUCTIONS.md
    TASKS.md
    PROGRESS.md
    DATABASE_SCHEMA.md
    API_ENDPOINTS.md
    SETTLEMENT_LOGIC.md
    SEO_STRATEGY.md
```

---

## Production Server Setup

### Cron Job (Required)

Laravel's task scheduler needs a cron entry on the server. This handles:
- **Todo reminders** — checks every minute for due reminders and sends notifications (in-app + email)
- **Weekly summary** — sends expense summary email to users every Monday at 8 AM

```bash
# Add this to server's crontab (crontab -e)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### Queue Worker (Recommended)

For faster email delivery, run a queue worker. Without it, emails are sent synchronously (slower page loads).

```bash
# Start queue worker (use supervisor in production)
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

### Key Environment Variables

```env
# Mail (required for email notifications)
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@jodtod.com
MAIL_FROM_NAME="JodTod"

# Queue driver (use 'database' or 'redis' in production)
QUEUE_CONNECTION=database
```

---

## Documentation

See `Docs/` folder for detailed documentation:
- **FLOWCHART.md** - Complete app flow
- **TASKS.md** - Development task tracker
- **DATABASE_SCHEMA.md** - Full database schema
- **SETTLEMENT_LOGIC.md** - Settlement calculation algorithm
