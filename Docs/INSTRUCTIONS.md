# JodTod - Feature-wise Implementation Instructions

---

## Feature 1: Authentication System

### What to build:
- Registration (name, email, password)
- Login (email + password)
- Google OAuth login
- Forgot password / Reset password
- Email verification
- JWT token for API (future mobile app)

### Implementation Notes:
- Use Laravel Breeze or Fortify for auth scaffolding
- Google login via Laravel Socialite
- Store users in `users` table
- After login redirect to `/dashboard`
- Auth pages should be Blade templates (SEO not needed but simpler)
- Session-based auth for web, JWT for API

### Validation Rules:
- Name: required, min 2, max 100
- Email: required, valid email, unique
- Password: required, min 8, confirmed

---

## Feature 2: Personal Expense Tracking

### What to build:
- Add expense (amount, category, description, date)
- Edit expense
- Delete expense (soft delete)
- List expenses with filters (date range, category)
- Summary: total, category-wise, monthly trend

### Implementation Notes:
- Expenses with `group_id = NULL` are personal expenses
- Categories: Food, Travel, Shopping, Bills, Entertainment, Medical, Education, Other
- Default date = today
- Amount must be positive, max 2 decimal places
- Summary data for charts: return JSON for Vue chart components
- Pagination: 20 items per page

### Validation Rules:
- Amount: required, numeric, min 0.01, max 99999999.99
- Category: required, must be from predefined list
- Description: optional, max 255
- Date: required, valid date, not future date

---

## Feature 3: Group Management

### What to build:
- Create group (name, description)
- Edit group (admin only)
- Delete group (admin only, confirm required)
- Invite members (generate invite code + link)
- Join group via code/link
- Leave group
- Remove member (admin only)
- Group members list with roles (admin/member)

### Implementation Notes:
- Group creator = admin automatically
- Invite code: 8 character unique alphanumeric (uppercase)
- Invite link format: `jodtod.com/join/{invite_code}`
- Member cannot leave if they have unsettled balance
- Admin cannot be removed
- If admin leaves, oldest member becomes admin
- Minimum 2 members required for group expenses

### Validation Rules:
- Group name: required, min 2, max 100
- Description: optional, max 500
- Invite code: auto-generated, unique, 8 chars

---

## Feature 4: Group Expenses

### What to build:
- Add group expense with split options
- Edit group expense (creator or admin only)
- Delete group expense (creator or admin only)
- List group expenses with filters
- Show individual member's balance in group

### Split Types:

#### Equal Split:
- Total amount / number of selected members
- All members selected by default
- Can deselect members who are not part of this expense
- Handle rounding: extra paisa goes to the person who paid

#### Custom Split:
- Enter specific amount for each member
- Total of all amounts MUST equal expense amount
- Show real-time validation: "Remaining: Rs.X"
- At least one member must have amount > 0

#### Percentage Split:
- Enter percentage for each member
- Total MUST equal 100%
- Show real-time validation: "Remaining: X%"
- Convert percentage to amount and store in expense_splits

### Implementation Notes:
- Store split details in `expense_splits` table
- `paid_by` can be different from the user adding the expense
- Expense amount stored in `expenses` table
- Each member's share stored in `expense_splits` table
- Recalculate balances on every add/edit/delete

### Validation Rules:
- Description: required, max 255
- Amount: required, numeric, min 0.01
- Paid by: required, must be group member
- Split type: required, one of (equal, custom, percentage)
- Custom split total must equal expense amount
- Percentage split total must equal 100

---

## Feature 5: Settlement System

### What to build:
- Calculate balances (net balance for each member)
- Generate optimized settlement plan (minimum transactions)
- Show settlement screen: "A owes B Rs.X"
- Settle individual transaction
- Settle all transactions
- Settlement history

### Implementation Notes:
- FULL ALGORITHM DETAILS: See `Docs/SETTLEMENT_LOGIC.md`
- Settlement only calculates unsettled expenses (`is_settled = false`)
- After settlement, mark all related expenses as settled
- Store each transaction in `settlements` table
- Settlement can be "pending" or "completed"
- Show confirmation before settling

---

## Feature 6: Landing Page & SEO Pages (Blade)

### What to build:
- Landing/Home page - app intro, features, CTA
- Features page - detailed feature list
- Blog system - articles for SEO traffic
- Free tools - Expense Splitter Calculator (no login required)
- About, Contact, Privacy Policy, Terms pages

### Implementation Notes:
- All these pages use Blade templates (NOT Inertia)
- Mobile responsive with Tailwind CSS
- Include proper meta tags, OG tags, structured data
- Auto-generate sitemap.xml
- Blog: store in database, admin panel to write/edit posts
- Free tools: interactive Vue components embedded in Blade
- Fast loading: minimize CSS/JS, optimize images

### SEO Checklist per page:
- Title tag (unique, 50-60 chars)
- Meta description (unique, 150-160 chars)
- H1 tag (one per page)
- Alt tags on images
- Internal linking
- Schema markup (Organization, WebApplication, BlogPosting)
- Open Graph tags for social sharing

---

## Feature 7: Dashboard

### What to build:
- Overview of personal expenses (this month total, category breakdown)
- List of groups with quick balance view
- Recent activity (last 5-10 expenses across personal + groups)
- Quick add expense button
- Pending settlements indicator

### Implementation Notes:
- Dashboard is an Inertia + Vue page
- Fetch summary data from API
- Charts: use a lightweight chart library (Chart.js or ApexCharts)
- Show "You owe Rs.X overall" and "You are owed Rs.X overall"
- Mobile: stack sections vertically, swipeable

---

## Feature 8: PWA Setup

### What to build:
- Service worker for caching
- Web app manifest (name, icons, theme color)
- Offline support for viewing cached data
- "Add to Home Screen" prompt
- Background sync for offline-added expenses

### Implementation Notes:
- Use laravel-pwa package or manual setup
- Cache strategy: Network First for API, Cache First for static assets
- App icons: 192x192 and 512x512 PNG
- Theme color: match app primary color
- Offline page: show cached data with "You're offline" indicator
- When back online: sync queued expenses automatically

---

## Feature 9: Notifications

### What to build:
- In-app notifications (bell icon with count)
- Email notifications (optional, user can toggle)
- Push notifications via PWA (future)

### Notification Triggers:
- Expense added in your group
- You were added to a group
- Settlement requested
- Settlement completed
- Weekly expense summary

### Implementation Notes:
- Use Laravel Notifications system
- Store in `notifications` table (Laravel default)
- Mark as read/unread
- Real-time: consider Laravel Echo + Pusher (or polling initially)
- Email: queue emails for performance

---

## Feature 10: Responsive Design

### Breakpoints (Tailwind defaults):
- Mobile: < 640px (sm)
- Tablet: 640px - 1024px (md, lg)
- Desktop: > 1024px (xl, 2xl)

### Mobile-first approach:
- Design for mobile first, then scale up
- Bottom navigation bar on mobile
- Side navigation on desktop
- Touch-friendly buttons (min 44x44px)
- Swipe gestures where useful (delete expense)
- No horizontal scroll ever
