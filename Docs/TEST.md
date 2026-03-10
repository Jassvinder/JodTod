# JodTod - Manual Test Cases

> **How to use:** Test each case, mark with [x] if PASS, add comments if FAIL.
> Report issues in TASKS.md under "Current Tasks" or mention inline here.

---

## Pre-requisites

- [ ] Run `php artisan migrate` (all migrations pass)
- [ ] Run `php artisan db:seed --class=UserSeeder` (creates admin + 4 test users)
- [ ] Run `npm run build` (no build errors)
- [ ] App loads at `http://localhost/JodTod/public/` without errors

**Test Accounts:**
| Email | Password | Role | Phone |
|-------|----------|------|-------|
| admin@jodtod.com | password | admin | 9000000001 |
| rahul@example.com | password | user | 9000000002 |
| priya@example.com | password | user | 9000000003 |
| amit@example.com | password | user | 9000000004 |
| neha@example.com | password | user | 9000000005 |

---

## Phase 1: Project Setup

| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 1.1 | Landing page loads at `/` | [ ] | |
| 1.2 | Dashboard loads at `/dashboard` after login | [ ] | |
| 1.3 | Sidebar navigation works (Desktop) | [ ] | |
| 1.4 | Bottom nav works (Mobile - resize browser) | [ ] | |
| 1.5 | All nav links (Dashboard, Expenses, Groups, Profile) navigate correctly | [ ] | |

---

## Phase 2: Authentication

| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 2.1 | Register new user (name, email, password) | [ ] | |
| 2.2 | Login with email + password | [ ] | |
| 2.3 | Login with OTP (phone tab) | [ ] | Dev mode shows OTP in yellow box |
| 2.4 | Wrong password shows error | [ ] | |
| 2.5 | Wrong OTP shows error | [ ] | |
| 2.6 | Forgot password → email sent | [ ] | |
| 2.7 | Reset password via link works | [ ] | |
| 2.8 | Logout works | [ ] | |
| 2.9 | Unauthenticated user → redirected to login | [ ] | Try `/dashboard` without login |
| 2.10 | Google OAuth button visible (login + register) | [ ] | |
| 2.11 | Admin login → redirects to `/admin/dashboard` | [ ] | |
| 2.12 | Regular user login → redirects to `/dashboard` | [ ] | |

---

## Phase 3: Personal Expenses

| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 3.1 | Add personal expense (amount, category, description, date) | [ ] | |
| 3.2 | Expense appears in list after adding | [ ] | |
| 3.3 | Edit expense → changes saved | [ ] | |
| 3.4 | Delete expense → confirmation modal → removed from list | [ ] | |
| 3.5 | Filter by category works | [ ] | |
| 3.6 | Filter by date range works | [ ] | |
| 3.7 | Search by description works | [ ] | |
| 3.8 | Sort by date/amount works | [ ] | |
| 3.9 | Pagination works (add 25+ expenses) | [ ] | |
| 3.10 | Summary cards show correct totals | [ ] | |
| 3.11 | Category pie chart displays | [ ] | |
| 3.12 | Daily bar chart displays | [ ] | |
| 3.13 | Negative/zero amount rejected | [ ] | |
| 3.14 | Future date rejected | [ ] | |

---

## Task 1-3: OTP, Profile, Phone Verify

### Profile & Avatar
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| T3.1 | Profile page loads with name + email | [ ] | |
| T3.2 | Update name → saved | [ ] | |
| T3.3 | Update email → email_verified_at reset | [ ] | |
| T3.4 | Change password works | [ ] | |
| T3.5 | Delete account works (with password confirm) | [ ] | |
| T3.6 | Avatar: "Change Photo" opens file picker | [ ] | |
| T3.7 | Avatar: select image → crop modal appears | [ ] | |
| T3.8 | Avatar: crop and save → avatar updates | [ ] | |
| T3.9 | Avatar: shows in profile page after upload | [ ] | |
| T3.10 | Avatar: initials fallback when no avatar | [ ] | |

### Phone Verification
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| T3.11 | "Add Phone Number" button visible | [ ] | |
| T3.12 | Enter phone → Send OTP → OTP shown in dev mode | [ ] | |
| T3.13 | Enter correct OTP → phone verified, badge shows "Verified" | [ ] | |
| T3.14 | Wrong OTP → error message | [ ] | |
| T3.15 | Change phone → new OTP flow | [ ] | |
| T3.16 | Remove phone → phone removed | [ ] | |
| T3.17 | Duplicate phone number → error | [ ] | |
| T3.18 | Description says "required to create or join groups" | [ ] | |

### Email Verification
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| T3.19 | Email verification section shows Verified/Unverified badge | [ ] | |
| T3.20 | "Send Verification Email" button works (if unverified) | [ ] | |

### Phone Required for Groups
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| T3.21 | Without verified phone → clicking Groups → redirects to profile with error | [ ] | |
| T3.22 | Flash error message "Please verify your phone number..." shows | [ ] | |
| T3.23 | With verified phone → Groups page loads normally | [ ] | |

---

## Phase 4: Group Management

| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 4.1 | Create group (name + description) | [ ] | |
| 4.2 | Group appears in groups list | [ ] | |
| 4.3 | Creator is auto-added as admin | [ ] | |
| 4.4 | Invite code shown (8-char code) | [ ] | |
| 4.5 | Copy invite code works | [ ] | |
| 4.6 | Copy invite link works | [ ] | |
| 4.7 | Regenerate invite code works (admin) | [ ] | |
| 4.8 | Join group via code (another user) | [ ] | |
| 4.9 | Join group via link (/join/{code}) | [ ] | |
| 4.10 | Already a member → redirected to group page | [ ] | |
| 4.11 | Invalid invite code → error | [ ] | |
| 4.12 | Edit group (admin only) | [ ] | |
| 4.13 | Delete group (admin only) | [ ] | |
| 4.14 | Non-admin cannot see Edit/Delete buttons | [ ] | |

### Add Member by Phone
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 4.15 | "Add Member" button visible to admin only | [ ] | |
| 4.16 | Search by phone (3+ digits) → results appear | [ ] | |
| 4.17 | Search shows avatar + name + phone | [ ] | |
| 4.18 | Add member → success, member appears in list | [ ] | |
| 4.19 | Already a member → not in search results | [ ] | |
| 4.20 | Unverified phone user → not in search results | [ ] | |
| 4.21 | < 3 digits → "Type at least 3 digits" message | [ ] | |
| 4.22 | No results → "No registered user found" message | [ ] | |

### Member Removal Rules
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 4.23 | "Leave Group" button is removed (users can't self-leave) | [ ] | |
| 4.24 | Admin can remove member (no expenses in group) | [ ] | |
| 4.25 | Admin cannot remove member if group has expenses → error | [ ] | |
| 4.26 | Cannot remove another admin | [ ] | |

### Member Display
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 4.27 | Members show avatar thumbnail (or initials) | [ ] | |
| 4.28 | Members show email + phone number | [ ] | |
| 4.29 | Admin badge shows for admin members | [ ] | |

---

## Phase 5: Group Expenses

| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 5.1 | "View Expenses" link on group page works | [ ] | |
| 5.2 | "Add Expense" button opens create page | [ ] | |

### Add Group Expense
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 5.3 | Fill description, amount, category, date | [ ] | |
| 5.4 | Select "Paid By" from member dropdown | [ ] | |
| 5.5 | **Equal Split**: all members checked by default | [ ] | |
| 5.6 | Equal Split: uncheck member → per-person amount recalculates | [ ] | |
| 5.7 | Equal Split: rounding works (e.g., Rs.100 / 3 members) | [ ] | |
| 5.8 | **Custom Split**: enter amounts per member | [ ] | |
| 5.9 | Custom Split: shows "Remaining: Rs.X" in real-time | [ ] | |
| 5.10 | Custom Split: total != amount → cannot submit (validation) | [ ] | |
| 5.11 | **Percentage Split**: enter % per member | [ ] | |
| 5.12 | Percentage Split: auto-calculates amount from % | [ ] | |
| 5.13 | Percentage Split: total != 100% → cannot submit | [ ] | |
| 5.14 | Submit → expense created, redirects to list | [ ] | |

### Group Expense List
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 5.15 | Expenses listed with description, amount, paid by, split type badge | [ ] | |
| 5.16 | Click/expand expense → shows split details per member | [ ] | |
| 5.17 | Filter by category works | [ ] | |
| 5.18 | Filter by date range works | [ ] | |
| 5.19 | Search by description works | [ ] | |
| 5.20 | Pagination works | [ ] | |

### Edit/Delete Group Expense
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 5.21 | Edit expense (creator) → form pre-populated | [ ] | |
| 5.22 | Edit expense (group admin) → allowed | [ ] | |
| 5.23 | Edit expense (regular member, not creator) → forbidden | [ ] | |
| 5.24 | Delete expense → confirmation → soft deleted | [ ] | |
| 5.25 | Only creator or admin can delete | [ ] | |

### Balance Display
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 5.26 | Balance cards show at top of expense list | [ ] | |
| 5.27 | Positive balance → green (gets back) | [ ] | |
| 5.28 | Negative balance → red (owes) | [ ] | |
| 5.29 | Balance = paid - share (verify math manually) | [ ] | |
| 5.30 | Mini balance summary on group detail page | [ ] | |

---

## Phase 6: Settlement System

| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 6.1 | "Settle Up" button on group page / expense page → navigates to settlements | [ ] | |

### Settlement Page
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 6.2 | Balance overview cards show correctly | [ ] | |
| 6.3 | Suggested transactions show (from → to, amount) | [ ] | |
| 6.4 | No unsettled expenses → "All settled!" message | [ ] | |

### Settlement Actions
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 6.5 | "Settle Up" button → creates pending settlement records | [ ] | |
| 6.6 | Pending settlements appear in history with yellow badge | [ ] | |
| 6.7 | "Mark as Paid" → settlement status changes to "Completed" (green) | [ ] | |
| 6.8 | Only from_user, to_user, or admin can mark as paid | [ ] | |
| 6.9 | All settlements completed → expenses auto-marked as settled | [ ] | |
| 6.10 | After settling → "All settled!" message, no more suggested transactions | [ ] | |

### Admin Settle All
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 6.11 | "Settle All" button visible to admin only | [ ] | |
| 6.12 | Confirmation modal appears | [ ] | |
| 6.13 | All pending settlements → completed at once | [ ] | |

### Settlement Algorithm Verification
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 6.14 | 2 members, 1 expense → 1 transaction | [ ] | |
| 6.15 | 3 members, multiple expenses → verify minimum transactions | [ ] | Manually calculate and compare |
| 6.16 | Everyone paid equal → no settlement needed | [ ] | |
| 6.17 | Sum of all balances = 0 (verify) | [ ] | |

---

## Phase 7: Dashboard

| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 7.1 | Dashboard loads with all widgets | [ ] | |
| 7.2 | "This Month" card shows correct personal expense total | [ ] | |
| 7.3 | Month-over-month comparison % shown | [ ] | |
| 7.4 | "You Owe" card → sum of negative group balances | [ ] | |
| 7.5 | "You Are Owed" card → sum of positive group balances | [ ] | |
| 7.6 | Pending settlements alert shows (if any) | [ ] | |
| 7.7 | "Pay Now" link goes to settlement page | [ ] | |
| 7.8 | Groups overview → shows all groups with balance | [ ] | |
| 7.9 | Click group → navigates to group page | [ ] | |
| 7.10 | Category breakdown → top 5 categories with progress bars | [ ] | |
| 7.11 | Recent activity → shows mixed personal/group/settlement items | [ ] | |
| 7.12 | Relative dates work (Today, Yesterday, X days ago) | [ ] | |
| 7.13 | Quick action: "+Add Expense" button works | [ ] | |
| 7.14 | Quick action: "New Group" button works | [ ] | |
| 7.15 | New user (no data) → empty states show correctly | [ ] | |

---

## Phase 8: Admin Dashboard

### Admin Access
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 8.1 | Admin login → redirected to `/admin/dashboard` | [ ] | |
| 8.2 | Regular user → `/admin/dashboard` returns 403 | [ ] | |
| 8.3 | Admin sidebar: dark theme, correct nav links | [ ] | |
| 8.4 | "Back to App" link → goes to user dashboard | [ ] | |

### Admin Dashboard Stats
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 8.5 | Total Users count correct | [ ] | |
| 8.6 | Total Groups count correct | [ ] | |
| 8.7 | Total Expenses count correct | [ ] | |
| 8.8 | Total Amount tracked correct | [ ] | |
| 8.9 | New users/groups this month correct | [ ] | |
| 8.10 | Recent users table shows last 5 | [ ] | |

### User Management
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 8.11 | User list loads with all users | [ ] | |
| 8.12 | Search by name works | [ ] | |
| 8.13 | Search by email works | [ ] | |
| 8.14 | Filter by role (Admin/User) works | [ ] | |
| 8.15 | Change user role → updated immediately | [ ] | |
| 8.16 | Cannot change own role | [ ] | |
| 8.17 | Delete user → confirmation modal → user deleted | [ ] | |
| 8.18 | Cannot delete self | [ ] | |
| 8.19 | Pagination works | [ ] | |
| 8.20 | Phone verified badge shows correctly | [ ] | |

### Category Management
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 8.21 | Category list loads with expense counts | [ ] | |
| 8.22 | Add category → modal → created | [ ] | |
| 8.23 | Duplicate category name → error | [ ] | |
| 8.24 | Edit category → name/icon updated | [ ] | |
| 8.25 | Delete category (0 expenses) → deleted | [ ] | |
| 8.26 | Delete category (has expenses) → disabled/error | [ ] | |

---

## Phase 9: SEO & Landing Pages

### Public Pages
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 9.1 | Landing page `/` loads with hero, features, how-it-works, CTA | [ ] | |
| 9.2 | Features page `/features` loads with 6 feature cards | [ ] | |
| 9.3 | About page `/about` loads | [ ] | |
| 9.4 | Contact page `/contact` loads with form | [ ] | |
| 9.5 | Privacy page `/privacy` loads with policy text | [ ] | |
| 9.6 | Terms page `/terms` loads with terms text | [ ] | |
| 9.7 | Header nav links work on all public pages | [ ] | |
| 9.8 | Footer links work | [ ] | |
| 9.9 | Mobile menu toggle works | [ ] | |
| 9.10 | "Get Started" / CTA buttons → go to /register | [ ] | |

### Blog
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 9.11 | Blog index `/blog` loads (empty state if no posts) | [ ] | |
| 9.12 | Admin: create blog post (title, content, excerpt) | [ ] | |
| 9.13 | Admin: slug auto-generated from title | [ ] | |
| 9.14 | Admin: publish post (toggle) | [ ] | |
| 9.15 | Published post appears on `/blog` | [ ] | |
| 9.16 | Blog post detail `/blog/{slug}` loads | [ ] | |
| 9.17 | Draft post NOT visible on public blog | [ ] | |
| 9.18 | Admin: edit post → changes saved | [ ] | |
| 9.19 | Admin: delete post → removed | [ ] | |
| 9.20 | Blog post has proper meta title/description | [ ] | |

### Free Splitter Tool
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 9.21 | Splitter tool `/tools/expense-splitter` loads | [ ] | |
| 9.22 | Enter amount + people → equal split calculated | [ ] | |
| 9.23 | Add/remove people works | [ ] | |
| 9.24 | Switch to custom split → individual amounts editable | [ ] | |
| 9.25 | Custom split shows remaining amount | [ ] | |
| 9.26 | Works without login (no auth required) | [ ] | |

### SEO & Sitemap
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 9.27 | `/sitemap.xml` returns valid XML | [ ] | |
| 9.28 | Sitemap includes all public pages | [ ] | |
| 9.29 | Sitemap includes published blog posts | [ ] | |
| 9.30 | Each page has unique `<title>` tag | [ ] | View page source |
| 9.31 | Each page has `<meta name="description">` | [ ] | |
| 9.32 | OG tags present (og:title, og:description) | [ ] | |
| 9.33 | Blog post has JSON-LD schema markup | [ ] | View page source |

---

## General / Cross-Feature Tests

| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| G.1 | Mobile responsive: all pages look good at 375px width | [ ] | |
| G.2 | Tablet: pages work at 768px | [ ] | |
| G.3 | Desktop: sidebar visible, proper layout at 1280px+ | [ ] | |
| G.4 | Flash messages (success/error) show in green/red bars | [ ] | |
| G.5 | 404 error page shows for invalid URLs | [ ] | |
| G.6 | 403 error page shows for unauthorized access | [ ] | |
| G.7 | Currency format consistent (₹ with proper formatting) | [ ] | |
| G.8 | All forms show validation errors inline | [ ] | |
| G.9 | No console errors in browser dev tools | [ ] | |
| G.10 | Page transitions smooth (Inertia navigation) | [ ] | |

---

## Phase 10: Notifications

### 10.1 - Notifications Table & Migration
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 10.1.1 | `notifications` table exists in database | [ ] | |
| 10.1.2 | Migration runs without errors (`php artisan migrate`) | [ ] | |

### 10.2 - Notification Triggers
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 10.2.1 | Add group expense → all group members (except creator) get notification | [ ] | |
| 10.2.2 | Admin adds member to group → added user gets notification | [ ] | |
| 10.2.3 | Create settlement → debtor (from_user) gets notification | [ ] | |
| 10.2.4 | Mark settlement completed → creditor (to_user) gets notification | [ ] | |
| 10.2.5 | Creator of expense does NOT get self-notification | [ ] | |

### 10.3 - Bell Icon & Dropdown (Header)
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 10.3.1 | Bell icon visible in header | [ ] | |
| 10.3.2 | Unread count badge shows on bell when notifications exist | [ ] | |
| 10.3.3 | Badge shows "99+" when count exceeds 99 | [ ] | |
| 10.3.4 | Click bell → dropdown opens with recent notifications | [ ] | |
| 10.3.5 | Unread notifications have highlighted background | [ ] | |
| 10.3.6 | Click notification → navigates to relevant page | [ ] | |
| 10.3.7 | Click notification → marks as read | [ ] | |
| 10.3.8 | "Mark all read" button works | [ ] | |
| 10.3.9 | "View all notifications" link → full notifications page | [ ] | |
| 10.3.10 | Click outside dropdown → closes it | [ ] | |
| 10.3.11 | Empty state shown when no notifications | [ ] | |

### 10.4 - Notifications Page (/notifications)
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 10.4.1 | Page loads with list of all notifications | [ ] | |
| 10.4.2 | Unread notifications have blue dot and bold text | [ ] | |
| 10.4.3 | Click notification → navigates to relevant page + marks read | [ ] | |
| 10.4.4 | "Mark all as read" button visible when unread exist | [ ] | |
| 10.4.5 | Pagination works when >20 notifications | [ ] | |
| 10.4.6 | Empty state shows when no notifications | [ ] | |
| 10.4.7 | Relative timestamps correct (just now, Xm ago, Xh ago, Xd ago) | [ ] | |

### 10.5 - Sidebar & Bottom Nav
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 10.5.1 | Sidebar shows "Notifications" nav item with bell icon | [ ] | |
| 10.5.2 | Sidebar shows unread count badge (red) | [ ] | |
| 10.5.3 | Bottom nav shows "Alerts" item on mobile | [ ] | |
| 10.5.4 | Bottom nav badge shows count | [ ] | |
| 10.5.5 | Active state highlights when on notifications page | [ ] | |

### 10.6 - Notification Preferences (Profile)
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 10.6.1 | "Notification Preferences" section visible on profile page | [ ] | |
| 10.6.2 | Email notifications toggle works (checked = on) | [ ] | |
| 10.6.3 | Push notifications toggle works | [ ] | |
| 10.6.4 | Toggling email off → new notifications don't send email | [ ] | |
| 10.6.5 | Toggling email on → new notifications do send email | [ ] | |

### 10.7 - Email Notifications
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 10.7.1 | Group expense email sent to members (when email pref ON) | [ ] | |
| 10.7.2 | Added to group email sent (when email pref ON) | [ ] | |
| 10.7.3 | Settlement requested email sent (when email pref ON) | [ ] | |
| 10.7.4 | Settlement completed email sent (when email pref ON) | [ ] | |
| 10.7.5 | No email sent when user has notification_email = false | [ ] | |
| 10.7.6 | Email contains correct content and action link | [ ] | |

### 10.8 - Notification Icons & Types
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 10.8.1 | group_expense_added → shows currency/money icon (blue) | [ ] | |
| 10.8.2 | added_to_group → shows user-plus icon (green) | [ ] | |
| 10.8.3 | settlement_requested → shows clock icon (amber) | [ ] | |
| 10.8.4 | settlement_completed → shows checkmark icon (emerald) | [ ] | |

---

## Phase 11: PWA & Polish

### 11.1 - PWA Manifest & Icons
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 11.1.1 | /manifest.json loads correctly (check in browser) | [ ] | |
| 11.1.2 | Manifest has correct app name "JodTod" | [ ] | |
| 11.1.3 | Theme color is #6366f1 (indigo) | [ ] | |
| 11.1.4 | All icon sizes load (72, 96, 128, 144, 152, 192, 384, 512) | [ ] | |
| 11.1.5 | favicon.png loads in browser tab | [ ] | |
| 11.1.6 | Apple touch icon set correctly | [ ] | |

### 11.2 - Service Worker
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 11.2.1 | Service worker registers (check DevTools > Application > Service Workers) | [ ] | |
| 11.2.2 | Service worker shows as "activated" | [ ] | |
| 11.2.3 | Static assets cached (check DevTools > Application > Cache Storage) | [ ] | |
| 11.2.4 | Navigating pages works after first load (cached responses) | [ ] | |

### 11.3 - Offline Support
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 11.3.1 | Amber "You're offline" banner appears when internet disconnected | [ ] | |
| 11.3.2 | Banner disappears when internet reconnected | [ ] | |
| 11.3.3 | /offline page loads with proper branding | [ ] | |
| 11.3.4 | "Try Again" button reloads the page | [ ] | |
| 11.3.5 | Cached pages still accessible when offline | [ ] | |
| 11.3.6 | Navigating to uncached page shows offline fallback | [ ] | |

### 11.4 - PWA Install
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 11.4.1 | Chrome shows install icon in address bar (if on HTTPS) | [ ] | HTTPS required |
| 11.4.2 | App installs successfully on mobile (Android Chrome) | [ ] | |
| 11.4.3 | Installed app opens in standalone mode (no browser UI) | [ ] | |
| 11.4.4 | Manifest shortcuts visible in app context menu | [ ] | |

### 11.5 - Dark Mode
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 11.5.1 | Moon icon visible in header (light mode) | [ ] | |
| 11.5.2 | Click moon icon → switches to dark mode | [ ] | |
| 11.5.3 | Sun icon visible in header (dark mode) | [ ] | |
| 11.5.4 | Click sun icon → switches back to light mode | [ ] | |
| 11.5.5 | Dark mode persists after page reload | [ ] | |
| 11.5.6 | Dark mode persists across different pages | [ ] | |
| 11.5.7 | System dark mode preference detected on first visit | [ ] | |
| 11.5.8 | Header background changes (white → gray-800) | [ ] | |
| 11.5.9 | Sidebar background changes (white → gray-800) | [ ] | |
| 11.5.10 | Bottom nav background changes (white → gray-800) | [ ] | |
| 11.5.11 | Main content area background changes (gray-50 → gray-900) | [ ] | |
| 11.5.12 | Text colors adapt for readability in dark mode | [ ] | |
| 11.5.13 | Form inputs have dark backgrounds and light text | [ ] | |
| 11.5.14 | Flash messages (success/error) readable in dark mode | [ ] | |
| 11.5.15 | Profile dropdown menu dark themed | [ ] | |
| 11.5.16 | Notification dropdown dark themed | [ ] | |
| 11.5.17 | No flash of wrong theme on page load | [ ] | |

### 11.6 - Responsive & Polish
| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 11.6.1 | All pages responsive on mobile (375px width) | [ ] | |
| 11.6.2 | All pages responsive on tablet (768px width) | [ ] | |
| 11.6.3 | All pages responsive on desktop (1280px+ width) | [ ] | |
| 11.6.4 | Bottom nav only visible on mobile (hidden on lg+) | [ ] | |
| 11.6.5 | Sidebar only visible on desktop (hidden on mobile) | [ ] | |
| 11.6.6 | Dark mode toggle accessible on all screen sizes | [ ] | |

---

## Issue Tracking

Report issues here or in TASKS.md:

| # | Test Case | Issue Description | Severity | Status |
|---|-----------|-------------------|----------|--------|
| - | - | - | - | - |
