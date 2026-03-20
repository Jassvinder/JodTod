# JodTod - Task Tracker
- [] **0000**

**Admin** Section tasks. starts from 500 =======================================



## PENDING

### High Priority
(No pending high priority tasks)

### Low Priority / Future
- [ ] **Task 13** - Offline expense sync: Implement IndexedDB storage + background sync in service worker (currently empty placeholder)

### Release
- [ ] Manual testing of all features (use `Docs/TEST.md`)
- [ ] Production deployment

---

## COMPLETED
## Completed Current Tasks
- [x] **Task 504** - (A) Group expense image upload already implemented. (B) Member shares section added to settle up page - shows each member's total share from unsettled expenses with avatar, name, amount. Backend: calculateMemberShares() added to SettlementController. (C) Already done in Task 502 - only admin can settle up. (2026-03-19)
- [x] **Task 503** - (A) Expense image upload reduced to 1 image (hidden image_2 slot). WebP auto-conversion added to ExpenseController and GroupExpenseController via storeImageAsWebp() method. (B) Admin Pages listing: "Edit" text replaced with pencil icon. (2026-03-19)
- [x] **Task 500** - (A) Role dropdown removed from admin users listing. (B) Status column with clickable Active/Banned badges for ban/unban toggle. (C) Cursor pointer on all action buttons + Edit button with pencil icon added. (D) Admin user detail page: contacts listing with delete option. Edit page: name editable, email/phone disabled (verification required). (2026-03-19)
- [x] **Task 502** - Settle up fixes: (1) Duplicate prevention - if pending settlements exist, new ones won't be created. Button disabled with warning. (2) Only group admin can initiate settle up (backend + frontend enforced). (3) Confirmation dialog added. (4) Member deactivation system - members with unsettled expenses get deactivated (is_active=false) instead of removed. Deactivated members excluded from new expense splits but remain in settlement calculations. Admin can reactivate. Migration: added `is_active` to group_members. (2026-03-19)
- [x] **Task 501** - Admin Dashboard: Added phone number column to Recent Users table. Backend query updated to include phone field. Column hidden on mobile for responsiveness. (2026-03-19)
- [x] **Task 104** - Admin panel group detail page error: `Call to undefined relationship [user] on model [App\Models\User]`. Fixed eager loading `members.user` to `members` since BelongsToMany already returns User objects directly. (2026-03-19)
- [x] **Task 103-B** - Added "Group Expense" quick action button on Dashboard (single group → direct add expense, multiple → group listing). Added "Add Expense" button next to "View All" on Group Show page. All buttons responsive (icon-only on mobile, icon+text on desktop). (2026-03-19)
- [x] **Task 101 Expenses** - Expense add/update par date time select me select kiya gya time, list me galat show ho raha hai. Fixed timezone handling for correct time display. (2026-03-18)
- [x] **Task 102-A toDo** - Todo list ke filters me starting me ek "All" ka option add kiya. All todos dikhata hai. (2026-03-18)
- [x] **Task 102 toDo** - Todo reminder feature: reminder_at datetime field, bell icon UI in add/edit, alarm sound (Web Audio API), SweetAlert popup, browser notifications, multi-tab safe polling (60s), scheduled command for email notifications. (2026-03-18)
- [x] **Task 102-B toDo (Categories)** - User-specific todo categories with color picker (10 preset colors). Full CRUD management panel, category dropdown in add/edit forms, category filter buttons, color-coded category badges on tasks, category color bar on task cards. (2026-03-18)
- [x] **Task 102-B-ii toDo (Assignment)** - Todo assignment to contacts. Assign dropdown in add/edit forms, "Assigned to me" / "Assigned by me" scope filters, assignment badges on tasks, TodoAssigned notification (in-app + email). Assignee can toggle complete. (2026-03-18)
- [x] **Task Contacts** - Contacts system: search JodTod users by name/email/phone, add to contacts, remove. Contacts page in sidebar. Foundation for group member addition and todo assignment. (2026-03-18)
- [x] **Task 103-A Group (Contacts-based)** - Replaced invite code + phone search with contacts-based member addition. Admin sees contacts list in "Add Member" modal. Removed invite modal and join-by-code UI. Invite code column kept in DB. (2026-03-18)
- [x] **Task 6** - Profile page: Currency & Language dropdowns added (10 currencies, 2 languages). Validation in ProfileUpdateRequest. (2026-03-18)
- [x] **Task 7** - Group leave: Fixed leave logic. Members can leave if balance is zero. Blocks with unsettled balance message. Also fixed removeMember to check per-member balance instead of blocking all. (2026-03-18)
- [x] **Task 8-A** - Admin Reports page: Monthly expense trends (6-month bar chart), category breakdown (top 10), top groups table, settlement stats cards (total/completed/pending/amount). (2026-03-18)
- [x] **Task 8-C** - Sidebar collapsible: Already implemented with localStorage persistence, tooltip on collapsed state. (2026-03-18)
- [x] **Task 9** - Ban/Unban users: `banned_at` column, `isBanned()` helper, CheckBanned middleware (auto-logout + blocks login), ban/unban buttons in admin users list with confirmation, "Banned" badge. (2026-03-18)
- [x] **Task 10** - Admin Settings page: DB-backed key-value settings table with caching. Site name, default currency, maintenance mode toggle. Settings nav item in admin sidebar. (2026-03-18)
- [x] **Task 11** - Weekly summary notification: `summary:weekly` command scheduled Monday 8 AM. Emails users with: total expenses, transaction count, top category, income, pending tasks. Skips inactive users. (2026-03-18)
- [x] **Task 12** - Real-time notification polling: Header.vue auto-refreshes unread count every 60 seconds via `notifications.recent` endpoint. Updates badge and dropdown without page reload. (2026-03-18)
- [x] **Task 8-B** - Expense image upload: Max 2 images per expense (personal + group). Reusable ImageUpload.vue component. Dashed upload boxes with preview, hover-to-remove. 5MB max per image. Images stored in `storage/expenses/`. Edit supports add/remove/replace. Both controllers updated (ExpenseController + GroupExpenseController). (2026-03-18)
- [x] **Task 100** - check screenshot 11. profile image select karne ke baad jo crop ka popup aata hai wo sahi nahi hai. selected image he center me nahi aati. haan starting me aati thi. shayad kisi css se conflict na hua ho. or dusri baat agar user ne pehle koi image upload ki hai to use only delete karne ka option bhi hona chahiye, yaha sirf change ka option hai. (2026-03-17)

- [x] **Task 100-A** -image remove ke baad turand image section ko refresh kardo. abhi kya hai ke refresh ke baad he image remove ho rahi hai.(2026-03-17)
    or crop modal khali outer he set hua hai ander image abhi bhi utni he show ho rahi. screenshot 12 dekho full body image hai wo only head ka kuchh part he show ho raha hai. 
- [x] **Task 100-B** -pure project me jaha bhi confirmation hogi waha ek achha sa alert pupup lagao. for example shayad sweet alert hota tha ek main kabhi use kiya tha.(2026-03-17)
- [x] **Task 19** - Logo applied to all pages: Sidebar, Header (mobile only), GuestLayout, AdminLayout, Blade header/footer. Text "JodTod" removed where logo present (2026-03-16)
- [x] **Task 20** - Logout fix: `redirect('/')` changed to `Inertia::location('/')` to prevent Blade page rendering inside Inertia modal (2026-03-16)
- [x] **Task 14-A** - Favicon: Generated proper 32x32 ico + 48x48 png from logo.png (2026-03-16)
- [x] **Task 14-B** - Login button: Shows "Dashboard" for logged-in users, "Login" for guests on public pages (2026-03-16)
- [x] **Task 14-C** - Email templates: Published & branded Laravel mail templates - JodTod logo in header, indigo buttons (#6366f1), branded footer (2026-03-16)
- [x] **Task 14-D** - Registration email validation: `email` to `email:rfc,dns` (rejects invalid domains like admin@jodtod). VerifyEmail page Hindi title fixed to English (2026-03-16)
- [x] **Task 14-E** - Image uploads: Size limit 2MB to 5MB, all uploads auto-convert to .webp (80% quality). Applied to avatar upload + admin image upload (2026-03-16)
- [x] **Task 14** - To Do List (My Tasks): Full CRUD with inline add/edit, toggle complete, priority (low/medium/high), due date, overdue tracking, filters (2026-03-16)
- [x] **Task 15** - Income Tracking: Full CRUD, source autocomplete, monthly income/expense/savings summary, 6-month trend chart, source breakdown (2026-03-16)
- [x] **Task 16** - Expense Description Autocomplete: API endpoint returns distinct past descriptions, ExpenseForm shows suggestions on typing (2026-03-16)
- [x] **Task 17** - Dashboard Enhancement: Income/Savings/Loss cards, Income vs Expense 6-month bar chart, Tasks widget with pending/overdue count (2026-03-16)
- [x] **Task 18** - Settlement to Personal Expense: When group settlement is completed, auto-creates personal expense for the payer (2026-03-16)
- [x] **Task 5** - Dark mode: calendar icon color-scheme fix (`color-scheme: dark` in app.css) (2026-03-12)
- [x] **Task 5** - Group expenses: "From" and "To" labels added on date filter inputs (2026-03-12)
- [x] **Task 5-A** - Group page: total expenses amount displayed, member balances NaN fixed (explicit float cast + rounding) (2026-03-12)
- [x] **Task 5-A** - Group expenses: empty state label correctly conditional on `expenses.data.length === 0` (2026-03-12)
- [x] **Task 5-A** - Expense date field: `datetime-local` input added for date + time recording (2026-03-12)
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
