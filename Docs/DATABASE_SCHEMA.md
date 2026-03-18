# JodTod - Database Schema

---

## Table: users

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | |
| name | VARCHAR(100) | NOT NULL | |
| email | VARCHAR(255) | NOT NULL, UNIQUE | |
| email_verified_at | TIMESTAMP | NULLABLE | |
| password | VARCHAR(255) | NULLABLE (Google login) | |
| role | ENUM('admin', 'user') | DEFAULT 'user' | App-level role (admin or regular user) |
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
| title | VARCHAR(255) | NOT NULL | Task title |
| priority | ENUM('low', 'medium', 'high') | DEFAULT 'medium' | |
| due_date | DATE | NULLABLE | Optional deadline |
| is_completed | BOOLEAN | DEFAULT false | |
| completed_at | TIMESTAMP | NULLABLE | When marked as done |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

**Indexes:** (user_id, is_completed), due_date

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

categories (1) ----< (M) expenses
```
