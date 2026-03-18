# JodTod - Task Tracker

- [] **Task 101** - Favicon kabhi aa jata hai kabhi nahi. or kabhi admin pannel par aata hai. to kabhi login/register and landing page par nahi aata. isse achhe se fix karo. bahut chhota and silly issue hai?

- [] **Task 102 Expenses** - Expense and update par date time select me select kiya gya time, list me galat show ho raha hai. 

- [] **Task 103 toDo** - Isme main soch raha tha ke task/todo reminder bhi add karne ka option ho. jaise kisi perticular day me mujhe kitne tasks complete karne hain. subha app me alarm baje or us din ke sabhi tasks list me show ho jaye. or jab alarm set kare to usme time ka option bhi ho. default chahe kisi perticular time pa set ho?

- [] **Task 103-A toDo** - Todo list ke filters me starting me ek "All" ka option bhi add karo. basically wo todos ke main link ko perafar karega. means all todos dikhayega.

- [] **Task 103-B toDo** - Agar hum tasks me category bhi add kare to kaisa rahega. Unche level par socho jara kisi marriage ke liye ye app use ho, jisme bahut saare tasks add ho, or wo bhi category wise. or dusra koi bahut saare tasks bnaye for example 100 tasks bnaye, shayad ek indian marriage me itne tasks hona to asan baat hai. or saare tasks category wise ho. ab tasks bnane wala wo tasks dusro ko assign karde isi app ke through. dusre bhi app user hone chahiye. jaise ki 9 logo ko 10-10 tasks or 10 chahe khud ke liye bhi rakhle. ok or sab ko track bhi kar sake. 

- [] **Task 104-A Group** - Group ka invite code baar-baar change kyu hota hai bas group bante time ek he baar code banna chahiye. agar maine 2 alag users ko invite bheja, or galti se dono baar code change ho gya to, bas last wala he join kar payega. sochna padega iske baare me.

## PENDING

### High Priority (Core functionality gaps)
- [ ] **Task 6** - Profile page: Add Currency & Language settings UI (DB fields exist: `currency`, `language` — just need form in Profile/Edit.vue)
- [ ] **Task 7** - Group leave: Fix leave logic — currently nobody can leave. Should allow leave only if balance is zero; block with message if unsettled balance exists
- [ ] **Task 8 - A** - Admin Reports page (`/admin/reports`): Expense trends, category breakdown, top groups, settlement stats
- [ ] **Task 8 - B** - User Expenses (individual and group both "optional") add karte time image bhi upload kar sake max 2 images jaise ki bill image ya samaan ki image.
- [ ] **Task 8 - C** - make sidebar collapsable.

### Medium Priority (Planned features)
- [ ] **Task 9** - Admin: Ban/Unban user functionality (add `banned_at` column, block login for banned users, UI in admin users list)
- [ ] **Task 10** - Admin Settings page (`/admin/settings`): Site name, default currency, maintenance mode toggle
- [ ] **Task 11** - Weekly summary notification: Scheduled command to email users their weekly expense summary

### Low Priority / Future
- [ ] **Task 12** - Real-time notifications: Polling interval in Header.vue (currently only fetches on bell-click, not auto-refresh)
- [ ] **Task 13** - Offline expense sync: Implement IndexedDB storage + background sync in service worker (currently empty placeholder)

### Release
- [ ] Manual testing of all features (use `Docs/TEST.md`)
- [ ] Production deployment

---

## COMPLETED
## Completed Current Tasks
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
