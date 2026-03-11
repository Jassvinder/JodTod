# JodTod - Task Tracker

---

## PENDING

- [ ] **Task 5** - Dark mode: calendar icon not visible in date inputs across the app. Icon stays black in dark theme, should adapt to white.
- [ ] **Task 5** - Group expenses: add "From" and "To" labels on date filter inputs.
- [ ] **Task 5-A** - Group page: total expenses amount not showing anywhere. Member balances display NaN.
- [ ] **Task 5-A** - Group with expenses still shows "No expenses yet. Add first expense" — label should change when expenses exist.
- [ ] **Task 5-A** - Expense date field: add time picker so users can record what time the expense was made.
- [ ] Manual testing of all features (use `Docs/TEST.md`)
- [ ] Production deployment

---

## COMPLETED
## Completed Current Tasks

- [x] **Task 1** - Add OTP login functionality with perfect structure (2026-03-09)
- [x] **Task 2** - Fix profile form UI, 404 error page, phone/email verification (2026-03-09)
- [x] **Task 3** - Profile avatar (crop), phone verify for groups, email verify UI, add member by phone (2026-03-10)
- [x] **Task 4** - CMS system: TipTap rich text editor, admin page management, DB-driven public pages, blog editor upgrade (2026-03-10)

### Phase 1: Project Setup
- [x] Initialize Laravel project
- [x] Configure MySQL database connection
- [x] Install & configure Inertia.js + Vue.js
- [x] Install & configure Tailwind CSS
- [x] Setup folder structure (Blade views, Vue components)
- [x] Setup Git repository
- [x] Build landing page (hero, features, CTA) - basic version
- [x] Create reusable Blade components (header, footer, public layout)
- [x] Create reusable Vue components (AppLayout, GuestLayout, Header, Sidebar, BottomNav)
- [x] Dashboard page placeholder

### Phase 3: Personal Expenses
- [x] Create expenses migration & model
- [x] Create categories migration & seeder
- [x] Build "Add Expense" form (Vue component)
- [x] Build "Expense List" with filters & pagination
- [x] Implement "Edit Expense" functionality
- [x] Implement "Delete Expense" with confirmation
- [x] Build expense summary/charts (Chart.js)

### Phase 2: Authentication
- [x] Create users migration & model (with avatar, google_id, currency, language, notifications)
- [x] Setup Laravel Breeze (Vue/Inertia stack)
- [x] Build registration page (themed, Google OAuth button)
- [x] Build login page (themed, Google OAuth button)
- [x] Implement forgot password / reset password (themed)
- [x] Email verification setup (MustVerifyEmail enabled)
- [x] Google OAuth login (Socialite + GoogleController)
- [x] Auth middleware & route protection
- [x] Profile page (update info, change password, delete account)

### Phase 4: Group Management
- [x] Create groups migration & model
- [x] Create group_members migration & model
- [x] Build "Create Group" form
- [x] Implement invite code generation (auto-generate 8-char code, refresh option)
- [x] Build "Join Group" via code/link
- [x] Build group list page
- [x] Build group detail page (members, invite, actions)
- [x] Implement member management (add/remove/leave)
- [x] Group settings (edit/delete - admin only)

### Phase 5: Group Expenses
- [x] Create expense_splits migration & model
- [x] Build "Add Group Expense" form with split options (equal/custom/percentage)
- [x] Implement Equal Split logic (with rounding)
- [x] Implement Custom Split logic (amount validation)
- [x] Implement Percentage Split logic (100% validation)
- [x] Build group expense list with filters & pagination
- [x] Show member-wise balance in group
- [x] Edit/Delete group expense (creator or admin)

### Phase 6: Settlement System
- [x] Create settlements migration & model
- [x] Implement balance calculation logic (total_paid - total_share per member)
- [x] Implement minimum transactions optimization algorithm (greedy min-transfers)
- [x] Build settlement summary screen (balance cards + suggested transactions)
- [x] Implement "Settle Individual" transaction (mark as paid)
- [x] Implement "Settle All" functionality (admin only, bulk complete)
- [x] Build settlement history page (status badges, pagination)
- [x] Mark expenses as settled after all settlements completed

### Phase 7: Dashboard
- [x] Build dashboard layout (Inertia + Vue)
- [x] Personal expense summary widget (this month/last month + category breakdown)
- [x] Groups overview with balances widget
- [x] Recent activity feed (mixed: personal, group, settlements)
- [x] Quick add expense button
- [x] Pending settlements indicator (amber alert with Pay Now links)

### Phase 8: Admin Dashboard & Role System
- [x] Add `role` column to users table (migration: enum admin/user)
- [x] Update User model (role field, isAppAdmin helper)
- [x] Create admin middleware (EnsureUserIsAdmin)
- [x] Role-based redirect after login (admin→/admin/dashboard)
- [x] Create AdminLayout (Vue - dark slate sidebar)
- [x] Build admin dashboard overview (stats cards + recent users)
- [x] Build user management (list, search, filter, change role, delete)
- [x] Build category management (CRUD with expense count protection)
- [x] Create UserSeeder (1 admin + 4 users with phones)

### Phase 9: SEO & Landing Pages
- [x] Landing page already built (hero, features, CTA) - Phase 1
- [x] Build features page (6 feature cards with details)
- [x] Build about, contact, privacy, terms pages
- [x] Build blog system (admin CRUD via Inertia + public Blade pages)
- [x] Build free tool: Expense Splitter Calculator (Alpine.js)
- [x] Setup sitemap.xml auto-generation
- [x] Add meta tags, OG tags, Twitter Cards to public layout
- [x] Add JSON-LD schema markup support (BlogPosting on blog posts)

### Phase 10: Notifications
- [x] Setup Laravel Notifications (notifications table migration)
- [x] In-app notification system (bell icon with count badge, dropdown, full page)
- [x] Email notification templates (4 notification types with mail support)
- [x] Notification preferences (toggle email/push on/off in profile)
- [x] Notification triggers: group expense added, added to group, settlement requested, settlement completed

### Phase 11: PWA & Polish
- [x] Complete PWA setup (manifest.json, icons 72-512px, apple-touch-icon, meta tags)
- [x] Service worker with caching (Network First for pages/API, Cache First for static assets)
- [x] Offline fallback page (/offline) with retry button
- [x] Offline indicator banner in AppLayout (amber bar when connection lost)
- [x] Service worker registration in app.js with hourly update check
- [x] Dark mode support (toggle in header, localStorage persistence, system preference detection)
- [x] Dark mode applied to: AppLayout, Header, Sidebar, BottomNav, Dropdown, DropdownLink, flash messages, form inputs
- [x] PWA meta tags in both app.blade.php (Inertia) and public.blade.php (Blade SEO)
- [x] PWA shortcuts: Add Expense, My Groups
