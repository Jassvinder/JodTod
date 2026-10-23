# JodTod - Task Tracker JV

**Task Movement Rules**

- When a task is completed, move it to the Completed section.
- Mark completed tasks with `[X]`.
- Add completion date in `DD-MM-YYYY` format.
- Keep completed tasks in descending task number order.
- Example: `- [X] Task title (21-05-2026)`
- If a task is completed from Current Tasks, add a new empty task with the next number in Current Tasks.
- If you're asked to check a screenshot for any task, look for it in the following directory:
  D:\Development\Projects\JodTod\Docs\Screenshots

## Current Tasks

- [] **510**
  A. jab user ko group me add kiya to push notification nahi aaya in-app aaya only.
  B. group expense add par task assing par chal raha hai. to group me member add par deactivate, remove par aise sabhi actions par push notification aani chahiye.

- [] **512**
  home page ke content me tasks system bhi is project me manage hota hai ye dalna hai.

- [] **514**

- [] **516** - Cloudflare tunnel `JodTod-Local`: remove the stale "Nidhi" connector (`736adb45-14c2-426c-aab9-358459548773`) that receives `jodtod.co.in` traffic and returns 502, so the worker shows the Coming Soon page. Rotate the tunnel token afterwards (it was printed in a local session log) and reinstall the service on TeqTop with the new token.

- [] **526** - Hostinger live setup (no SSH): (1) hPanel → Advanced → Cron Jobs, every minute: `/usr/bin/php /home/<user>/domains/<subdomain>/public_html/artisan schedule:run` (runs task reminders, weekly summary and the queue); (2) live `.env`: `MAIL_MAILER=smtp` with the Hostinger mailbox settings, and `SETUP_KEY=<long random string>`; (3) deploy, then open `/set-up-backend?key=<SETUP_KEY>` once to run migrations (inserts the default expense categories and creates the storage link; check the printed output lists `insert_default_expense_categories`); (4) send a test email (password reset) and a contact request to confirm emails and push notifications arrive.

- [] **519** - Install dev dependencies (`composer install`) so `php artisan test` works, then run `tests/Feature/Api/V1/RememberItemTest.php` (written but never run).

**Admin** Section tasks. starts from 500 =======================================

## PENDING

- [ ] **529** - After deploying the Support JodTod page: in Admin > Settings set the UPI ID and payee name and upload the UPI QR image, then open `/support` on web and the Support JodTod screen in the mobile app to check them. If the live database has a published CMS page with slug `terms`, it replaces the Blade Terms page, so add the new "Pricing & Payments" text there too.

### Web Pending (built in the mobile app first)

Features and fixes shipped in the mobile app that the web app does not have yet. Add every new mobile-only feature here.

- [ ] **528** - PWA icons in `public/icons/` (used by `manifest.json`) show an old, different logo, not the current JodTod mark. Regenerate all sizes from `favicon.png` (needs a decision on background/padding for maskable icons).

- [ ] **527** - Test the 12-10-2026 web parity work on the live site after deploy: welcome slides on first login, tip bubbles and the ? button on Dashboard/Expenses/Income/Groups/Tasks/Contacts/Remember, Help & Tips actions, Remember (add item, add/delete date, edit, delete), contact search Requested/Respond, Requests Sent + Cancel, Join Group by code, friend code copy on Profile and Contacts, empty states on each list page.

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

- [X] **Support JodTod page and free-for-now note** (14-10-2026)
  Users now see that JodTod is free for now and may add an optional donation or subscription later (feature names are not listed; "some features will always stay free"). New `/support` page (sidebar "Support JodTod") with the note, the UPI QR (Save QR Code), the UPI ID (Copy) and a "Pay with UPI App" deep link on phones. The UPI ID, payee name and QR image are set in Admin > Settings. Same content on the landing page (note only), in Terms section 5 and through `GET /api/v1/support` for the mobile app.

- [x] **Icon-only logo in collapsed sidebars** (12-10-2026)
  The collapsed web and admin sidebars squeezed the full wordmark, which was cut off. New `public/images/Logo/logo-icon.webp` (128px, made from `favicon.png`) and a shared `BrandLogo` component (`iconOnly` prop) used by Sidebar, AdminLayout, Header and GuestLayout. Also removed the Blade-style `?v={{ time() }}` that was sent literally in logo URLs, and fixed the collapse toggle size conflict that overlapped the logo.

- [x] **Web parity with mobile: Tasks 517, 518, 520, 521, 522, 523, 524, 525** (12-10-2026)
  Remember web page (list + detail side by side on wide screens), contact search with Requested/Respond and friend codes, Requests page with Received and Sent (Cancel), friend code on Profile and Contacts, no-contacts notes in Assign To and Add Member, readable friend code badges, dashboard card headers as links plus a Remember widget, first-login welcome slides, per-page tip bubbles with a ? button, Help & Tips page, and onboarding empty states on all list pages. Also added Join Group by invite code on the Groups page (web had only invite links).

- [x] **Web fixes found during the parity audit** (12-10-2026)
  `info` flash messages were never shown (e.g. "already a member"); dashboard "Pay Now" linked to group 0 because `pendingSettlements` had no `group_id` (also fixes the mobile type); settlement rows in Recent Activity showed an empty "→"; contact search placeholder promised email/phone search that does not exist; contact remove button was hidden until hover, so touch screens could not reach it.

- [x] **Default expense categories on fresh production databases** (12-10-2026)
  Live deploys run only `migrate`, so the `categories` table stayed empty and the app could not add expenses. New migration `2026_10_12_000000_insert_default_expense_categories` calls `Category::ensureDefaults()` (from `config('site.categories')`), and `GET /api/v1/categories` creates the defaults if the table is ever empty. `/set-up-backend` now also creates the storage link when missing and prints the migrate output.

- [x] **Run the queue from the scheduler and lock /set-up-backend** (12-10-2026)
  `routes/console.php` now runs `queue:work --stop-when-empty --max-time=50` every minute, so one Hostinger cron sends all queued emails and notifications. `/set-up-backend` returns 404 unless `?key=` matches `SETUP_KEY` (`config('app.setup_key')`); with no key set the route is disabled.

- [x] **Contact search shows pending requests; sent requests endpoint** (12-10-2026)
  Mobile API search now keeps users with a pending request (with `request_status`), matches exact friend codes, and a new `GET /contacts/requests/sent` lists outgoing requests. Web contact search is unchanged.

- [x] **Remember items API for the mobile app** (11-10-2026)
  New `remember_items` and `remember_entries` tables with a `/api/v1/remember-items` CRUD API, entry add/delete, last date and average gap per item, and `rememberStats.total` on the dashboard. No web UI yet.

- [x] **Highlight the Tasks module across public pages** (09-10-2026)
  Home, Features, About, Privacy, Terms, Blog, SEO defaults, footer, auth tagline, mail footer, and PWA manifest now describe tasks, reminders, task assignment, and event planning alongside expenses. Also fixed home feature icon backgrounds that used dynamic Tailwind classes.

- [x] **Fix fresh-install migration order for expenses.group_id FK** (09-10-2026)
  The expenses migration referenced `groups` before the groups table existed, failing on InnoDB (errno 150). The FK now lives in `2026_03_09_132057_add_group_foreign_key_to_expenses_table`, which skips databases that already have it.

- [x] **Task 515 — Second round of mobile/web parity fixes** (18-09-2026)
  Follow-up audit of Expense/Income/Settlement/Notification/Group controller pairs (backend logic only, not UI styling). Fixed the items that were clearly bugs; left the ones that looked like intentional platform differences alone:

  A. **Settlement race condition (fixed)** — web's `settle()` already checked for existing pending settlements inside a `DB::transaction()` with `lockForUpdate()`; the mobile API's version did the same check as a plain `count()` outside any lock, so two concurrent "Settle Up" requests could both pass and create duplicate settlement batches. Mobile now matches web's locked pattern.

  B. **Todo edit permission (aligned to mobile)** — mobile API already let a task's assignee edit it (`authorizeTodoOrAssignee`), not just the owner; web only allowed the owner. Aligned web's `update()` and the Edit button visibility in `Todos/Index.vue` to match (Delete stays owner-only on both).

  C. **Expense image WebP conversion (aligned to web)** — web's `ExpenseController`/`GroupExpenseController` each had their own copy of `storeImageAsWebp()` (duplicated); the mobile API versions of both stored the raw upload with no conversion. Extracted the shared logic into a `StoresWebpImages` trait and applied it to all four controllers.

  D. **Missing trend data on mobile (added)** — web's Expense summary includes `daily_trend` and Income summary includes `monthly_trend` (6-month) + `source_breakdown`; mobile's API versions were missing all three. Mirrored web's exact computation on both mobile endpoints.

  E. **Notifications "recent" list (left alone)** — web returns unread-only ("for header dropdown", per its own docblock); mobile returns latest 10 regardless of read status (feeds the Home dashboard's activity feed). Different UI contexts, both look intentional — not changed.

  F. **Dead `GroupController::searchUsers` (removed)** — phone-based member search, confirmed unreachable from any current Vue/Blade file. Web's own Task 103-A documented replacing this with contacts-based member addition, so this was leftover from before that migration.

  G. **`GroupController::refreshInviteCode` (left alone, flagged)** — also currently unused, but with no "replaced by X" evidence like searchUsers had. Reads more like an unfinished feature than confirmed dead code, so left as-is rather than guessing.

  Smoke-tested A–D via tinker fixtures (race-condition check, WebP output, trend data presence). `npm run build` clean throughout.

- [x] **Task 513 — Mobile/web parity gaps found while auditing both TASKS.md files** (18-09-2026)
  Mobile app (`JodTodApp`) had pulled ahead of web on a few things. Found by reading both repos' actual controller code, not just the task logs, then fixed:

  A. Default Todo categories ("Home"/"Market") never got created for web-only users — the seeding logic lived only in the mobile API's `TodoCategoryController::index()`. Extracted it onto `TodoCategory::ensureDefaultsForUser()` and call it from both the API and web `TodoController::index()`.

  B. Web let you delete the default Todo categories with no protection, unlike the API. Web's `TodoCategoryController::destroy()` now blocks it the same way.

  C. Web's Todo category filter was missing categories from tasks assigned to you by someone else (only showed your own categories). Web's `TodoController::index()` now merges both, matching the API.

  D. Web was more permissive than mobile on who can edit/delete a group expense — it allowed the expense creator too, not just the admin, inconsistent with mobile's deliberate Task 1204 policy ("only the Group Admin can use them; other members see an admin-only alert"). Aligned web's `GroupExpenseController` to admin-only, and Edit/Delete buttons on `Groups/Expenses/Index.vue` now show an "Admin Only" alert for non-admins instead of hitting a 403 page.

  Smoke-tested all four via tinker fixtures: default-category seeding, delete protection, and both the blocked non-admin and allowed-admin paths for group expense edit.

- [x] **Task 602** (03-08-2026)
      A. Removed the Push Notifications option from web and mobile Profile notification settings.
      B. Renamed the remaining preference to “Email Notifications (Weekly Summary & Settlements)” and aligned its web helper copy with the retained email types.
      C. Fixed mobile email-verification logout: it now clears the access token, in-memory user state, and registered push token before redirecting to Login.
      D. Added Change Email on the same screen. It uses the identical cleanup flow and redirects to Register so a different email can be used.
      E. Updated the root authentication guard so an unauthenticated app restart cannot remain on the verification screen.

- [x] **Task 601** (03-08-2026)
      A. Added one reusable, responsive, email-client-compatible branded layout for registration verification, password reset, weekly summary, and settlement emails.
      B. Reworked the retained email copy, CTA instructions, branding, spacing, typography, and professional closing while preserving dynamic URLs, tokens, and business logic.
      C. Removed email delivery and obsolete mail builders for group, group expense, group join request, todo assignment, and todo reminder notifications. Those events continue to use database and Expo push notifications.
      D. Verified notification PHP syntax, compiled Blade views, and updated password reset coverage for the queued branded notification.

- [x] **Task 600** (23-07-2026)
      A. Fixed the mobile Todo Categories API query to use the schema-defined `todos.todo_category_id` column instead of the nonexistent `todos.category_id` column. This resolves the Manage Categories SQL error.
      B. Aligned the mobile Todo create/edit request contract and response type with the backend's `todo_category_id` field, so selected categories persist and load correctly.
      C. Added API regression tests covering own and assigned-task categories plus Todo creation with a selected category. `php artisan test --filter=TodoCategoryTest` passes with 2 tests and 8 assertions.
      D. Updated the database schema and API endpoint documentation. The web Todo Category UI was not changed.

- [x] **511**
      npm run build chalane par kuchh warning type show ho raha hai ise check karo ke ye kyu ho raha hai. message niche hai jo show ho raha hai.
      "(!) Some chunks are larger than 500 kB after minification. Consider:
- Using dynamic import() to code-split the application
- Use build.rollupOptions.output.manualChunks to improve chunking: https://rollupjs.org/configuration-options/#output-manualchunks
- Adjust chunk size limit for this warning via build.chunkSizeWarningLimit.
  ✓ built in 13.28s" (2026-05-18)
- [x] **Task 509** - Profile page dark mode fix: Added dark: classes to all sections - Profile Info header/subtitle, avatar ring/buttons, Phone Number (border, headings, input, verified badge, OTP debug), Notification Preferences (border, headings, labels), Email Verification (border, heading, email display). (2026-03-23)
- [x] **Task 508** - (A) Tick icon added to "All Settled" badge on groups listing. (B) Settlement page: Member Shares hides after all settled (reverted). (C) Group detail page: new "Member Shares" section between Members and Expenses - purple themed, shows all-time total share per member, collapsible. (D) "Settle Up" → "Settlement" button text. (E) All 4 settlement page sections (Balance Overview, Member Shares, Suggested Settlements, Settlement History) made collapsible with smooth CSS grid transitions. (2026-03-21)
- [x] **Task 507** - (A) Member Shares section now always visible even after all settled. (B) Added stats summary bar (Expenses count, Total Spent highlighted, Members count) on group detail page. (C) "All Settled" green badge on groups listing when all expenses settled. (D) Members and Pending Requests sections collapsible with smooth CSS grid transition + animated chevron. (E) Group expense list: Edit/Delete text replaced with pencil/trash icons, expand/collapse uses Vue Transition with smooth max-height animation. (2026-03-21)
- [x] **Task 506** - (A) Description field moved after Category in group expense Create and Edit forms. New order: Amount → Category → Description → Date → Paid By. (B) Split Between section made collapsible with animated arrow, default closed. Shows current split type label. Applied to both Create and Edit. (C) Custom/Percentage split explained to user. (2026-03-20)
- [x] **Task 505** - (A) Added cursor-pointer to group expense Create/Edit save buttons. (B) Fixed missing notifications: added RemovedFromGroup and JoinRequestRejected notification classes. Added notifications to removeMember, rejectMember in both web and API controllers. Root cause of notifications not arriving: QUEUE_CONNECTION=database requires queue:work to be running. (2026-03-20)
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
