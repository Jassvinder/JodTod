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

## Contacts (Mobile API)

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/contacts` | List approved contacts | Yes |
| GET | `/api/v1/contacts/search?q={name or friend code}` | Search users by name or exact friend code (case-insensitive); returns public name, friend code, avatar, and `request_status` | Yes |
| POST | `/api/v1/contacts` | Send a contact request | Yes |
| GET | `/api/v1/contacts/requests` | List incoming contact requests | Yes |
| GET | `/api/v1/contacts/requests/sent` | List outgoing pending requests (with `contact_user`) | Yes |
| POST | `/api/v1/contacts/{id}/approve` | Approve an incoming request | Yes |
| DELETE | `/api/v1/contacts/{id}/reject` | Decline an incoming request | Yes |
| DELETE | `/api/v1/contacts/{id}` | Remove an approved contact, or cancel an outgoing pending request | Yes |

Contact search requires at least three characters. Email and phone are only available after the recipient approves the request.

Search hides the current user and approved contacts. Users with a pending request stay in the results with `request_status`: `sent` (you requested them), `received` (they requested you), or `null`.

Every user has a unique 6-character `friend_code` (3 letters + 3 digits). It tells apart users with the same name, and a friend can share it so you can find them directly.

---

## Group Expenses

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/groups/{id}/expenses` | List group expenses | Yes |
| POST | `/groups/{id}/expenses` | Add group expense | Yes |
| GET | `/groups/{id}/expenses/{eid}/edit` | Show edit form | Yes |
| PUT | `/groups/{id}/expenses/{eid}` | Update group expense (group admin only) | Yes |
| DELETE | `/groups/{id}/expenses/{eid}` | Delete group expense (group admin only) | Yes |
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

## Todos and Todo Categories (Mobile API)

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/todos` | List the authenticated user's own and assigned Todos | Yes |
| POST | `/api/v1/todos` | Create a Todo | Yes |
| PUT | `/api/v1/todos/{id}` | Update a Todo | Yes |
| PATCH | `/api/v1/todos/{id}/toggle` | Toggle Todo completion | Yes |
| DELETE | `/api/v1/todos/{id}` | Delete an owned Todo | Yes |
| GET | `/api/v1/todo-categories` | List own and assigned-task categories; creates Home and Market defaults for the authenticated user | Yes |
| POST | `/api/v1/todo-categories` | Create a Todo category | Yes |
| PUT | `/api/v1/todo-categories/{id}` | Update an owned Todo category | Yes |
| DELETE | `/api/v1/todo-categories/{id}` | Delete an owned, non-default Todo category | Yes |

**Todo category contract:** use `todo_category_id` in Todo create and update request bodies. It references `todo_categories.id`; `category_id` is reserved for expense categories.

Default categories include `is_default: true` and cannot be deleted.

## Remember Items (Mobile API)

Personal log of recurring things, such as when the gas cylinder was replaced or remote batteries were changed. Each item keeps a history of dates.

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/remember-items` | List own items with summary fields, ordered by name | Yes |
| POST | `/api/v1/remember-items` | Create an item: `name` (required, max 100), `note` (optional), `done_on` (optional first entry date, not in the future) | Yes |
| GET | `/api/v1/remember-items/{id}` | Item summary plus `entries`, newest first | Yes |
| PUT | `/api/v1/remember-items/{id}` | Update `name` and `note` | Yes |
| DELETE | `/api/v1/remember-items/{id}` | Delete the item and all its entries | Yes |
| POST | `/api/v1/remember-items/{id}/entries` | Add an entry: `done_on` (required, not in the future), `note` (optional) | Yes |
| DELETE | `/api/v1/remember-items/{id}/entries/{entryId}` | Delete one entry of the item | Yes |

**Item summary fields:** `entries_count`, `last_done_on` (latest `done_on` or `null`), and `avg_gap_days` (average days between first and last entry, `null` with fewer than two entries). Item, entry create/delete, and update responses return the summary plus `entries`. Items owned by another user return 403.

The dashboard response includes `rememberStats.total`, the number of items the user has saved.

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
