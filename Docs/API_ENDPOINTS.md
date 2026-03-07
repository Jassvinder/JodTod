# JodTod - API Endpoints

These endpoints serve dual purpose:
1. Inertia.js routes for web app (return Inertia responses)
2. API routes for future mobile app (return JSON)

---

## Authentication

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/register` | Show register page | No |
| POST | `/register` | Create new account | No |
| GET | `/login` | Show login page | No |
| POST | `/login` | Login with email/password | No |
| POST | `/logout` | Logout | Yes |
| GET | `/forgot-password` | Show forgot password page | No |
| POST | `/forgot-password` | Send reset link | No |
| GET | `/reset-password/{token}` | Show reset form | No |
| POST | `/reset-password` | Reset password | No |
| GET | `/auth/google` | Redirect to Google OAuth | No |
| GET | `/auth/google/callback` | Google OAuth callback | No |

---

## Dashboard

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/dashboard` | Dashboard with summary data | Yes |

**Response Data:**
```json
{
  "personal_summary": {
    "this_month_total": 15000,
    "last_month_total": 12000,
    "category_breakdown": [
      {"category": "Food", "total": 5000},
      {"category": "Travel", "total": 3000}
    ]
  },
  "groups_summary": {
    "total_you_owe": 1500,
    "total_owed_to_you": 2300,
    "groups": [
      {"id": 1, "name": "Goa Trip", "your_balance": -500},
      {"id": 2, "name": "Flat Expenses", "your_balance": 800}
    ]
  },
  "recent_activity": [...]
}
```

---

## Personal Expenses

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/expenses` | List personal expenses (with filters) | Yes |
| POST | `/expenses` | Add new personal expense | Yes |
| GET | `/expenses/{id}/edit` | Show edit form | Yes |
| PUT | `/expenses/{id}` | Update expense | Yes |
| DELETE | `/expenses/{id}` | Soft delete expense | Yes |
| GET | `/expenses/summary` | Get summary/chart data | Yes |

**Query Parameters for GET /expenses:**
```
?start_date=2026-03-01
&end_date=2026-03-31
&category_id=1
&search=pizza
&sort_by=date|amount
&sort_order=asc|desc
&page=1
```

**POST /expenses Request Body:**
```json
{
  "amount": 500.00,
  "category_id": 1,
  "description": "Lunch at cafe",
  "expense_date": "2026-03-07"
}
```

---

## Groups

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/groups` | List user's groups | Yes |
| POST | `/groups` | Create new group | Yes |
| GET | `/groups/{id}` | Group detail page | Yes |
| PUT | `/groups/{id}` | Update group (admin) | Yes |
| DELETE | `/groups/{id}` | Delete group (admin) | Yes |
| POST | `/groups/{id}/invite` | Generate/refresh invite code | Yes |
| POST | `/groups/join` | Join group via invite code | Yes |
| DELETE | `/groups/{id}/members/{user_id}` | Remove member (admin) | Yes |
| POST | `/groups/{id}/leave` | Leave group | Yes |

**POST /groups Request Body:**
```json
{
  "name": "Goa Trip",
  "description": "December 2026 trip expenses"
}
```

**POST /groups/join Request Body:**
```json
{
  "invite_code": "ABC12XYZ"
}
```

**GET /join/{invite_code}** - Public URL for invite links (redirects to join page)

---

## Group Expenses

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/groups/{id}/expenses` | List group expenses | Yes |
| POST | `/groups/{id}/expenses` | Add group expense | Yes |
| GET | `/groups/{id}/expenses/{eid}/edit` | Show edit form | Yes |
| PUT | `/groups/{id}/expenses/{eid}` | Update group expense | Yes |
| DELETE | `/groups/{id}/expenses/{eid}` | Delete group expense | Yes |
| GET | `/groups/{id}/balances` | Get all member balances | Yes |

**POST /groups/{id}/expenses Request Body:**
```json
{
  "description": "Hotel booking",
  "amount": 3000.00,
  "paid_by": 5,
  "category_id": 2,
  "expense_date": "2026-03-07",
  "split_type": "equal",
  "splits": [
    {"user_id": 5, "share_amount": 1000},
    {"user_id": 8, "share_amount": 1000},
    {"user_id": 12, "share_amount": 1000}
  ]
}
```

**For percentage split:**
```json
{
  "split_type": "percentage",
  "splits": [
    {"user_id": 5, "percentage": 50, "share_amount": 1500},
    {"user_id": 8, "percentage": 30, "share_amount": 900},
    {"user_id": 12, "percentage": 20, "share_amount": 600}
  ]
}
```

**GET /groups/{id}/balances Response:**
```json
{
  "balances": [
    {"user_id": 5, "name": "Amit", "balance": 1600},
    {"user_id": 8, "name": "Rahul", "balance": -500},
    {"user_id": 12, "name": "Priya", "balance": -1100}
  ]
}
```

---

## Settlements

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/groups/{id}/settlements` | Settlement history | Yes |
| POST | `/groups/{id}/settle` | Calculate & create settlement | Yes |
| PUT | `/groups/{id}/settlements/{sid}` | Mark settlement as completed | Yes |
| POST | `/groups/{id}/settle-all` | Mark all pending as completed | Yes |

**POST /groups/{id}/settle Response:**
```json
{
  "transactions": [
    {
      "from": {"id": 12, "name": "Priya"},
      "to": {"id": 5, "name": "Amit"},
      "amount": 1100
    },
    {
      "from": {"id": 8, "name": "Rahul"},
      "to": {"id": 5, "name": "Amit"},
      "amount": 500
    }
  ],
  "total_transactions": 2,
  "settlement_ids": [1, 2]
}
```

---

## User Profile

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/profile` | Show profile page | Yes |
| PUT | `/profile` | Update profile | Yes |
| PUT | `/profile/password` | Change password | Yes |
| DELETE | `/profile` | Delete account | Yes |

---

## Notifications

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/notifications` | List notifications | Yes |
| PUT | `/notifications/{id}/read` | Mark as read | Yes |
| PUT | `/notifications/read-all` | Mark all as read | Yes |

---

## Public/SEO Routes (Blade)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | Landing page |
| GET | `/features` | Features page |
| GET | `/about` | About page |
| GET | `/contact` | Contact page |
| GET | `/privacy` | Privacy policy |
| GET | `/terms` | Terms of service |
| GET | `/blog` | Blog listing |
| GET | `/blog/{slug}` | Blog post detail |
| GET | `/tools/expense-splitter` | Free splitter tool |
| GET | `/sitemap.xml` | Auto-generated sitemap |
