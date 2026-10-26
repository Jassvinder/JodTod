# JodTod - Development Progress

### 2026-10-26 - Team Builder Phase 1 API (COMPLETED, API only)
- New Team Builder mode for network marketers of any company. Migrations `2026_10_26_000000_add_app_mode_to_users_table` (`users.app_mode`) and `2026_10_26_000001_create_team_builder_tables` (`teams`, `team_members`, `prospects`, `prospect_activities`).
- Tree: every `team_members` row has its own `invite_code` and a materialized `path` of user ids, so joining with someone's code makes them the sponsor and `TeamMember::downlineOf()` finds the whole downline with one `LIKE 'path%'` query.
- `Api\V1\TeamBuilder\TeamController` (create, join, show with direct members and counts, rename, delete while alone, leave while nobody is under you) and `ProspectController` (prospect CRUD, activities that can also move the status and set or clear the next follow-up, follow-ups split into overdue/today/upcoming, weekly dashboard). `PUT /api/v1/user/app-mode` in `AuthController`. 17 routes (see `Docs/API_ENDPOINTS.md`).
- Verified locally: PHP lint, migrations, `route:list`, and an HTTP smoke test through the kernel with three temporary users (create, join, nested join under a member, already-a-member, show counts, leave/delete rules, prospect create, another user's prospect 403, plan shown auto-status, follow-up buckets, joined clears the follow-up, dashboard, wrong team 422). Test rows deleted afterwards.
- Next: Phase 2 (upline view of the whole downline with prospect names, statuses and follow-up dates, Push/Training flags, auto contacts) and Phase 3 (targets, team announcements, notifications). Tasks 538-541.

### 2026-10-23 - Task Details API, Guest Group Members, Lend & Borrow (COMPLETED, API only)
- **Tasks (530):** new `GET /api/v1/todos/{todo}` returns the task with `category`, `assignee` and `user` (creator); store/update/toggle responses now also include `user`. 403 messages are readable ("This task was assigned to you by someone else, so only they can delete it."). The assignee was always blocked from saving edits because the mobile app sends `assigned_to`; now only a real change of assignee is blocked.
- **Guest members (531):** migration `2026_10_23_000000_add_guest_fields_to_users_table` (nullable `email`, `is_guest`, `guest_created_by`). `GroupController::addGuestMember` creates the guest user and membership in one transaction and rejects a duplicate name in the same group. `AppServiceProvider` cancels every notification to a guest through `NotificationSending`. `User::registered()` scope used by contact search, admin dashboard counts/user list/monthly chart and `SendWeeklySummary`. Contact requests and task assignment reject guest ids (`exists:users,id,is_guest,0`). Settlements paid by a guest skip the "Settlement" personal expense (API and web controllers). Group show members include `is_guest`.
- **Lend & Borrow (532):** migration `2026_10_23_000001_create_ledger_tables`, models `LedgerPerson` (`withLedgerTotals()`, `toSummary()`, `toDetail()` with running `balance_after`) and `LedgerEntry`, `Api\V1\LedgerController` and 8 routes under `/api/v1/ledger-people` (see `Docs/API_ENDPOINTS.md`).
- Verified on the local database: PHP lint, `route:list`, migrations, and an HTTP smoke test through the kernel with Sanctum tokens (task show/edit/reassign/delete as assignee, guest add + duplicate, group expense paid by a guest and balances, ledger entries, totals and 403 for another user). The local tables are MyISAM, so the test's transaction did not roll back; the test rows were deleted by id afterwards (Task 536).
- Web app still lacks these features: Task 533.

### 2026-10-14 - Support JodTod Page and Free-for-Now Note (COMPLETED)
- Goal: tell users that running JodTod costs money, that every feature is free right now, and that an optional donation or subscription may come later depending on growth. The copy does not name features; it says some features will always stay free. Monetization will be decided later from real usage.
- Note text lives in `config('site.support')` (title, paragraphs, early users note, pay steps). `App\Services\SupportService::details()` adds the admin-set `support_upi_id`, `support_payee_name` and QR image (`support_qr_path` setting, stored on the public disk under `support/` with a new file name on each upload) and builds the `upi://pay` link (UPI ID left unencoded because some UPI apps do not decode `%40`).
- Admin > Settings: new Support JodTod (UPI) section with UPI ID (validated as `name@bank`), payee name, QR upload with preview and Remove QR. The form now posts multipart with `_method=put`.
- Web: `Pages/Support.vue` at `/support` (sidebar "Support JodTod", heart icon): note card, QR with Save QR Code (download), steps, UPI ID with Copy, and Pay with UPI App on phone widths. Payment card is hidden until a UPI ID or QR is set. Landing page shows the note; Terms section 5 now says pricing may change, some features always stay free, and donations are voluntary, unlock nothing and are non-refundable.
- Shared: `Utils/clipboard.js` (copy with fallback, now also used by `FriendCodeBadge`), `heart` and `download` icons in `Shared/Icon`.
- API: `GET /api/v1/support` for the mobile app (see `Docs/API_ENDPOINTS.md`).
- Verified: PHP lint, `route:list`, `vite build`, home and terms views render the new text, and a tinker run of QR upload, `upi_link` and QR removal.

### 2026-10-12 - Icon-Only Logo for Collapsed Sidebar (COMPLETED)
- Collapsed sidebars (user and admin) now show only the round JodTod mark (`images/Logo/logo-icon.webp`, generated from `favicon.png` with GD) through the new `Components/Shared/BrandLogo.vue`. All Vue layouts use it; logo URLs use a `VERSION` query instead of the literal `{{ time() }}` text. Verified collapsed and expanded states in Chrome.

### 2026-10-12 - Web Parity with the Mobile App (COMPLETED)
- **Remember:** new `RememberController` (Inertia) and `/remember` routes. `Pages/Remember/Index.vue` shows the list and the selected item side by side on wide screens and one at a time on phones; New/Edit Item in modals, Add Date inline, history with delete. Summary/detail logic moved into `RememberItem::withEntryStats()/toSummary()/toDetail()`, shared with the mobile API controller.
- **Contacts:** search logic moved to `Contact::searchUsersFor()` (used by web and API): name or exact friend code, pending users stay visible with `request_status`. Contacts page has a friend code card, Find Friends search with Add / Requested / Respond, and an onboarding empty state; desktop uses two columns. Requests page shows Received and Sent (Cancel). `friend_code` is now a shared Inertia prop and shows on Profile.
- **Onboarding:** `Utils/onboarding.js` (copy kept in sync with mobile `constants/tips.ts`, per-user state in localStorage key `jodtod_tips_v1_<userId>`), `WelcomeModal`, `TipsTour` (spotlight + bubble on `data-tip` anchors, skips hidden anchors, keyboard support) and `HelpButton`. New `/help` page; sidebar gained Remember and Help & Tips.
- **Shared components:** `Shared/Icon`, `EmptyState`, `FriendCodeBadge`, `NoContactsNote`, `Dashboard/SummaryCard`, `Utils/dates.js`. Empty states on Expenses, Income, Groups, Tasks, Group Expenses, Contacts, Notifications and Remember, with a separate "No ... match" state when filters are active.
- **Dashboard:** card headers link to their pages (Savings has no link), Tasks and Remember widgets, ? button.
- **Groups:** Join Group by invite code modal (web had no manual join).
- **Fixes:** `info` flash now displayed; `pendingSettlements.group_id` added; settlement activity text; contact search placeholder; always-visible remove button.
- Verified on a temporary SQLite database with `php artisan serve` in Chrome: welcome + tours, Remember create/add date, contact request flow, Requests approve, Join Group error, Tasks assign picker, Help page, phone width (390px) and dark mode; `vite build` passes; mobile API smoke-tested (contacts search, remember items + 403 for other users, dashboard). Intentional difference: on web the Contacts search box stays visible with no contacts, because it is the add-friend search (mobile has a separate Add Contact screen).

### 2026-10-12 - Empty Expense Categories on Production (COMPLETED)
- The mobile Add Expense screen showed no categories on the live server. Cause: Hostinger deploys only run `migrate` through `/set-up-backend`; `CategorySeeder` never ran, so the `categories` table was empty (settlement auto-expenses also lost their "Other" category).
- Added `Category::ensureDefaults()` (idempotent, by name, from `config('site.categories')`), used by `CategorySeeder`, a new migration `2026_10_12_000000_insert_default_expense_categories`, and `ApiV1CategoryController::index()` as a fallback when the table is empty.
- `/set-up-backend` now runs `storage:link` when `public/storage` is missing (receipt and photo URLs need it) and prints the migrate output.
- Not seeded on production on purpose: `UserSeeder` (test accounts with a shared password). Public pages fall back to Blade views, so `PageSeeder` is optional. To make an admin on live, set `users.role = 'admin'` for that account in phpMyAdmin.
- Verified on a temporary SQLite database: a fresh `migrate` creates all 9 categories, and the API recreates them after the table is emptied.

### 2026-10-12 - Queue on Shared Hosting and Setup Route Lock (COMPLETED)
- All notifications and auth emails implement `ShouldQueue` with `QUEUE_CONNECTION=database`, but Hostinger has no SSH or long-running worker, so nothing was being sent. The scheduler now runs `queue:work --stop-when-empty --max-time=50` every minute with `withoutOverlapping()`. A single hPanel cron for `schedule:run` covers reminders, the weekly summary and the queue (see Task 526).
- `/set-up-backend` was public, so anyone could trigger migrations. It now requires `?key=` matching `SETUP_KEY` (new `config('app.setup_key')`, documented in `.env.example`) and returns 404 otherwise or when the key is not set.
- Verified locally: `php artisan schedule:list` shows the queue job; the route returns 404 without or with a wrong key.

### 2026-10-12 - Contact Requests Visibility (COMPLETED)
- `Api\V1\ContactController::search` previously hid everyone with any contact row, so users you had already requested vanished from search. It now hides only yourself and approved contacts, and returns `request_status` (`sent`, `received`, or `null`) for the rest.
- Search also matches an exact friend code (case-insensitive) besides the name.
- New `GET /api/v1/contacts/requests/sent` lists your outgoing pending requests; the existing `DELETE /contacts/{id}` cancels one.
- Smoke-tested with curl against a temporary SQLite database. The web `ContactController` search is unchanged.

### 2026-10-11 - Remember Items API (COMPLETED)
- Added `remember_items` (owned by a user) and `remember_entries` (dated history per item) with the migration `2026_10_11_000000_create_remember_items_tables`.
- New `Api\V1\RememberItemController`: list, show, create (optional first entry date), update, delete, add entry, and delete entry. Each item returns `entries_count`, `last_done_on`, and `avg_gap_days`. Future dates are rejected and other users' items return 403.
- The dashboard now returns `rememberStats.total` for the mobile Summary card.
- Added `tests/Feature/Api/V1/RememberItemTest.php`. PHPUnit is not installed in `vendor/`, so the API was smoke-tested end to end with curl against a temporary SQLite database instead.
- Run `php artisan migrate` on the server when deploying. No web UI yet; the feature is mobile-only for now.

### 2026-10-09 - Tasks Module on Public Pages (COMPLETED)
- Updated site copy in `config/site.php` (tagline, hero, SEO title/description, footer) to present JodTod as an expense tracker, bill splitter, and task planner.
- Home: added Tasks & Reminders, Assign Tasks to Others, and Plan Big Events Early feature cards, plus a config-driven "Plan big events, months ahead" section with a sample event task list. "How it works" now covers tasks too.
- Features page: added three task-related feature cards and updated the title, meta description, hero, and CTA.
- About, Privacy (task data, reminders, sharing with assignees), Terms, and Blog meta now mention tasks. The PWA manifest gained a "My Tasks" shortcut; the auth layout tagline and mail footer were updated.
- Fixed home feature icon backgrounds: dynamic `bg-{color}-100` classes were never generated by Tailwind, so they now use a literal class map.

### 2026-10-09 - Production Migration Fix (COMPLETED)
- A fresh `php artisan migrate` failed on Hostinger (InnoDB) because the expenses migration created a foreign key to `groups` before that table existed. Local MySQL defaulted to MyISAM, which ignores foreign keys, so the issue never appeared locally.
- Moved the `expenses.group_id` foreign key into a new migration that runs right after the groups table is created and skips databases that already have the key.
- Verified a full fresh migration on a temporary InnoDB database; all expenses foreign keys are created.

### 2026-08-21 - Contact Privacy and Approval (COMPLETED)
- Contact search now returns only public name and avatar data, preventing email and phone disclosure through arbitrary searches.
- Contact additions create pending requests; recipients can approve or decline them, and approved contacts are available mutually.
- Group Add Member data now excludes unapproved contact requests.

### 2026-08-21 - Group Expense Admin Authorization (COMPLETED)
- Group-expense update and delete API actions now require the current user to be the group's admin, including expenses created by that member.
- Non-admin API attempts receive a consistent 403 response explaining that only the group admin can edit or delete group expenses.

---

## Current Status: ALL PHASES COMPLETE (1-11)

### 2026-08-19 - Task 1203 Todo Categories Support (COMPLETED)
- Added an `is_default` flag to Todo categories and protected default categories from API deletion.
- The Todo category endpoint now creates each authenticated user's Home and Market defaults on first load; existing Home and Market records are protected by the migration.
- Extended Todo category API coverage for default-category creation and deletion protection.

### 2026-08-03 - Task 602: Notification Preferences and Mobile Verification Flow (COMPLETED)
- Removed the Push Notifications profile option from both web and mobile interfaces and clarified the email preference label for weekly summaries and settlements.
- Fixed the mobile verification-screen logout and added Change Email. Both clear the current token, user state, and push registration before navigating to the intended authentication screen.
- Updated the mobile root guard so unauthenticated users are redirected to Login instead of remaining on the email verification screen after an app restart.

### 2026-08-03 - Task 601: Professional Email Notifications (COMPLETED)
- Created one reusable, responsive Blade email layout with JodTod branding, accessible CTA markup, and email-client-compatible table structure.
- Applied the shared layout to email verification, password reset, weekly summary, and settlement notifications without changing their dynamic URLs, tokens, or delivery logic.
- Removed email channels and mail builders from group, expense, and todo notifications; they continue to create database notifications and send Expo push notifications.
- Verified notification syntax and Blade compilation. Password reset coverage now checks the queued notification uses the shared template.

### 2026-07-27 - Income Mobile Edit API Fix (COMPLETED)
- Added the missing `GET /api/v1/incomes/{income}` route and `IncomeController::show` action.
- The endpoint enforces the same ownership authorization as update and delete, allowing the mobile edit form to retrieve only the authenticated user's selected income.

### What's Done:
- Planning complete (flowchart, schema, API, settlement logic, SEO strategy)
- Phase 1: Project setup complete (Laravel + Inertia + Vue + Tailwind + MySQL)
- Reusable component architecture setup (Blade + Vue)
- Phase 2: Authentication complete (login, register, Google OAuth, email verify, profile)
- Phase 3: Personal Expenses complete (categories, CRUD, filters, charts)
- Phase 4: Group Management complete (create, join, invite, members, edit, delete)
- OTP login functionality added (phone-based login with tab UI)
- Phase 10: Notification system complete (in-app + email, 4 trigger types, preferences)
- Phase 11: PWA & Polish complete (manifest, service worker, dark mode, offline support)
- Landing page, Dashboard placeholder, Git repo initialized

### What's Next:
- Testing & bug fixes (use TEST.md)
- Production deployment

### 2026-07-23 - Task 600 Mobile Todo Categories (COMPLETED)
- Root cause: the mobile Todo Categories API queried the nonexistent `todos.category_id` column even though the schema, migration, and `Todo` model use `todo_category_id`.
- Corrected the API query and aligned mobile Todo create/edit payloads and types to `todo_category_id`.
- Added API regression coverage for fetching owned and assigned-task categories and creating a Todo with a selected category. The web Todo Category UI remains intentionally unchanged.

### 2026-03-16 - Task 14 Variations: Favicon, Auth Fixes, Email Templates, Image WebP (COMPLETED)
- **Task 14-A - Favicon:**
  - Generated proper favicon from logo.png using GD (32x32 .ico + 48x48 .png)
  - Both layouts (app.blade.php + public.blade.php) already had favicon references
- **Task 14-B - Login Button for Logged-in Users:**
  - Added `@auth`/`@else` check in blade header component
  - Logged-in users see "Dashboard" button, guests see "Login"
  - Applied to both desktop nav and mobile menu
- **Task 14-C - Branded Email Templates:**
  - Published Laravel mail templates (`vendor:publish --tag=laravel-mail` + `--tag=laravel-notifications`)
  - Header: JodTod logo image instead of app name text
  - Footer: "JodTod. All rights reserved." + tagline
  - Button color: Black (#18181b) changed to Indigo (#6366f1) matching app theme
  - Panel accent: Indigo border
  - Applies to all emails: notifications (4 types), password reset, email verification
- **Task 14-D - Registration Email Validation & Verify Flow:**
  - Email validation upgraded: `email` to `email:rfc,dns` (now rejects invalid domains like `admin@jodtod`)
  - VerifyEmail.vue page title fixed: "Email Verify Karein" (Hindi) → "Verify Email" (English)
  - Verify-email flow is correct: logout button exists so user can re-register with correct email if needed
- **Task 14-E - Image Upload WebP Conversion:**
  - Avatar upload: size limit 2MB → 5MB, auto-converts to .webp (80% quality), path changed from `.jpg` to `.webp`
  - Admin CMS image upload: size limit 2MB → 5MB, auto-converts to .webp
  - Uses PHP GD `imagewebp()` for conversion
- **Other Fixes:**
  - Logo applied to all pages (Sidebar, Header mobile-only, GuestLayout, AdminLayout, Blade header/footer)
  - Removed duplicate text "JodTod" where logo image is present
  - Logout fix: `redirect('/')` → `Inertia::location('/')` to prevent Blade page rendering inside Inertia app
  - SMTP config fixed: MAIL_MAILER log→smtp, host→smtp.gmail.com, port→587, encryption→tls

### 2026-03-16 - New Features: Tasks, Income, Autocomplete, Dashboard Charts, Settlement-to-Expense (COMPLETED)
- **To Do List (My Tasks):**
  - Created `todos` table migration (user_id, title, priority enum, due_date, is_completed, completed_at)
  - Created Todo model with scopes (pending, completed, forUser)
  - Created TodoController: index (list with filters + stats), store, update, toggle, destroy
  - Created Todos/Index.vue: inline add form, edit mode, toggle checkbox, delete, priority/status filters, stats cards (total/pending/done/overdue), pagination
  - Added "My Tasks" to Sidebar navigation with clipboard-check icon
- **Income Tracking:**
  - Created `incomes` table migration (user_id, amount, source, description, income_date)
  - Created Income model with forUser scope
  - Created IncomeController: index (list + summary + 6-month trend + source breakdown), store, update, destroy
  - Created Incomes/Index.vue: add form with source autocomplete, edit inline, summary cards (income/expense/savings), 6-month bar chart (income vs expense), source breakdown bars, income history list with pagination
  - Added "Income" to Sidebar and BottomNav navigation
- **Expense Description Autocomplete:**
  - Added `suggestions()` method to ExpenseController: returns distinct descriptions matching query
  - Added GET /expenses/suggestions route
  - Updated ExpenseForm.vue: debounced API call on description input, dropdown with matching suggestions
- **Dashboard Enhancement:**
  - Added incomeSummary, monthlyTrend, todoStats to DashboardController
  - Dashboard now shows 5 summary cards: Expenses, Income, Savings/Loss, You Owe, Owed to You
  - Income vs Expense 6-month bar chart on dashboard
  - Tasks widget showing pending/overdue count with link to My Tasks page
- **Settlement to Personal Expense:**
  - When settlement is marked completed (individual or settle-all), auto-creates personal expense for the payer
  - Expense gets "Other" category, description includes group name and payee name
  - Added addSettlementToPersonalExpense() helper to SettlementController
- **Navigation Updates:**
  - Sidebar: Added "Income" (currency icon) and "My Tasks" (clipboard icon) nav items
  - BottomNav: Replaced "Alerts" with "Income" (notifications still accessible via sidebar and header bell)

### 2026-03-10 - Task 4: CMS System (COMPLETED)
- Created `pages` table migration (title, slug unique, content longText, meta_title, meta_description, is_published)
- Created Page model with published scope, boolean cast for is_published
- Created PageSeeder: seeds 5 pages (about, contact, privacy, terms, features) with HTML content
- Registered PageSeeder in DatabaseSeeder
- **Rich Text Editor (TipTap):**
  - Installed @tiptap/vue-3, @tiptap/starter-kit, @tiptap/extension-image, @tiptap/extension-link
  - Created RichTextEditor.vue component with toolbar (Bold, Italic, Strike, H2, H3, Bullet/Ordered List, Quote, Divider, Link, Image upload)
  - Image upload via admin endpoint (stores to public/uploads, max 2MB)
  - Full prose styling + dark mode support
- **Admin CMS Pages:**
  - Admin/PageController: index (list all), edit, update, uploadImage
  - Admin/Pages/Index.vue: table listing pages with title, slug, status badge, updated date, edit link
  - Admin/Pages/Edit.vue: page editor with title, RichTextEditor, SEO settings (meta_title, meta_description), publish toggle
  - Added "Pages" nav item to AdminLayout sidebar (both desktop + mobile) with page icon
  - Added admin routes: /admin/pages, /admin/pages/{page}/edit, /admin/pages/{page}, /admin/upload-image
- **Blog Editor Upgrade:**
  - Updated Blog Create.vue: replaced plain textarea with RichTextEditor component
  - Updated Blog Edit.vue: replaced plain textarea with RichTextEditor component
- **Public Pages - DB-driven:**
  - Updated PageController: loads page content from DB via `showPage()` helper
  - Falls back to static Blade view if page not found in DB (graceful degradation)
  - Created `cms.blade.php` generic Blade template: hero section with title + prose content rendering
  - 5 pages (about, contact, privacy, terms, features) now editable from admin panel

### 2026-03-10 - Phase 11: PWA & Polish (COMPLETED)
- **PWA Setup:**
  - Created `manifest.json` with app name, description, theme color (#6366f1), 8 icon sizes, shortcuts
  - Generated PWA icons (72x72 to 512x512) using PHP GD
  - Added PWA meta tags to both `app.blade.php` (Inertia) and `public.blade.php` (Blade)
  - Meta tags: theme-color, mobile-web-app-capable, apple-mobile-web-app-capable, apple-touch-icon
  - Manifest shortcuts: "Add Expense" → /expenses/create, "My Groups" → /groups
- **Service Worker:**
  - Created `service-worker.js` with dual caching strategy
  - Network First: HTML pages, Inertia requests, API calls (with dynamic cache)
  - Cache First: static assets (JS, CSS, images, fonts, /build/ files)
  - Pre-caches /offline page for offline navigation fallback
  - Auto-cleans old caches on activate, claims clients immediately
  - Background sync placeholder for future offline expense queueing
  - Push notification handler placeholder for future web push
  - Registered in `app.js` with hourly update check
- **Offline Support:**
  - Created `/offline` route and `offline.blade.php` (standalone HTML, no dependencies)
  - Offline page: branded icon, "You're Offline" message, "Try Again" button
  - Respects prefers-color-scheme for dark mode on offline page
  - AppLayout: offline indicator banner (amber bar) when connection lost, auto-hides when back online
  - Uses navigator.onLine + online/offline events for real-time detection
- **Dark Mode:**
  - Added `@custom-variant dark` to Tailwind CSS v4 config (class-based strategy)
  - Dark mode script in `<head>` prevents flash of wrong theme on load
  - Checks localStorage first, falls back to prefers-color-scheme
  - Header: sun/moon toggle button, updates localStorage + meta theme-color
  - Dark mode applied to all shared components:
    - AppLayout: bg-gray-900, dark flash messages
    - Header: bg-gray-800, dark borders, dark text, dark notification dropdown
    - Sidebar: bg-gray-800, dark borders, dark active states
    - BottomNav: bg-gray-800, dark borders, dark text
    - Dropdown: bg-gray-800, dark ring
    - DropdownLink: dark text and hover states
    - Form inputs: dark bg-gray-800, dark borders, dark text (via CSS)
    - Checkboxes: dark borders and background

### 2026-03-10 - Phase 10: Notifications (COMPLETED)
- Created `notifications` table migration (Laravel default polymorphic table)
- Created 4 Notification classes with database + mail channels:
  - `GroupExpenseAdded`: triggered when expense added to group (notifies all members except creator)
  - `AddedToGroup`: triggered when admin adds a member to group
  - `SettlementRequested`: triggered when settlement created (notifies debtor/from_user)
  - `SettlementCompleted`: triggered when settlement marked done (notifies creditor/to_user)
- Each notification respects `notification_email` user preference for email channel
- Created `NotificationController` with 5 methods:
  - `index`: paginated notification list (Inertia page)
  - `recent`: JSON endpoint for header dropdown (last 10 unread)
  - `markAsRead`: mark single notification as read
  - `markAllAsRead`: mark all notifications as read
  - `updatePreferences`: toggle email/push notification preferences
- Notifications/Index.vue: full notification list page with type-specific icons, relative timestamps, click-to-navigate, pagination
- Header.vue updated: bell icon with unread count badge (red), dropdown showing recent notifications, mark all read, link to full page
- Sidebar.vue updated: added Notifications nav item with unread count badge
- BottomNav.vue updated: added Alerts nav item with badge for mobile
- HandleInertiaRequests: shares `unread_notifications_count` prop globally
- Profile page: added Notification Preferences section (email/push toggle checkboxes)
- 5 notification routes under auth+verified middleware
- Triggers integrated into: GroupController (addMember), GroupExpenseController (store), SettlementController (settle, markCompleted)

### 2026-03-10 - Phase 9: SEO & Landing Pages (COMPLETED)
- Created blog_posts migration + BlogPost model (auto slug, published scope)
- Public Blade pages: Features, About, Contact, Privacy, Terms
- Blog system: public list/detail (Blade, SEO) + admin CRUD (Inertia/Vue)
- Free Expense Splitter tool (Alpine.js interactive, no login required)
- Sitemap controller: auto-generates /sitemap.xml with all pages + blog posts
- Public layout enhanced: OG tags, Twitter Cards, JSON-LD schema support
- Admin Blog pages: Index (list), Create, Edit with SEO fields
- AdminLayout updated with Blog nav item
- PageController for static pages, BlogController for public blog
- All routes added: /features, /about, /contact, /privacy, /terms, /blog, /tools/expense-splitter, /sitemap.xml

### 2026-03-10 - Phase 8: Admin Dashboard & Role System (COMPLETED)
- Added `role` enum column to users (admin/user, default user)
- User model: added isAppAdmin() helper, role to fillable
- Created EnsureUserIsAdmin middleware (admin alias)
- Role-based redirect: admin→/admin/dashboard, user→/dashboard (both email+OTP login)
- AdminLayout.vue: dark slate sidebar, admin nav, "Back to App" link
- AdminController: dashboard stats (total users/groups/expenses/amount, monthly, recent users)
- User management: paginated list with search/role filter, change role, delete user
- CategoryController (admin): CRUD with expense count check on delete
- UserSeeder updated: admin user gets role=admin, all test users get phone numbers
- 8 admin routes under /admin prefix with auth+verified+admin middleware
- Inertia shared props: role included in auth.user

### 2026-03-10 - Phase 7: Dashboard (COMPLETED)
- Created DashboardController with 4 data methods:
  - personalSummary: this month/last month totals + top 5 category breakdown
  - groupsSummary: per-group balance + total_you_owe / total_owed_to_you
  - recentActivity: last 10 mixed activities (personal, group expenses, settlements)
  - pendingSettlements: settlements where user needs to pay
- Rebuilt Dashboard.vue with real data widgets:
  - Summary cards: This Month, You Owe (red), You Are Owed (green)
  - Pending settlements alert (amber) with "Pay Now" links
  - Groups overview with balance badges
  - Category breakdown with CSS progress bars (no chart library)
  - Recent activity timeline with type-specific icons + relative dates
  - Quick action buttons: +Add Expense, New Group
- All empty states handled for new users
- Currency formatting with Intl.NumberFormat('en-IN')
- Replaced inline dashboard route closure with DashboardController

### 2026-03-10 - Phase 6: Settlement System (COMPLETED)
- Created `settlements` migration (group_id, from_user, to_user, amount, status, note, settled_at)
- Created Settlement model with fromUser(), toUser(), group() relationships
- Created SettlementController with 4 methods:
  - index: balance overview + optimized transaction suggestions + settlement history
  - settle: creates pending settlement records from greedy min-transfers algorithm
  - markCompleted: marks individual settlement done (from_user, to_user, or admin)
  - settleAll: admin-only bulk completion of all pending settlements
- Greedy min-transfers algorithm: separates creditors/debtors, matches largest pairs
- Auto-marks expenses + splits as settled when all settlements completed
- Vue Settlement Dashboard (Groups/Settlements/Index):
  - Balance overview cards (green/red/gray per member)
  - Suggested transactions with from→to arrows + amounts
  - Settlement history with status badges + "Mark as Paid" button
  - Admin "Settle All" button with confirmation modal
- "Settle Up" button added to Group Show page + Group Expenses Index page
- 4 settlement routes under phone.verified middleware

### 2026-03-10 - Phase 5: Group Expenses (COMPLETED)
- Created `expense_splits` migration (expense_id, user_id, share_amount, percentage, is_settled)
- Created ExpenseSplit model with relationships
- Updated Expense model: added splits(), group(), forGroup() scope
- Created GroupExpenseController with full CRUD + balance calculation:
  - index: list with filters (category, date, search), pagination, member balances
  - create/store: add expense with equal/custom/percentage split
  - edit/update: modify expense + re-create splits in transaction
  - destroy: soft delete (creator or admin only)
  - balances: JSON endpoint for member balance summary
- Equal split rounding: remainder goes to first member
- Split validation: amounts must sum to total, percentages must sum to 100
- Authorization: member check, creator-or-admin for edit/delete
- Vue Pages: Groups/Expenses/Index (list + balances + filters), Create (split UI), Edit
- Split selector UI: 3 tabs - Equal (checkboxes), Custom (amount inputs), Percentage (% inputs)
- Real-time validation on custom/percentage splits
- Group Show page updated with expenses section + mini balance summary
- Routes: 7 group expense routes under phone.verified middleware

### 2026-03-10 - Task 3: Profile Avatar, Phone/Email Verify, Group Access (COMPLETED)
- **Profile Avatar Upload with Crop:**
  - Installed cropperjs v2 (web components) + configured Vite isCustomElement for cropper-* tags
  - Avatar section at top of profile page: shows current avatar (or initials fallback)
  - "Change Photo" → file picker → crop modal (1:1 aspect ratio, 400x400px output, JPEG 0.8 quality)
  - Backend: ProfileController@updateAvatar accepts base64, saves to storage/avatars/{id}.jpg
  - Storage symlink created, avatars directory ready
  - Intervention/Image installed (available for future use)
- **Phone Verification Required for Groups:**
  - Created EnsurePhoneIsVerified middleware (phone.verified alias)
  - Group routes wrapped with phone.verified middleware
  - Unverified users redirected to profile page with flash error
  - Phone section description updated: "required to create or join groups"
- **Email Verification UI:**
  - Added dedicated Email Verification section to profile page (similar style to phone)
  - Shows email with Verified/Unverified badge
  - "Send Verification Email" button for unverified emails
  - Removed old inline email verification prompt (replaced with new section)
- **Group Members Avatar:**
  - Group Show page shows member avatar thumbnail (with initials fallback)
  - GroupController now loads phone + avatar for group members
- **Flash Messages:**
  - AppLayout now displays flash success/error messages (used by middleware redirect)
- **Inertia Shared Props:**
  - HandleInertiaRequests updated to explicitly share user fields + flash messages

---

## Progress Log

### 2026-03-07 - Project Planning
- Finalized project name: JodTod
- Decided tech stack: Laravel (Blade for SEO + Inertia+Vue for app) + MySQL
- Created complete app flowchart (user flow, auth, expenses, groups, settlement)
- Designed database schema (8 tables)
- Defined all API endpoints
- Documented settlement calculation algorithm with examples
- Created SEO strategy (blog, free tools, technical SEO)
- Setup Docs folder with all documentation files
- Created task tracker with 10 phases

### 2026-03-07 - Phase 1: Project Setup (COMPLETED)
- Laravel project initialized
- Installed & configured: Inertia.js, Vue.js, Tailwind CSS
- MySQL database 'jodtod' created & configured in .env
- Vite configured with Vue plugin + @ alias
- Inertia middleware registered
- Created folder structure:
  - Blade: layouts/public.blade.php, components/blade/ (header, footer)
  - Vue: Layouts/ (AppLayout, GuestLayout), Components/Shared/ (Header, Sidebar, BottomNav)
  - Pages: Dashboard.vue, pages/public/home.blade.php
- Landing page built (hero, features, how-it-works, CTA)
- Dashboard page placeholder with summary cards
- Routes configured: / (Blade), /dashboard (Inertia)
- Git repo initialized with initial commit
- Build tested successfully (npm run build)

### 2026-03-07 - Phase 2: Authentication (COMPLETED)
- Installed Laravel Breeze (Vue/Inertia stack) + Laravel Socialite
- Updated users migration: avatar, google_id, currency, language, notification_email, notification_push
- User model: MustVerifyEmail enabled
- Themed all auth pages (Indigo+Rose design):
  - Login: email/password + Google OAuth + "Remember me" + forgot password link
  - Register: name/email/password + Google OAuth
  - Forgot Password, Reset Password, Verify Email
- Google OAuth: GoogleController (redirect + callback), auto-creates/links users
- AppLayout: sidebar with active route highlighting (Ziggy), header with user dropdown + logout
- BottomNav: mobile navigation with active state
- Profile page: update info, change password, delete account (uses AppLayout)
- Removed Breeze defaults (Welcome.vue, AuthenticatedLayout.vue)
- Fixed Tailwind v4 compatibility (removed v3 postcss/tailwind configs)
- Fixed MySQL string length issue (Schema::defaultStringLength 191)
- .env: Google OAuth env vars added (GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET)

### 2026-03-09 - Task 1: OTP Login (COMPLETED)
- Added `phone` column to users table (migration)
- Created `otp_codes` table (phone, code, expires_at, used)
- Created OtpCode model with `isValid()` helper
- Created OtpController: send OTP (with rate limiting) + verify OTP (find/create user, auto-login)
- Updated Login.vue: two-tab UI ("Email & Password" / "OTP Login") matching screenshot design
- OTP flow: enter phone → send OTP → enter 6-digit code → verify & login
- Features: rate limiting (3 send / 5 verify per minute), OTP expires in 5 min, previous OTPs invalidated
- Dev mode: OTP shown in yellow debug box + logged to laravel.log
- OTP login only for existing users with verified phone (no auto-create)
- Updated User model: added `phone`, `phone_verified_at` to fillable
- Routes: POST otp/send, POST otp/verify (guest middleware)

### 2026-03-09 - Task 2: UI Fixes & Phone Verification (COMPLETED)
- Fixed TextInput.vue: `focus:border-indigo-500` → `focus:border-primary-500` (consistent theme)
- Fixed PrimaryButton.vue: `bg-gray-800` → `bg-primary-600` (matches app theme)
- Fixed hardcoded indigo references in profile form
- Created Error.vue: proper 404/403/500/503 error page (no more modal popup)
- Configured `bootstrap/app.php` Inertia error handler for proper error rendering
- Added `phone_verified_at` column to users table (migration)
- Created PhoneVerificationController: send OTP, verify & save phone, remove phone
- Redesigned profile phone section: separate from main form, OTP-verified flow
  - Add phone → Send OTP → Verify → Save (phone only saved after OTP verified)
  - Shows verified badge, change/remove options
- OTP login now requires `phone_verified_at` to be set
- Phone removed from ProfileUpdateRequest (handled separately via OTP verification)
- Routes: POST profile/phone/send-otp, POST profile/phone/verify, DELETE profile/phone

### 2026-03-09 - Phase 4: Group Management (COMPLETED)
- Created `groups` table (name, description, invite_code UNIQUE 8-char, created_by FK)
- Created `group_members` table (group_id, user_id, role enum admin/member, joined_at, UNIQUE constraint)
- Group model: auto-generates invite code on create, isAdmin/isMember helpers, relationships
- GroupMember model: belongs to group & user
- User model: added `groups()` BelongsToMany relationship
- GroupController: full CRUD + invite/join/leave/removeMember
  - Create: name + description, creator auto-becomes admin
  - Show: members list, invite modal (code + link + copy), admin actions
  - Edit/Delete: admin only
  - Join: via 8-char invite code or public link (/join/{code})
  - Leave: admin transfer to oldest member, last member deletes group
  - Remove member: admin only, cannot remove other admins
- Vue Pages: Groups/Index (list + join form), Groups/Create, Groups/Show (detail + modals), Groups/Edit, Groups/Join (public link landing)
- Sidebar & BottomNav: Groups link updated from fallback href to proper route
- Routes: resource groups, POST groups/join, POST groups/{id}/invite, POST groups/{id}/leave, DELETE groups/{id}/members/{userId}, GET /join/{inviteCode}
