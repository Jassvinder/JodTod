# JodTod - Database Schema

---

## Table: users

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| name | VARCHAR(100) | NOT NULL | |
| email | VARCHAR(255) | NULLABLE, UNIQUE | NULL only for guest members |
| email_verified_at | TIMESTAMP | NULLABLE | |
| password | VARCHAR(255) | NULLABLE (Google login) | |
| role | ENUM('admin', 'user') | DEFAULT 'user' | App-level role (admin or regular user) |
| is_guest | BOOLEAN | DEFAULT false | Group member without the app (name only). Cannot log in, never notified, hidden from search and admin counts (`User::registered()` scope) |
| guest_created_by | BIGINT UNSIGNED | NULLABLE, FK -> users.id, ON DELETE SET NULL | Group admin who added the guest |
| avatar | VARCHAR(255) | NULLABLE | Profile photo path |
| google_id | VARCHAR(255) | NULLABLE, UNIQUE | For Google OAuth |
| currency | VARCHAR(10) | DEFAULT 'INR' | Preferred currency |
| language | VARCHAR(10) | DEFAULT 'hi' | hi or en |
| notification_email | BOOLEAN | DEFAULT true | |
| notification_push | BOOLEAN | DEFAULT true | |
| remember_token | VARCHAR(100) | NULLABLE | |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

## Table: groups

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| name | VARCHAR(100) | NOT NULL | Group name |
| description | VARCHAR(500) | NULLABLE | |
| invite_code | VARCHAR(8) | NOT NULL, UNIQUE | 8 char alphanumeric code |
| created_by | BIGINT UNSIGNED | FK -> users.id | Group creator (admin) |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

**Indexes:** invite_code (UNIQUE), created_by

---

## Table: group_members

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| group_id | BIGINT UNSIGNED | FK -> groups.id, ON DELETE CASCADE | |
| user_id | BIGINT UNSIGNED | FK -> users.id, ON DELETE CASCADE | |
| role | ENUM('admin', 'member') | DEFAULT 'member' | |
| is_active | BOOLEAN | DEFAULT true | Active members appear in new expense splits. Inactive members are excluded from new expenses but remain in settlement calculations for their existing balances. |
| joined_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |

**Indexes:** (group_id, user_id) UNIQUE - ek user ek group me ek baar hi ho sakta hai

---

## Table: categories

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| name | VARCHAR(50) | NOT NULL, UNIQUE | |
| icon | VARCHAR(50) | NULLABLE | Icon class or emoji |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

**Default Categories (Seeder):**
1. Food (khana-peena)
2. Travel (safar)
3. Shopping (khareedari)
4. Bills (bijli, paani, phone)
5. Entertainment (manoranjan)
6. Medical (dawai, doctor)
7. Education (padhai)
8. Rent (kiraya)
9. Other (baaki sab)

---

## Table: expenses

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| group_id | BIGINT UNSIGNED | FK -> groups.id, NULLABLE | NULL = personal expense |
| user_id | BIGINT UNSIGNED | FK -> users.id | Who created this entry |
| paid_by | BIGINT UNSIGNED | FK -> users.id | Who actually paid |
| amount | DECIMAL(12,2) | NOT NULL | Total expense amount |
| category_id | BIGINT UNSIGNED | FK -> categories.id | |
| description | VARCHAR(255) | NULLABLE | |
| expense_date | DATE | NOT NULL | Date of expense |
| split_type | ENUM('equal','custom','percentage') | NULLABLE | NULL for personal |
| is_settled | BOOLEAN | DEFAULT false | Only for group expenses |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |
| deleted_at | TIMESTAMP | NULLABLE | Soft delete |

**Indexes:** group_id, user_id, paid_by, category_id, expense_date, is_settled

**Notes:**
- `group_id = NULL` means personal expense
- `group_id != NULL` means group expense
- `user_id` = who added the expense entry
- `paid_by` = who actually paid the money (can be different from user_id)
- `split_type` only applies to group expenses
- Soft delete enabled (deleted_at) so data is recoverable

---

## Table: expense_splits

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| expense_id | BIGINT UNSIGNED | FK -> expenses.id, ON DELETE CASCADE | |
| user_id | BIGINT UNSIGNED | FK -> users.id | Member who owes this share |
| share_amount | DECIMAL(12,2) | NOT NULL | How much this member owes |
| percentage | DECIMAL(5,2) | NULLABLE | Only for percentage split |
| is_settled | BOOLEAN | DEFAULT false | |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

**Indexes:** (expense_id, user_id) UNIQUE, is_settled

**Notes:**
- For equal split: share_amount = total_amount / number_of_members
- For custom split: share_amount = manually entered amount
- For percentage split: share_amount = (percentage/100) * total_amount
- SUM of all share_amounts for an expense MUST equal expense amount

---

## Table: settlements

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| group_id | BIGINT UNSIGNED | FK -> groups.id | |
| from_user | BIGINT UNSIGNED | FK -> users.id | Who is paying (debtor) |
| to_user | BIGINT UNSIGNED | FK -> users.id | Who is receiving (creditor) |
| amount | DECIMAL(12,2) | NOT NULL | Settlement amount |
| status | ENUM('pending','completed') | DEFAULT 'pending' | |
| note | VARCHAR(255) | NULLABLE | Optional note |
| settled_at | TIMESTAMP | NULLABLE | When marked as completed |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

**Indexes:** group_id, from_user, to_user, status

---

## Table: blog_posts (SEO)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| title | VARCHAR(255) | NOT NULL | |
| slug | VARCHAR(255) | NOT NULL, UNIQUE | URL-friendly title |
| content | TEXT | NOT NULL | Blog content (HTML) |
| excerpt | VARCHAR(500) | NULLABLE | Short description for listing |
| meta_title | VARCHAR(60) | NULLABLE | SEO title |
| meta_description | VARCHAR(160) | NULLABLE | SEO description |
| featured_image | VARCHAR(255) | NULLABLE | |
| author_id | BIGINT UNSIGNED | FK -> users.id | |
| is_published | BOOLEAN | DEFAULT false | |
| published_at | TIMESTAMP | NULLABLE | |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

**Indexes:** slug (UNIQUE), is_published, published_at

---

## Table: todos

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| user_id | BIGINT UNSIGNED | FK -> users.id, ON DELETE CASCADE | |
| todo_category_id | BIGINT UNSIGNED | FK -> todo_categories.id, NULLABLE, ON DELETE SET NULL | Optional user-defined task category |
| title | VARCHAR(255) | NOT NULL | Task title |
| priority | ENUM('low', 'medium', 'high') | DEFAULT 'medium' | |
| due_date | DATE | NULLABLE | Optional deadline |
| is_completed | BOOLEAN | DEFAULT false | |
| completed_at | TIMESTAMP | NULLABLE | When marked as done |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

**Indexes:** (user_id, is_completed), todo_category_id, due_date

---

## Table: todo_categories

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| user_id | BIGINT UNSIGNED | FK -> users.id, ON DELETE CASCADE | Category owner |
| name | VARCHAR(50) | NOT NULL | Category name, unique per owner |
| color | VARCHAR(7) | NOT NULL, DEFAULT '#6366f1' | Hex color value |
| is_default | BOOLEAN | NOT NULL, DEFAULT false | Home and Market defaults; protected from deletion |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

**Indexes:** (user_id, name) UNIQUE

---

## Table: incomes

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| user_id | BIGINT UNSIGNED | FK -> users.id, ON DELETE CASCADE | |
| amount | DECIMAL(12,2) | NOT NULL | Income amount |
| source | VARCHAR(100) | NOT NULL | Income source (Salary, Freelance, etc.) |
| description | VARCHAR(255) | NULLABLE | Optional details |
| income_date | DATE | NOT NULL | Date of income |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

**Indexes:** (user_id, income_date)

---

## Table: remember_items

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| user_id | BIGINT UNSIGNED | FK -> users.id, ON DELETE CASCADE | Item owner |
| name | VARCHAR(100) | NOT NULL | What to remember, e.g. "Gas Cylinder" |
| note | VARCHAR(255) | NULLABLE | Optional item details |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

**Indexes:** (user_id, name)

---

## Table: remember_entries

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| remember_item_id | BIGINT UNSIGNED | FK -> remember_items.id, ON DELETE CASCADE | Parent item |
| done_on | DATE | NOT NULL | When it was done; cannot be in the future |
| note | VARCHAR(255) | NULLABLE | Optional entry details |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

**Indexes:** (remember_item_id, done_on)

---

## Table: ledger_people

Lend & Borrow: people the user gives money to or gets money from.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| user_id | BIGINT UNSIGNED | FK -> users.id, ON DELETE CASCADE | Ledger owner |
| name | VARCHAR(100) | NOT NULL | Person's name |
| phone | VARCHAR(20) | NULLABLE | Optional phone |
| note | VARCHAR(255) | NULLABLE | Optional details |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | Touched on every new entry (list is ordered by it) |

**Indexes:** (user_id, name)

---

## Table: ledger_entries

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| ledger_person_id | BIGINT UNSIGNED | FK -> ledger_people.id, ON DELETE CASCADE | Parent person |
| type | ENUM('gave', 'got') | NOT NULL | gave = user gave money to the person, got = user received money from them |
| amount | DECIMAL(12,2) | NOT NULL | Always positive |
| entry_date | DATE | NOT NULL | Cannot be in the future |
| note | VARCHAR(255) | NULLABLE | Optional details |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

**Indexes:** (ledger_person_id, entry_date)

Balance with a person = SUM(gave) - SUM(got). Positive: they owe the user. Negative: the user owes them.

---

## Column: users.app_mode

ENUM('personal', 'team_builder'), NULLABLE, after `is_guest`. Which part of the mobile app the user works in. Null until the user chooses once after login.

---

## Table: teams

Team Builder: one network marketing team of any company.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| owner_id | BIGINT UNSIGNED | FK -> users.id, ON DELETE CASCADE | Creator, top of the tree |
| name | VARCHAR(100) | NOT NULL | Team name |
| company_name | VARCHAR(100) | NULLABLE | Network marketing company |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

## Table: team_members

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| team_id | BIGINT UNSIGNED | FK -> teams.id, ON DELETE CASCADE | |
| user_id | BIGINT UNSIGNED | FK -> users.id, ON DELETE CASCADE | |
| sponsor_id | BIGINT UNSIGNED | FK -> users.id, NULLABLE, ON DELETE SET NULL | Upline (whose code was used). Null for the owner |
| invite_code | VARCHAR(8) | UNIQUE | This member's own code; joining with it makes them the sponsor |
| path | VARCHAR(1000) | NOT NULL | User ids from the top down to this member, e.g. `/3/12/45/`. Downline = rows whose path starts with this path |
| joined_at | TIMESTAMP | | |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

**Indexes:** UNIQUE (team_id, user_id), (team_id, sponsor_id)

---

## Table: team_builder_targets

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| user_id | BIGINT UNSIGNED | FK -> users.id, UNIQUE, ON DELETE CASCADE | One weekly target per user |
| set_by | BIGINT UNSIGNED | FK -> users.id, NULLABLE, ON DELETE SET NULL | The user or an upline; the latest save wins |
| plans_per_week | SMALLINT UNSIGNED | DEFAULT 0 | 0 = no target |
| follow_ups_per_week | SMALLINT UNSIGNED | DEFAULT 0 | 0 = no target |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

## Table: team_announcements

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| team_id | BIGINT UNSIGNED | FK -> teams.id, ON DELETE CASCADE | |
| user_id | BIGINT UNSIGNED | FK -> users.id, ON DELETE CASCADE | Author; readers are everyone below them (path-based) |
| message | VARCHAR(1000) | NOT NULL | |
| link | VARCHAR(500) | NULLABLE | Training video, meeting or document |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

**Indexes:** (team_id, created_at)

---

## Table: prospects

People the user shows the business plan to. Uplines may later see names, statuses and follow-up dates of their downline's prospects, but never phone numbers or notes.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| user_id | BIGINT UNSIGNED | FK -> users.id, ON DELETE CASCADE | Owner |
| team_id | BIGINT UNSIGNED | FK -> teams.id, NULLABLE, ON DELETE SET NULL | Which team/company it is for |
| name | VARCHAR(100) | NOT NULL | |
| phone | VARCHAR(20) | NULLABLE | |
| source | VARCHAR(100) | NULLABLE | Where the user met them |
| status | ENUM('new', 'plan_shown', 'follow_up', 'joined', 'not_interested') | DEFAULT 'new' | |
| next_follow_up_at | DATETIME | NULLABLE | Cleared when the status is joined or not_interested |
| joined_at | TIMESTAMP | NULLABLE | Set when the status becomes joined |
| note | VARCHAR(500) | NULLABLE | |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | Bumped on every logged activity (list order) |

**Indexes:** (user_id, status), (user_id, next_follow_up_at)

---

## Table: prospect_activities

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| prospect_id | BIGINT UNSIGNED | FK -> prospects.id, ON DELETE CASCADE | |
| type | ENUM('call', 'plan_shown', 'follow_up', 'meeting', 'note') | NOT NULL | |
| note | VARCHAR(500) | NULLABLE | |
| done_at | DATETIME | NOT NULL | When it happened |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

**Indexes:** (prospect_id, done_at)

---

## Entity Relationship Diagram (Text)

```
users (1) ----< (M) group_members (M) >---- (1) groups
  |                                              |
  |                                              |
  +----< expenses >----+                         |
  |      (personal)    |                         |
  |                    |                         |
  +----< expenses (group) >---------------------+
  |         |
  |         +----< expense_splits
  |
  +----< settlements (from_user)
  +----< settlements (to_user)
  +----< blog_posts (author)
  +----< todos
  +----< incomes
  +----< remember_items ----< remember_entries
  +----< ledger_people ----< ledger_entries
  +----< teams (owner) ----< team_members (also user_id, sponsor_id -> users)
  +----< prospects ----< prospect_activities
  +----< team_builder_targets (user_id, set_by)
  +----< team_announcements (author) >---- teams

categories (1) ----< (M) expenses
```
