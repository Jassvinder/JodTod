# JodTod - Mobile App Task Tracker
**Note** After complete any task move to the completed section with descending order. If tasks completed from Current Tasks section then place new empty task with next number in this section.

**Tech Stack:** React Native + Expo | Backend: Laravel API (Sanctum) | Same MySQL DB as web

## Current Tasks

- [] **Task 11**
    Kai baar terminal me ye show hota hai.
    Logs for your project will appear below. Press Ctrl+C to exit.
    Android Bundled 5416ms node_modules\expo-router\entry.js (1385 modules)
    LOG  Push notifications not available (Expo Go or missing package)
    Android Bundled 42ms node_modules\expo-router\entry.js (1 module)
    LOG  Push notifications not available (Expo Go or missing package)

    
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
> API Controller: DONE

- [x] **M-301** - **[API]** Create ExpenseController with: index (paginated + filters), store, show, update, destroy (2026-03-19)
- [x] **M-302** - **[API]** Expense description autocomplete endpoint (2026-03-19)
- [x] **M-303** - **[API]** Categories list endpoint (GET /api/v1/categories) (2026-03-19)
- [x] **M-304** - Expense list screen (paginated, pull-to-refresh) (2026-03-19)
- [x] **M-305** - Expense filters (category dropdown, search) (2026-03-19)
- [x] **M-306** - Add expense screen (amount, category, description with autocomplete, date picker) (2026-03-19)
- [x] **M-307** - Edit expense screen (same form, pre-filled) (2026-03-19)
- [x] **M-308** - Delete expense (long press → confirm) (2026-03-19)
- [x] **M-309** - Image upload for expense (2 max, camera + gallery, preview, remove) (2026-03-19)
- [ ] **M-310** - Expense summary/charts (category breakdown pie chart, monthly trends)

### Phase 4: Income Tracking
> API Controller: DONE

- [x] **M-401** - **[API]** Create IncomeController with: index (paginated), store, update, destroy, summary (2026-03-19)
- [x] **M-402** - **[API]** Income source autocomplete endpoint (2026-03-19)
- [x] **M-403** - Income list screen (paginated, search, pull-to-refresh) (2026-03-19)
- [x] **M-404** - Add/Edit income (amount, source with autocomplete, description, date) (2026-03-19)
- [x] **M-405** - Delete income (long press → confirm) (2026-03-19)
- [x] **M-406** - Income summary cards (this month income, savings/loss) (2026-03-19)
- [ ] **M-407** - Income vs Expense chart (6-month trend)

### Phase 5: Todos/Tasks
> API Controller: DONE

- [x] **M-501** - **[API]** Create TodoController with: index (filters + stats), store, update, destroy, toggle (2026-03-19)
- [x] **M-502** - **[API]** Create TodoCategoryController with: index, store, update, destroy (2026-03-19)
- [x] **M-503** - Todo list screen (filters: All, Pending, Completed, Assigned to me/by me, priority, category) (2026-03-19)
- [x] **M-504** - Add/Edit todo (title, priority, due date, category, assign to contact, reminder) (2026-03-19)
- [x] **M-505** - Toggle complete (tap checkbox, strikethrough) (2026-03-19)
- [x] **M-506** - Delete todo (long press → confirm) (2026-03-19)
- [x] **M-507** - Todo categories management screen (CRUD with 10 preset color circles) (2026-03-19)
- [ ] **M-508** - Reminder notifications (local push notifications using expo-notifications)
- [x] **M-509** - Overdue visual indicators (red date highlight) (2026-03-19)

### Phase 6: Contacts
> API Controller: DONE

- [x] **M-601** - **[API]** Create ContactController with: index, search, store, destroy (2026-03-19)
- [x] **M-602** - Contacts list screen (search, paginated, pull-to-refresh, FAB to add) (2026-03-19)
- [x] **M-603** - Search & add JodTod users screen (debounced search, add button per result) (2026-03-19)
- [x] **M-604** - Remove contact (long press → confirm) (2026-03-19)

### Phase 7: Group Management
> API Controller: DONE

- [x] **M-701** - **[API]** Create GroupController with: index, store, show, update, destroy, join, leave, addMember, removeMember, reactivateMember (2026-03-19)
- [x] **M-702** - Groups list screen (my groups with member count, balance badge, FAB, join button) (2026-03-19)
- [x] **M-703** - Create group screen (name, description) (2026-03-19)
- [x] **M-704** - Group detail screen (members, expenses preview, balances, invite code, admin actions) (2026-03-19)
- [x] **M-705** - Join group (invite code input) (2026-03-19)
- [x] **M-706** - Edit group (admin only) (2026-03-19)
- [x] **M-707** - Add member from contacts (admin only) (2026-03-19)
- [x] **M-708** - Remove/Deactivate member (admin only, correct labels) (2026-03-19)
- [x] **M-709** - Reactivate member (admin only) (2026-03-19)
- [x] **M-710** - Leave group (deactivate if unsettled) (2026-03-19)
- [x] **M-711** - Delete group (blocked if unsettled) (2026-03-19)
- [x] **M-712** - Invite code copy (expo-clipboard) (2026-03-19)

### Phase 8: Group Expenses
> API Controller: DONE

- [x] **M-801** - **[API]** Create GroupExpenseController with: index, store, show, update, destroy, balances (2026-03-19)
- [x] **M-802** - Group expense list screen (paginated, category filter, search, FAB) (2026-03-19)
- [x] **M-803** - Add group expense (amount, category, description, date, paid by, split types: equal/custom/percentage with validation) (2026-03-19)
- [x] **M-804** - Edit group expense (pre-filled with existing splits) (2026-03-19)
- [x] **M-805** - Delete group expense (long press → confirm) (2026-03-19)
- [x] **M-806** - Member-wise balance display on group detail (2026-03-19)

### Phase 9: Settlements
> API Controller: DONE

- [x] **M-901** - **[API]** Create SettlementController with: index, settle, markCompleted, settleAll (exact min-transfers algorithm) (2026-03-19)
- [x] **M-902** - Settlement screen (balance cards, suggested transactions, history) (2026-03-19)
- [x] **M-903** - Settle Up button (admin only, disabled if pending exist, confirm) (2026-03-19)
- [x] **M-904** - Mark as Paid button (from_user/to_user/admin) (2026-03-19)
- [x] **M-905** - Settle All button (admin only, confirm) (2026-03-19)

### Phase 10: Notifications
> API Controller: DONE

- [x] **M-1001** - **[API]** Create NotificationController with: index, recent (10 + unread count), markRead, markAllRead (2026-03-19)
- [x] **M-1002** - Notifications list screen (paginated, pull-to-refresh, type-based icons) (2026-03-19)
- [x] **M-1003** - Mark as read (tap) / Mark all as read button (2026-03-19)
- [x] **M-1004** - Unread count badge on dashboard bell icon (2026-03-19)
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

- [x] **Task 10** (2026-03-21)
    A. Reminder field now uses DatePickerField with datetime mode (date picker → time picker flow). No more manual typing.
    B. Clear filter button styled as red pill button with close icon (was plain text).
    C. Created reusable BottomNav component - added to ALL 16 sub-pages (contacts, todos, groups, settlements, notifications). Footer nav now visible everywhere.
    D. DatePickerField updated: chevron-down → calendar icon. Added maxDate to expense/income pickers (no future dates). Reminder/due date allow future dates.
    E. My contacts also has footer nav (included in the 16 pages).
- [x] **Task 9** Todo category form keyboard closing bug fixed. Root cause: TextInput was inside FlatList's ListHeaderComponent - typing triggered re-render which unmounted/remounted the TextInput, dismissing keyboard. Fix: moved form outside FlatList as a separate View. (2026-03-21)
- [x] **Task 8** (2026-03-21)
    A. Income tab restored as 6th footer icon (was hidden).
    B. Groups/Profile headings fixed - show "Groups"/"Profile" instead of "Index".
    C. Sub-page footer nav hiding is standard mobile UX (Instagram/WhatsApp behavior) - kept as is.
    D. Web 507/508 changes applied: stats summary bar on group detail, Member Shares purple section (all-time), "Settlement" button text, "All Settled" badge with tick on groups list, Member Shares hidden after settled on settlement page, isAllSettled flag added to API.
    E. CollapsibleSection component rewritten with smooth animated height transition (no more jerky LayoutAnimation).
- [x] **Task 7C** Smart 4-step settle flow implemented on both mobile and web: (1) "Settle Up" when unsettled expenses exist, (2) "Mark All as Paid" when pending settlements exist, (3) "All Settled!" green message when everything is done. Button auto-changes based on state. (2026-03-20)
- [x] **Task 7** (A) Standardized image upload to 1 image across both personal and group expense add. Personal expense reduced from 2 to 1. Group expense add now supports image upload via FormData. (B) Group delete now checks unsettled expenses upfront - shows "Cannot Delete" message directly in confirm dialog instead of making API call first. ConfirmDialog updated to support single-button mode. (2026-03-20)
- [x] **Task 6** "Add Expense" button moved out of Recent Expenses collapsible section. Now in a row with "Settle Up" button - both always visible regardless of section collapse state. (2026-03-20)
- [x] **Task 5** (2026-03-20)
    A. Calendar date picker added to ALL date fields (expenses, incomes, todos, group expenses - both add and edit). Uses @react-native-community/datetimepicker with native calendar UI.
    B. Group expense "Split Between" section now collapsible, default closed. Users expand when needed.
    C. All listing sections made collapsible with reusable CollapsibleSection component. Applied to Dashboard (6 sections), Group Detail (3 sections), Settlements (3 sections). Animated arrow, clickable headers, defaultOpen=true. Component supports custom colors/icons.
    D. Member Shares section added to settlements screen (was missing from mobile). Shows total unsettled amount + each member's share in compact list format (not cards). API updated with calculateMemberShares method.
- [x] **Task 4** Group photo upload added to both Create Group and Edit Group screens. Photo shows in group list and group detail. Backend supports upload/change/remove on both web and API. (2026-03-20)
- [x] **Task 3** Added header with hamburger menu (left) + user avatar (right) on dashboard. Hamburger drawer has all web navigation links. Footer nav: replaced Income tab with Tasks tab. Income accessible from drawer menu. (2026-03-20)
- [x] **Task 2** Group join by invite code now requires admin approval. Join creates pending request, admin gets notification, can approve/reject from group detail. Implemented on both web and mobile. (2026-03-20)
- [x] **Task 1** Notification tap now navigates to relevant screen: todo notifications → tasks, group expense → group expenses, settlement → settlements, group → group detail. (2026-03-20)


### Phase 1: Project Setup

