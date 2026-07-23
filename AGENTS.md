# JodTod - Project Instructions for Claude

Read this file before making changes in this repository.
The standard agent instruction file name for this project is `AGENTS.md`.

## Language Rules (STRICT)

- **Chat with user:** Hinglish (Hindi + English mix)
- **Code, comments, variable names:** English ONLY
- **UI content, labels, buttons:** English ONLY
- **Documentation files (Docs/):** English ONLY
- **Git commit messages:** English ONLY
- NO Hindi/Hinglish anywhere in the codebase. All project content must be in English.

## Project Overview

JodTod is an expense tracker & splitter app. Users can track personal expenses and split group expenses with settlement calculation.
AND manage the daily and future tasks and assign any tasks to others and daily tasks reminders.

## Tech Stack

- **Backend:** Laravel (PHP)
- **Database:** MySQL
- **Frontend (SEO pages):** Laravel Blade (landing, blog, free tools)
- **Frontend (App pages):** Inertia.js + Vue.js (dashboard, expenses, groups)
- **Styling:** Tailwind CSS
- **PWA:** Yes (installable, offline support)
- **Responsive:** Yes (mobile + tablet + desktop)
- **Mobile App:** (React Native) app folder = ../JodTodApp OR D:\Development\Projects\JodTodApp

## Source of Truth

- Backend/web source of truth: this repository, `D:\Development\Projects\JodTod`.
- Backend/web documentation source of truth: `Docs/` in this repository.
- Mobile app source of truth: `D:\Development\Projects\JodTodApp`.
- Mobile documentation source of truth: `D:\Development\Projects\JodTodApp\Docs`.
- Do not treat copied, archived, generated, or error-log files as active documentation unless the user explicitly says so.

## Cross-Repository Work Rule

- This repository is the backend and web app source of truth.
- If a task requires mobile app changes, first switch to `D:\Development\Projects\JodTodApp` and read that repository's `AGENTS.md`.
- Follow the target repository's `AGENTS.md`, `Docs/TASKS.md`, and `Docs/PROGRESS.md` before editing files there.
- Keep backend documentation in this repository and mobile documentation in the mobile app repository, except for explicit cross-references.

## Before Editing Checklist

1. Read this `AGENTS.md`.
2. Read the relevant files in `Docs/`.
3. Check `Docs/TASKS.md` for current task status.
4. Inspect the existing implementation before changing code.
5. Keep the change scoped to the requested task.
6. Run the relevant verification command when available.
7. Update `Docs/TASKS.md` and `Docs/PROGRESS.md` after completing a task.

## Screenshot Reference Rule

- If the user asks to check, inspect, compare, or use a screenshot, check `Docs/Screenshots/` first before asking the user for another image. If this repository uses `Docs/screenshots/`, use that folder.
- Treat screenshot files as reference material only; do not modify or delete them unless the user explicitly asks.

## Sensitive Files

- Do not commit or expose `.env`, `.env*.local`, database dumps, private keys, API keys, OAuth secrets, payment credentials, signing credentials, or generated credential payloads.
- Treat files under screenshots, archive, mobile-copy, and generated log folders as reference material only, not active project instructions.
- If a task requires changing secrets or credentials, confirm the exact requirement before editing.

## Documentation Files (MUST READ before working)

All project documentation is in the `Docs/` folder:
Use the files listed below as active documentation. Other files or folders under `Docs/` are reference material unless explicitly listed here or requested by the user.

| File                       | Purpose                                               |
| -------------------------- | ----------------------------------------------------- |
| `Docs/FLOWCHART.md`        | Complete app flow - user journey, screens, navigation |
| `Docs/INSTRUCTIONS.md`     | Detailed feature-wise implementation instructions     |
| `Docs/TASKS.md`            | Task list - pending, in-progress, completed tracking  |
| `Docs/PROGRESS.md`         | Development progress log - what's done, what's next   |
| `Docs/DATABASE_SCHEMA.md`  | Full database schema with all tables and relations    |
| `Docs/API_ENDPOINTS.md`    | All REST API endpoints with request/response format   |
| `Docs/SETTLEMENT_LOGIC.md` | Core settlement calculation algorithm with examples   |
| `Docs/SEO_STRATEGY.md`     | SEO plan, blog strategy, free tools for traffic       |

## Code Architecture Rules (STRICT - NO DUPLICATION)

### Reusable Components - MANDATORY

- NEVER duplicate code. Every repeating UI section MUST be a component.
- If any piece of UI appears on 2+ pages, extract it into a reusable component.

### Blade (SEO/Public Pages) - Component Structure:

```
resources/views/
  layouts/
    app.blade.php          --> Main app layout (Inertia)
    public.blade.php       --> Public pages layout (Blade SEO)
  components/
    blade/
      header.blade.php     --> Public header (nav, logo)
      footer.blade.php     --> Public footer (links, copyright)
      meta-tags.blade.php  --> SEO meta tags component
      breadcrumb.blade.php --> Breadcrumb navigation
  pages/
    public/                --> Landing, about, contact, blog etc.
```

### Vue (App Pages) - Component Structure:

```
resources/js/
  Layouts/
    AppLayout.vue          --> Main app layout (sidebar, header, footer)
    GuestLayout.vue        --> Auth pages layout (login, register)
  Components/
    Shared/
      Header.vue           --> App header (logo, notifications, profile)
      Sidebar.vue          --> Side navigation (desktop)
      BottomNav.vue        --> Bottom navigation (mobile)
      Footer.vue           --> App footer
      Modal.vue            --> Reusable modal
      ConfirmDialog.vue    --> Reusable confirm dialog
      DataTable.vue        --> Reusable table with pagination/filters
      EmptyState.vue       --> "No data" placeholder
      LoadingSpinner.vue   --> Loading indicator
    Expenses/
      ExpenseForm.vue      --> Add/Edit expense form (personal + group reuse)
      ExpenseCard.vue      --> Single expense display card
      ExpenseFilters.vue   --> Date, category, search filters
      CategoryPicker.vue   --> Category selection dropdown
    Groups/
      GroupCard.vue         --> Group list item
      MemberAvatar.vue     --> Member avatar with name
      SplitSelector.vue    --> Equal/Custom/Percentage split UI
      BalanceBadge.vue     --> +/- balance indicator
    Charts/
      PieChart.vue         --> Category breakdown
      BarChart.vue         --> Monthly/weekly trends
  Pages/
    Dashboard.vue
    Expenses/              --> Personal expense pages
    Groups/                --> Group pages
    Profile/               --> Profile pages
```

### Key Principles:

- **Layouts wrap pages** - common structure (header/sidebar/footer) defined once
- **Components are props-driven** - pass data via props, emit events up
- **ExpenseForm.vue** is ONE component used for both personal & group expenses
- **DataTable.vue** is ONE component reused for expense lists, member lists, settlement lists
- **Modal.vue** is ONE component for all modals (add expense, confirm delete, etc.)
- **No inline styles** - Tailwind classes only, use @apply for repeated patterns
- **Form validation** - one composable/utility for all forms

## Key Rules

1. Always check `Docs/TASKS.md` before starting any work
2. Update `Docs/PROGRESS.md` after completing any task
3. Move completed tasks to "Completed" section in `Docs/TASKS.md` with `[X]` and completion date, for example: `- [X] Task title (DD-MM-YYYY)`
4. Follow the database schema exactly as defined in `Docs/DATABASE_SCHEMA.md`
5. Follow API structure as defined in `Docs/API_ENDPOINTS.md`
6. Settlement logic MUST follow the algorithm in `Docs/SETTLEMENT_LOGIC.md`
7. SEO pages use Blade, App pages use Inertia.js + Vue.js
8. All pages must be responsive (mobile-first approach)
9. PWA setup required for installability and offline support
10. ZERO code duplication - every reusable section MUST be a component
11. Layouts handle common structure (header, footer, sidebar, nav)
12. One component per responsibility - reuse everywhere via props
