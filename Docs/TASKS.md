# JodTod - Task Tracker

---

## PENDING

### Phase 1: Project Setup
- [ ] Configure PWA manifest & service worker (basic)


### Phase 3: Personal Expenses
- [ ] Create expenses migration & model
- [ ] Create categories migration & seeder
- [ ] Build "Add Expense" form (Vue component)
- [ ] Build "Expense List" with filters & pagination
- [ ] Implement "Edit Expense" functionality
- [ ] Implement "Delete Expense" with confirmation
- [ ] Build expense summary/charts (Chart.js)

### Phase 4: Group Management
- [ ] Create groups migration & model
- [ ] Create group_members migration & model
- [ ] Build "Create Group" form
- [ ] Implement invite code generation
- [ ] Build "Join Group" via code/link
- [ ] Build group list page
- [ ] Build group detail page
- [ ] Implement member management (add/remove/leave)
- [ ] Group settings (edit/delete - admin only)

### Phase 5: Group Expenses
- [ ] Create expense_splits migration & model
- [ ] Build "Add Group Expense" form with split options
- [ ] Implement Equal Split logic
- [ ] Implement Custom Split logic
- [ ] Implement Percentage Split logic
- [ ] Build group expense list with filters
- [ ] Show member-wise balance in group
- [ ] Edit/Delete group expense

### Phase 6: Settlement System
- [ ] Create settlements migration & model
- [ ] Implement balance calculation logic
- [ ] Implement minimum transactions optimization algorithm
- [ ] Build settlement summary screen
- [ ] Implement "Settle Individual" transaction
- [ ] Implement "Settle All" functionality
- [ ] Build settlement history page
- [ ] Mark expenses as settled after settlement

### Phase 7: Dashboard
- [ ] Build dashboard layout (Inertia + Vue)
- [ ] Personal expense summary widget
- [ ] Groups overview with balances widget
- [ ] Recent activity feed
- [ ] Quick add expense button
- [ ] Pending settlements indicator

### Phase 8: SEO & Landing Pages (Blade)
- [ ] Build landing page (hero, features, CTA)
- [ ] Build features page
- [ ] Build about, contact, privacy, terms pages
- [ ] Build blog system (CRUD for admin, list/detail for public)
- [ ] Build free tool: Expense Splitter Calculator
- [ ] Setup sitemap.xml auto-generation
- [ ] Add meta tags, OG tags, structured data
- [ ] Add schema markup

### Phase 9: Notifications
- [ ] Setup Laravel Notifications
- [ ] In-app notification system (bell icon, count)
- [ ] Email notification templates
- [ ] Notification preferences (toggle on/off)
- [ ] Notification triggers for all events

### Phase 10: PWA & Polish
- [ ] Complete PWA setup (icons, manifest, service worker)
- [ ] Offline caching strategy
- [ ] Background sync for offline data
- [ ] "Add to Home Screen" prompt
- [ ] Dark mode support
- [ ] Performance optimization
- [ ] Testing & bug fixes

---

## IN PROGRESS

### Phase 3: Personal Expenses (NEXT UP)

---

## COMPLETED

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
