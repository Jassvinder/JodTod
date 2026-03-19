# JodTod - Mobile App Task Tracker
**Note** After complete any task move to the completed section with descending order. If tasks completed from Current Tasks section then place new empty task with next number in this section.

**Tech Stack:** React Native + Expo | Backend: Laravel API (Sanctum) | Same MySQL DB as web

## Current Tasks

- [] **Task 1**


## PENDING

### Phase 1: Project Setup & Auth (API: Partially Done)
> Auth API controllers already exist (AuthController, OtpController). Need React Native screens.

- [ ] **M-101** - React Native + Expo project setup (folder structure, navigation, theme, dark mode support)
- [ ] **M-102** - API client setup (Axios instance, base URL, token interceptor, 401 auto-logout, refresh handling)
- [ ] **M-103** - Secure token storage (expo-secure-store for Sanctum tokens)
- [ ] **M-104** - Login screen (email/password) - API: POST /api/v1/login (DONE)
- [ ] **M-105** - OTP Login screen (phone input → OTP verify) - API: POST /api/v1/otp/send, /api/v1/otp/verify (DONE)
- [ ] **M-106** - Registration screen - API: POST /api/v1/register (DONE)
- [ ] **M-107** - Forgot Password screen - API: POST /api/v1/forgot-password (DONE)
- [ ] **M-108** - Email verification screen/flow - API: POST /api/v1/email/verification-notification (DONE)
- [ ] **M-109** - Auth navigation flow (splash → auth check → login/home)
- [ ] **M-110** - Google OAuth login (Expo AuthSession / expo-google-sign-in) - API: Need endpoint

### Phase 2: Dashboard & Profile
> Dashboard and Profile controllers already have wantsJson() support (dual-purpose).

- [ ] **M-201** - Bottom tab navigation (Home, Expenses, Groups, Notifications, Profile)
- [ ] **M-202** - Dashboard/Home screen - API: GET /api/v1/dashboard (PARTIAL - needs JSON response testing)
    - Summary cards (Expenses, Income, Savings, You Owe, Owed to You)
    - Income vs Expense bar chart (6 months)
    - Groups overview with balance badges
    - Pending settlements alert
    - Recent activity feed
    - Tasks widget (pending/overdue count)
    - Quick action buttons (Add Expense, Group Expense)
- [ ] **M-203** - Profile screen - API: GET /api/v1/user (DONE)
    - View profile info (name, email, phone, avatar)
    - Currency & language selection
    - Notification preferences (email/push toggles)
- [ ] **M-204** - Edit profile (name only, email/phone are verified fields) - API: Need PUT /api/v1/profile
- [ ] **M-205** - Avatar upload with crop - API: Need POST /api/v1/profile/avatar
- [ ] **M-206** - Phone verification (OTP flow) - API: Need endpoints
- [ ] **M-207** - Change password - API: Need PUT /api/v1/password
- [ ] **M-208** - Delete account - API: Need DELETE /api/v1/profile
- [ ] **M-209** - Logout - API: POST /api/v1/logout (DONE)

### Phase 3: Personal Expenses
> API Controller: MISSING - Need to create Api/V1/ExpenseController

- [ ] **M-301** - **[API]** Create ExpenseController with: index (paginated + filters), store, show, update, destroy
- [ ] **M-302** - **[API]** Expense description autocomplete endpoint
- [ ] **M-303** - **[API]** Categories list endpoint (GET /api/v1/categories)
- [ ] **M-304** - Expense list screen (paginated, pull-to-refresh)
- [ ] **M-305** - Expense filters (category dropdown, date range picker, search)
- [ ] **M-306** - Add expense screen (amount, category, description with autocomplete, date/time picker)
- [ ] **M-307** - Edit expense screen (same form, pre-filled)
- [ ] **M-308** - Delete expense (swipe-to-delete or long press → confirm)
- [ ] **M-309** - Image upload for expense (2 max, camera + gallery, preview, remove)
- [ ] **M-310** - Expense summary/charts (category breakdown pie chart, monthly trends)

### Phase 4: Income Tracking
> API Controller: MISSING - Need to create Api/V1/IncomeController

- [ ] **M-401** - **[API]** Create IncomeController with: index (paginated), store, update, destroy, summary
- [ ] **M-402** - **[API]** Income source autocomplete endpoint
- [ ] **M-403** - Income list screen (paginated, grouped by month)
- [ ] **M-404** - Add/Edit income (amount, source with autocomplete, description, date)
- [ ] **M-405** - Delete income (swipe/long press → confirm)
- [ ] **M-406** - Income summary cards (this month income, last month, savings)
- [ ] **M-407** - Income vs Expense chart (6-month trend)

### Phase 5: Todos/Tasks
> API Controller: MISSING - Need to create Api/V1/TodoController, TodoCategoryController

- [ ] **M-501** - **[API]** Create TodoController with: index (filters), store, update, destroy, toggle
- [ ] **M-502** - **[API]** Create TodoCategoryController with: index, store, update, destroy
- [ ] **M-503** - Todo list screen (filters: All, Pending, Completed, Assigned to me, Assigned by me, By category)
- [ ] **M-504** - Add/Edit todo (title, priority, due date, category, assign to contact, reminder)
- [ ] **M-505** - Toggle complete (tap checkbox)
- [ ] **M-506** - Delete todo (swipe/long press → confirm)
- [ ] **M-507** - Todo categories management screen (CRUD with color picker)
- [ ] **M-508** - Reminder notifications (local push notifications using expo-notifications)
- [ ] **M-509** - Overdue visual indicators (red badge/highlight)

### Phase 6: Contacts
> API Controller: MISSING - Need to create Api/V1/ContactController

- [ ] **M-601** - **[API]** Create ContactController with: index, search, store, destroy
- [ ] **M-602** - Contacts list screen (alphabetical, search bar)
- [ ] **M-603** - Search & add JodTod users (by name/email/phone)
- [ ] **M-604** - Remove contact (swipe/long press → confirm)

### Phase 7: Group Management
> API Controller: MISSING - Need to create Api/V1/GroupController

- [ ] **M-701** - **[API]** Create GroupController with: index, store, show, update, destroy, join, leave, addMember, removeMember, reactivateMember
- [ ] **M-702** - Groups list screen (my groups with member count, balance badge)
- [ ] **M-703** - Create group screen (name, description)
- [ ] **M-704** - Group detail screen (members list with status badges: Admin/Inactive, expenses preview, total amount)
- [ ] **M-705** - Join group (enter invite code or deep link /join/{code})
- [ ] **M-706** - Edit group (admin only - name, description)
- [ ] **M-707** - Add member from contacts (modal with contacts list, admin only)
- [ ] **M-708** - Remove/Deactivate member (admin only, proper labels based on unsettled expenses)
- [ ] **M-709** - Reactivate member (admin only)
- [ ] **M-710** - Leave group (with unsettled expense check → deactivate if needed)
- [ ] **M-711** - Delete group (admin only, blocked if unsettled expenses)
- [ ] **M-712** - Invite code share (copy code, share link via native share sheet)

### Phase 8: Group Expenses
> API Controller: MISSING - Need to create Api/V1/GroupExpenseController

- [ ] **M-801** - **[API]** Create GroupExpenseController with: index (paginated + filters), store, show, update, destroy, balances
- [ ] **M-802** - Group expense list screen (paginated, filters: category, date range)
- [ ] **M-803** - Add group expense screen:
    - Amount, description, category, date/time
    - Paid by (member dropdown, default: me)
    - Split type tabs: Equal / Custom / Percentage
    - Equal: checkboxes to select members, auto-calculate per person
    - Custom: amount input per member, total validation
    - Percentage: % input per member, 100% validation
- [ ] **M-804** - Edit group expense (same form, pre-filled with existing splits)
- [ ] **M-805** - Delete group expense (creator or admin, confirm dialog)
- [ ] **M-806** - Member-wise balance display on group detail

### Phase 9: Settlements
> API Controller: MISSING - Need to create Api/V1/SettlementController

- [ ] **M-901** - **[API]** Create SettlementController with: index (balances + suggestions + history), settle (admin only), markCompleted, settleAll (admin only)
- [ ] **M-902** - Settlement screen (3 sections):
    - Balance overview cards (per member: gets back/owes/settled)
    - Suggested transactions (optimized min-transfers)
    - Settlement history (paginated, status badges)
- [ ] **M-903** - Settle Up button (admin only, disabled if pending settlements exist, confirm dialog)
- [ ] **M-904** - Mark as Paid button (debtor/creditor/admin can mark individual settlement)
- [ ] **M-905** - Settle All button (admin only, confirm dialog)

### Phase 10: Notifications
> API Controller: MISSING - Need to create Api/V1/NotificationController

- [ ] **M-1001** - **[API]** Create NotificationController with: index (paginated), recent (latest 10 + unread count), markRead, markAllRead
- [ ] **M-1002** - Notifications list screen (paginated, pull-to-refresh)
- [ ] **M-1003** - Mark as read (tap notification) / Mark all as read button
- [ ] **M-1004** - Unread count badge on bottom tab bell icon
- [ ] **M-1005** - Push notifications setup (expo-notifications, device token registration)
- [ ] **M-1006** - **[API]** Device token storage endpoint (POST /api/v1/device-token)
- [ ] **M-1007** - Notification tap → deep link to relevant screen (group, settlement, todo etc.)

### Phase 11: Polish & UX
- [ ] **M-1101** - Dark mode support (system preference + manual toggle, persist in AsyncStorage)
- [ ] **M-1102** - Pull-to-refresh on all list screens
- [ ] **M-1103** - Empty states with helpful messages on all screens
- [ ] **M-1104** - Loading skeletons/spinners
- [ ] **M-1105** - Confirm dialogs for all destructive actions (delete, leave, ban etc.)
- [ ] **M-1106** - Toast/snackbar for success/error messages (use API response messages, never hardcode)
- [ ] **M-1107** - Offline indicator banner
- [ ] **M-1108** - Deep linking setup (expo-linking: /join/{code}, notification taps)
- [ ] **M-1109** - App icon + splash screen (JodTod branding)
- [ ] **M-1110** - Currency formatting (INR with ₹ symbol, Indian number format)

### Release
- [ ] Manual testing of all features
- [ ] Android build (EAS Build)
- [ ] iOS build (EAS Build)
- [ ] App store submission


## COMPLETED =================================================

## Completed Current Tasks


### Phase 1: Project Setup

