# JodTod - Application Flowchart

---

## 1. Overall App Flow

```
[App Open (Browser/PWA)]
    |
    v
[Landing Page] (Blade - SEO optimized)
    |
    +---> [Features Page]
    +---> [Blog]
    +---> [Free Tools] (Expense Splitter Calculator)
    +---> [Login / Register]
              |
              v
        [Check User Role]
              |
              +---> [role = 'admin'] --> [Admin Dashboard] (/admin)
              |
              +---> [role = 'user'] --> [User Dashboard] (/dashboard)
                        |
                        +---> [Personal Expenses]
                        +---> [Groups]
                        +---> [Profile / Settings]
                        +---> [Notifications]
```

---

## 2. Authentication Flow

```
[Login Page]
    |
    +---> [Email + Password Login]
    |         |
    |         v
    |     [Validate] --> [Success] --> [Check Role] --> [Admin? /admin : /dashboard]
    |                --> [Fail] --> [Error Message]
    |
    +---> [Google OAuth Login]
    |         |
    |         v
    |     [Google Consent] --> [Callback] --> [Create/Find User] --> [Check Role] --> [Redirect]
    |
    +---> [Register]
              |
              v
          [Name, Email, Password]
              |
              v
          [Email Verification]
              |
              v
          [Dashboard]

[Forgot Password]
    |
    v
[Enter Email] --> [Reset Link Sent] --> [New Password] --> [Login]
```

---

## 3. Personal Expenses Flow

```
[Dashboard]
    |
    v
[Personal Expenses Tab]
    |
    +---> [Add Expense Button]
    |         |
    |         v
    |     [Expense Form]
    |         - Amount (required)
    |         - Category (dropdown - Food, Travel, Shopping, Bills, Entertainment, Other)
    |         - Description/Note (optional)
    |         - Date (default: today)
    |         |
    |         v
    |     [Save] --> [Expense List Updated]
    |
    +---> [Expense List]
    |         |
    |         +---> Filter: All / Today / This Week / This Month / Custom Date Range
    |         +---> Search by description
    |         +---> Sort: Date / Amount / Category
    |         |
    |         +---> [Click on Expense]
    |                   |
    |                   +---> [Edit Expense]
    |                   +---> [Delete Expense] --> [Confirm Dialog] --> [Delete]
    |
    +---> [Summary / Overview]
              |
              +---> Total spent (this month)
              +---> Category-wise breakdown (pie chart)
              +---> Daily/Weekly/Monthly trend (bar chart)
              +---> Comparison with last month
```

---

## 4. Groups Flow

```
[Dashboard]
    |
    v
[Groups Tab]
    |
    +---> [Create New Group]
    |         |
    |         v
    |     [Group Form]
    |         - Group Name (required)
    |         - Description (optional)
    |         |
    |         v
    |     [Group Created] --> [Invite Members]
    |                             |
    |                             +---> Share Invite Link
    |                             +---> Share Invite Code
    |                             +---> Add by Email
    |
    +---> [Join Group]
    |         |
    |         +---> Enter Invite Code
    |         +---> Click Invite Link
    |         |
    |         v
    |     [Joined Successfully] --> [Group Detail]
    |
    +---> [My Groups List]
              |
              v
          [Click on Group] --> [Group Detail Screen]
```

---

## 5. Group Detail Flow

```
[Group Detail Screen]
    |
    +---> [Group Info]
    |         - Group Name
    |         - Members List (with roles)
    |         - Total Group Spend
    |         - My Balance (+ ya -)
    |
    +---> [Add Group Expense]
    |         |
    |         v
    |     [Expense Form]
    |         - Description (required)
    |         - Amount (required)
    |         - Paid By: [Select Member] (default: me)
    |         - Date (default: today)
    |         - Category
    |         - Admin only: can add expense on behalf of any member
    |         - Split Type:
    |             |
    |             +---> [Equal Split]
    |             |         Equal share among selected members
    |             |
    |             +---> [Custom Split]
    |             |         Enter specific amount per member
    |             |         Validation: total = expense amount
    |             |
    |             +---> [Percentage Split]
    |                       Enter % per member
    |                       Validation: total = 100%
    |         |
    |         v
    |     [Save] --> [Expense Added] --> [Balances Updated]
    |
    +---> [Group Expenses List]
    |         - All expenses with: description, amount, paid by, date
    |         - Filter by: date range, member, category
    |         - Edit / Delete (only creator or admin)
    |
    +---> [Balances Overview]
    |         - Har member ka current balance dikhao
    |         - "+500" means usko milna hai
    |         - "-300" means usko dena hai
    |
    +---> [Settlement Button] --> See SETTLEMENT_LOGIC.md
    |
    +---> [Settlement History]
    |         - Past settlements list
    |         - Date, amounts, who paid whom
    |
    +---> [Group Settings] (Admin only)
              - Edit group name/description
              - Remove member (if no unsettled expenses) / Deactivate member (if unsettled expenses exist)
              - Reactivate deactivated member
              - Delete group (confirm dialog)
```

---

## 6. Profile & Settings Flow

```
[Profile / Settings]
    |
    +---> [Edit Profile]
    |         - Name
    |         - Email (read-only if Google login)
    |         - Avatar/Photo
    |         - Change Password
    |
    +---> [App Settings]
    |         - Currency (INR default)
    |         - Language (Hindi/English)
    |         - Dark Mode toggle
    |
    +---> [Notification Settings]
    |         - Email notifications on/off
    |         - Push notifications on/off
    |         - Expense added notification
    |         - Settlement reminder
    |
    +---> [Logout]
```

---

## 7. Notification Flow

```
[Triggers]
    |
    +---> New expense added in group --> Notify all group members
    +---> Member joined group --> Notify admin
    +---> Settlement requested --> Notify involved members
    +---> Settlement completed --> Notify both parties
    +---> Weekly summary --> All users (optional)
```

---

## 8. Admin Dashboard Flow

```
[Admin Login] --> [/admin] (Admin Dashboard)
    |
    +---> [Overview / Stats]
    |         - Total users count
    |         - Total groups count
    |         - Total expenses (all users)
    |         - New users this month
    |         - Active groups this month
    |
    +---> [User Management] (/admin/users)
    |         - List all users (search, filter by role, paginated)
    |         - View user details (expenses, groups, activity)
    |         - Change user role (admin/user)
    |         - Ban / Unban user
    |         - Delete user (soft delete, confirm dialog)
    |
    +---> [Category Management] (/admin/categories)
    |         - List categories
    |         - Add new category
    |         - Edit category (name, icon)
    |         - Delete category (only if no expenses linked)
    |
    +---> [Blog Management] (/admin/blog)
    |         - List all blog posts (draft/published)
    |         - Create new post (title, slug, content, excerpt, image, SEO fields)
    |         - Edit post
    |         - Publish / Unpublish post
    |         - Delete post
    |
    +---> [Reports] (/admin/reports)
    |         - Expense trends (daily/weekly/monthly)
    |         - Category-wise breakdown (all users)
    |         - Top groups by activity
    |         - Settlement stats
    |
    +---> [Settings] (/admin/settings)
              - Site name / tagline (future)
              - Default currency
              - Maintenance mode toggle
```

---

## 9. PWA Flow

```
[First Visit]
    |
    v
[Service Worker Register]
    |
    v
[Cache Static Assets]
    |
    v
[Show "Add to Home Screen" prompt]
    |
    v
[User Installs PWA]
    |
    v
[App Icon on Home Screen]
    |
    v
[Opens like native app - no browser bar]
    |
    v
[Offline Support]
    +---> View cached expenses
    +---> Add expense offline --> Sync when online
    +---> Show offline indicator
```
