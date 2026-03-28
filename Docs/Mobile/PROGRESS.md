# JodTod Mobile App - Development Progress

## Project Info
- **Code:** `D:\JodTodApp`
- **GitHub:** https://github.com/Jassvinder/JodTodApp
- **Docs:** `D:\phpserver\www\JodTod\Docs\Mobile\`
- **Web Backend (API):** `D:\phpserver\www\JodTod` (Laravel - same DB, shared API)

## Tech Stack
- React Native 0.83 + Expo 55 (expo-router for file-based routing)
- TypeScript
- NativeWind (Tailwind CSS for React Native)
- Zustand (state management)
- Axios (API client)
- expo-secure-store (token storage)
- @tanstack/react-query (installed, not yet used)
- Ionicons (icons)

---

## What's Built (as of 2026-03-19)

### Project Setup
- Expo project initialized with TypeScript
- NativeWind configured (tailwind.config.js, global.css)
- File-based routing via expo-router
- Constants: colors.ts (theme colors), config.ts (API_URL)
- .env for API_URL (gitignored)

### API Client (`services/api.ts`)
- Axios instance with baseURL from config
- Request interceptor: auto-attaches Bearer token from SecureStore
- Response interceptor: clears token on 401 (auto-logout)
- 15s timeout, JSON headers

### Auth System
**Store:** `stores/authStore.ts` (Zustand)
- State: user, token, isLoading, isAuthenticated
- Actions: login, register, loginWithOtp, logout, loadToken, setUser
- Token persisted in expo-secure-store

**Service:** `services/auth.ts`
- login, register, logout, getUser, sendOtp, verifyOtp, forgotPassword, resetPassword

**Screens:**
- `app/(auth)/login.tsx` - Email/Password + OTP login (tabbed UI, full validation, dev OTP debug display)
- `app/(auth)/register.tsx` - Name, email, password, confirm password
- `app/(auth)/forgot-password.tsx` - Email input → success message
- `app/(auth)/verify-email.tsx` - Resend verification email

**Navigation:**
- `app/_layout.tsx` - Root layout, checks auth state, routes to (auth) or (tabs)
- `app/index.tsx` - Splash/loading screen while checking token
- `app/(auth)/_layout.tsx` - Auth stack layout

### Dashboard (`app/(tabs)/index.tsx`)
**Service:** `services/dashboard.ts` - getData() calls GET /dashboard

**UI Sections:**
- Welcome header with user name
- Summary cards (Expenses, Income, Savings/Loss, You Owe, Owed to You)
- Pending settlements alert (amber banner with payment details)
- Todo stats widget (pending/overdue count)
- Groups overview (list with balance per group)
- Category breakdown (progress bars with percentages)
- Recent activity feed (personal/group expenses + settlements)
- Pull-to-refresh

### Profile (`app/(tabs)/profile/index.tsx`)
**Service:** `services/profile.ts` - getProfile, updateProfile, deleteAccount

**UI Sections:**
- Avatar display (image or initials)
- Email verified/unverified badge
- Profile info card (name, email, phone, currency) with Edit mode
- Edit: inline form for name + email (saves via API)
- Notification preferences (email/push toggles - display only for now)
- Logout button
- Delete account (password confirmation flow)

### Tab Navigation (`app/(tabs)/_layout.tsx`)
5 tabs configured:
1. **Home** (dashboard) - home-outline icon
2. **Expenses** - wallet-outline icon (placeholder)
3. **Groups** - people-outline icon (placeholder)
4. **Incomes** - trending-up-outline icon (placeholder)
5. **Profile** - person-outline icon

### Placeholder Screens (Coming Soon)
- `app/(tabs)/expenses/index.tsx` - "Coming soon" with icon
- `app/(tabs)/groups/index.tsx` - "Coming soon" with icon
- `app/(tabs)/incomes/index.tsx` - "Coming soon" with icon

### Utilities
- `utils/format.ts` - formatCurrency (INR), formatRelativeDate, percentChange
- `utils/device.ts` - getDeviceName (for Sanctum token naming)

### Types
- `types/models.ts` - User, LoginPayload, RegisterPayload
- `types/api.ts` - ApiResponse<T>, PaginatedResponse<T>
- `types/dashboard.ts` - DashboardData with all sub-types

---

## What's NOT Built Yet

### Backend API Controllers (in Laravel `D:\phpserver\www\JodTod`)
These need to be created under `app/Http/Controllers/Api/V1/`:
- ExpenseController (personal expenses CRUD + filters)
- IncomeController (income CRUD + summary)
- TodoController + TodoCategoryController (todos CRUD + categories)
- ContactController (contacts search + CRUD)
- GroupController (groups CRUD + members + invite)
- GroupExpenseController (group expenses + splits)
- SettlementController (settle up + mark paid + history)
- NotificationController (list + mark read + unread count)
- CategoryController (categories list)

**Note:** DashboardController and ProfileController already have `wantsJson()` support (dual-purpose).

### Mobile Screens
- Personal Expenses (list, add, edit, delete, filters, images, charts)
- Income (list, add, edit, delete, summary, charts)
- Todos (list, add, edit, delete, categories, reminders, assignment)
- Contacts (list, search, add, remove)
- Groups (list, create, detail, members, invite, join, leave)
- Group Expenses (list, add with splits, edit, delete)
- Settlements (balances, suggestions, settle up, mark paid, history)
- Notifications (list, mark read, badge)

### Features
- Dark mode toggle
- Push notifications (expo-notifications)
- Deep linking (/join/{code})
- Offline indicator
- Image upload (camera + gallery)
- Google OAuth
- Phone verification

---

## File Structure
```
D:\JodTodApp\
├── app/
│   ├── _layout.tsx              # Root layout (auth check)
│   ├── index.tsx                # Splash screen
│   ├── (auth)/
│   │   ├── _layout.tsx          # Auth stack
│   │   ├── login.tsx            # Email + OTP login
│   │   ├── register.tsx         # Registration
│   │   ├── forgot-password.tsx  # Password reset
│   │   └── verify-email.tsx     # Email verification
│   └── (tabs)/
│       ├── _layout.tsx          # Tab navigator (5 tabs)
│       ├── index.tsx            # Dashboard/Home
│       ├── expenses/
│       │   ├── _layout.tsx
│       │   └── index.tsx        # Placeholder
│       ├── groups/
│       │   ├── _layout.tsx
│       │   └── index.tsx        # Placeholder
│       ├── incomes/
│       │   ├── _layout.tsx
│       │   └── index.tsx        # Placeholder
│       └── profile/
│           ├── _layout.tsx
│           └── index.tsx        # Profile screen
├── services/
│   ├── api.ts                   # Axios instance + interceptors
│   ├── auth.ts                  # Auth API calls
│   ├── dashboard.ts             # Dashboard API
│   └── profile.ts               # Profile API
├── stores/
│   └── authStore.ts             # Zustand auth state
├── types/
│   ├── models.ts                # User, payloads
│   ├── api.ts                   # ApiResponse types
│   └── dashboard.ts             # Dashboard data types
├── utils/
│   ├── format.ts                # Currency, dates, percentage
│   └── device.ts                # Device name helper
├── constants/
│   ├── colors.ts                # Theme colors
│   └── config.ts                # API_URL
├── assets/images/
│   └── logo.png                 # App logo
├── package.json
├── app.json                     # Expo config
├── tsconfig.json
├── tailwind.config.js
└── global.css
```
