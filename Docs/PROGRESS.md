# JodTod - Development Progress

---

## Current Status: ALL PHASES COMPLETE (1-11)

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
