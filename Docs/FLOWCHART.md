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
        [Dashboard / Home Screen] (Inertia + Vue)
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
    |     [Validate] --> [Success] --> [Dashboard]
    |                --> [Fail] --> [Error Message]
    |
    +---> [Google OAuth Login]
    |         |
    |         v
    |     [Google Consent] --> [Callback] --> [Create/Find User] --> [Dashboard]
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
    |         - Split Type:
    |             |
    |             +---> [Equal Split]
    |             |         All members ka equal share
    |             |
    |             +---> [Custom Split]
    |             |         Har member ka amount manually enter karo
    |             |         Validation: total = expense amount
    |             |
    |             +---> [Percentage Split]
    |                       Har member ka % enter karo
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
              - Remove member
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

## 8. PWA Flow

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
