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
| GET | `/api/v1/todos/{id}` | One Todo with `category`, `assignee` and `user` (creator, i.e. who assigned it). Creator or assignee only | Yes |
| PUT | `/api/v1/todos/{id}` | Update a Todo. The assignee can edit it but cannot change `assigned_to` (send it unchanged or leave it out) | Yes |
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

## Lend & Borrow (Mobile API)

Simple personal ledger of money given to and received from people.

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/ledger-people` | `{ people: [summary], totals: { you_will_get, you_will_give, net } }`, most recently active first. Optional `search` (name) | Yes |
| POST | `/api/v1/ledger-people` | Add a person: `name` (required, max 100), `phone` (optional, max 20), `note` (optional) | Yes |
| GET | `/api/v1/ledger-people/{id}` | Person summary plus `entries`, newest first | Yes |
| PUT | `/api/v1/ledger-people/{id}` | Update `name`, `phone`, `note` | Yes |
| DELETE | `/api/v1/ledger-people/{id}` | Delete the person and all entries | Yes |
| POST | `/api/v1/ledger-people/{id}/entries` | Add an entry: `type` (`gave`/`got`), `amount` (> 0), `entry_date` (not in the future), `note` (optional) | Yes |
| PUT | `/api/v1/ledger-people/{id}/entries/{entryId}` | Update an entry (same fields) | Yes |
| DELETE | `/api/v1/ledger-people/{id}/entries/{entryId}` | Delete an entry | Yes |

**Person summary fields:** `entries_count`, `total_gave`, `total_got`, `balance` (`total_gave - total_got`; positive = they owe the user), `last_entry_on`. Each entry has `balance_after` (running balance after that entry). Entry and person write responses return the full detail. Another user's records return 403.

## Team Builder (Mobile API)

Network marketing teams (upline/downline), prospects and follow-ups. All routes are under `/api/v1/team-builder`. Date-times are sent as local Indian time (`YYYY-MM-DD HH:MM`) and returned as ISO 8601 with offset.

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| PUT | `/api/v1/user/app-mode` | `app_mode`: `personal` or `team_builder`. Returns the user. `users.app_mode` is null until the user chooses | Yes |
| GET | `/team-builder/dashboard` | `week` (Monday to now: `plans_shown`, `follow_ups`, `calls`, `meetings`, `new_prospects`, `joined`), `follow_ups` (`overdue`, `today` counts), `pipeline` (count per status), `teams` (`id`, `name`, `downline_count`), `target` (`plans_per_week`, `follow_ups_per_week`, `set_by_name`) or null, `latest_announcement` (newest visible across my teams) or null | Yes |
| GET | `/team-builder/follow-ups` | Open prospects with a follow-up date: `{ overdue, today, upcoming }` | Yes |
| GET | `/team-builder/teams` | My teams: team summary list | Yes |
| POST | `/team-builder/teams` | Create a team: `name` (required, max 100), `company_name` (optional). The creator becomes the top member | Yes |
| POST | `/team-builder/teams/join` | `invite_code` (8 chars, any member's code). That member becomes the sponsor. Sponsor and new member become approved contacts of each other. On the user's first team, their prospects without a team move into it. 404 for a wrong code, 422 if already in the team | Yes |
| GET | `/team-builder/teams/{id}` | Team summary. Members only (403) | Yes |
| GET | `/team-builder/teams/{id}/members` | Everyone below me in the team (any depth) as one flat list, flagged members first: `{ members: [member summary], counts: { total, push, training } }` | Yes |
| GET | `/team-builder/teams/{id}/members/{userId}` | Member summary plus `prospects` (`id`, `name`, `status`, `next_follow_up_at`, `joined_at`, `last_activity_at`; never phone or note), open ones first. 404 if the user is not below me | Yes |
| PUT | `/team-builder/teams/{id}` | Rename (`name`, `company_name`). Creator only | Yes |
| DELETE | `/team-builder/teams/{id}` | Creator only, and only while nobody else is in the team (422) | Yes |
| POST | `/team-builder/teams/{id}/leave` | Not for the creator, and not while anyone joined under you (422) | Yes |
| PUT | `/team-builder/target` | My weekly target: `plans_per_week`, `follow_ups_per_week` (0-500, 0 = none). Returns the target | Yes |
| PUT | `/team-builder/teams/{id}/members/{userId}/target` | An upline sets the weekly target of someone below them (same fields). Notifies the member. Returns the member summary | Yes |
| GET | `/team-builder/teams/{id}/announcements` | `{ announcements, can_post }`: up to 50 newest posts by me or anyone above me in the team. `can_post` is true when someone is below me | Yes |
| POST | `/team-builder/teams/{id}/announcements` | `message` (max 1000), `link` (optional URL). Sends a push to everyone below me. 422 when nobody is below me | Yes |
| DELETE | `/team-builder/announcements/{id}` | Author only | Yes |
| GET | `/team-builder/prospects` | `{ prospects: [summary], status_counts }`, most recently worked first. Optional `status`, `search` (name) | Yes |
| POST | `/team-builder/prospects` | `name` (required), `phone`, `source`, `team_id` (must be my team), `status` (default `new`), `next_follow_up_at`, `note` (max 500) | Yes |
| GET | `/team-builder/prospects/{id}` | Prospect plus `activities`, newest first. Owner only (403) | Yes |
| PUT | `/team-builder/prospects/{id}` | Same fields as create | Yes |
| DELETE | `/team-builder/prospects/{id}` | Deletes the prospect and its activities | Yes |
| POST | `/team-builder/prospects/{id}/activities` | `type` (`call`, `plan_shown`, `follow_up`, `meeting`, `note`), `note`, `done_at` (default now, not in the future), optional `status`, and `next_follow_up_at` (sending the key with null clears it). `plan_shown` on a `new` prospect moves it to `plan_shown`. Returns the prospect detail | Yes |
| DELETE | `/team-builder/prospects/{id}/activities/{activityId}` | Delete an activity. Returns the prospect detail | Yes |

**Team summary fields:** `id`, `name`, `company_name`, `is_owner`, `invite_code` (my own code), `joined_at`, `level` (0 = top), `sponsor` (`id`, `name`, `avatar_url`, `phone`) or null, `direct_count`, `downline_count` (whole downline, any depth).

**Member summary fields:** `user_id`, `name`, `avatar_url`, `phone`, `joined_at`, `is_direct`, `sponsor_name`, `last_active_at`, `open_prospects`, `overdue_follow_ups`, `plans_week`, `follow_ups_week`, `joined_week`, `target` (or null), `flag`. Only prospects saved under this team count. `flag`: `push` when nothing was logged for 7 days (counted from the join date for new members), `training` when 5+ plans were shown in 30 days and nobody joined, otherwise null.

**Team Builder notifications** (database + push, push data includes `team_id`): `team_member_joined` (to the whole upline), `team_announcement` (to everyone below the author), `team_target_set` (to the member), `team_weekly_summary` (Mondays 8:30 AM via `team-builder:weekly-summary`, to anyone with people below them: people count, push / training counts, overdue follow-ups).

**Prospect fields:** `id`, `name`, `phone`, `source`, `status` (`new`, `plan_shown`, `follow_up`, `joined`, `not_interested`), `team_id`, `team_name`, `next_follow_up_at`, `joined_at` (set when the status becomes `joined`), `note`, `last_activity_at` (lists only), `created_at`. Moving to `joined` or `not_interested` clears `next_follow_up_at`.

## Guest Group Members (Mobile API)

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/api/v1/groups/{id}/guest-members` | Admin only. Add a member who does not use the app: `name` (required, max 100, unique among the group's members). Creates a `users` row with `is_guest = true` | Yes |

Group members in `GET /api/v1/groups/{id}` include `is_guest`. Guests take part in expenses, splits, balances and settlements like any member; the admin marks their settlements as paid. They never receive notifications and cannot be found in contact search or assigned tasks.

## Support JodTod

The app is free for now; this page explains that it may add an optional donation or subscription later and shows the UPI details for voluntary donations. The note text lives in `config('site.support')`; the UPI ID, payee name and QR image are set in Admin > Settings. Both use `App\Services\SupportService::details()`.

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/support` | Web Support JodTod page (Inertia) | Yes |
| GET | `/api/v1/support` | Same content for the mobile app | Yes |
| PUT | `/admin/settings` | Also accepts `support_upi_id` (`name@bank`), `support_payee_name`, `support_qr` (image, max 2 MB, multipart with `_method=put`) and `remove_support_qr` | Admin |

**Response fields:** `title`, `paragraphs[]`, `early_users_note`, `pay_steps[]`, `upi_id` (`null` when not set), `payee_name`, `qr_url` (`null` when no QR is uploaded), `upi_link` (`upi://pay?pa=...&pn=...&cu=INR`, `null` without a UPI ID). The payment card is hidden when both `upi_id` and `qr_url` are `null`.

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
