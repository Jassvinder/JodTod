# JodTod - Project Instructions for Claude

## Project Overview
JodTod is an expense tracker & splitter app. Users can track personal expenses and split group expenses with settlement calculation ("kaun kisko kitna dega").

## Tech Stack
- **Backend:** Laravel (PHP)
- **Database:** MySQL
- **Frontend (SEO pages):** Laravel Blade (landing, blog, free tools)
- **Frontend (App pages):** Inertia.js + Vue.js (dashboard, expenses, groups)
- **Styling:** Tailwind CSS
- **PWA:** Yes (installable, offline support)
- **Responsive:** Yes (mobile + tablet + desktop)
- **Mobile App:** Future (React Native / Flutter)

## Documentation Files (MUST READ before working)
All project documentation is in the `Docs/` folder:

| File | Purpose |
|------|---------|
| `Docs/FLOWCHART.md` | Complete app flow - user journey, screens, navigation |
| `Docs/INSTRUCTIONS.md` | Detailed feature-wise implementation instructions |
| `Docs/TASKS.md` | Task list - pending, in-progress, completed tracking |
| `Docs/PROGRESS.md` | Development progress log - what's done, what's next |
| `Docs/DATABASE_SCHEMA.md` | Full database schema with all tables and relations |
| `Docs/API_ENDPOINTS.md` | All REST API endpoints with request/response format |
| `Docs/SETTLEMENT_LOGIC.md` | Core settlement calculation algorithm with examples |
| `Docs/SEO_STRATEGY.md` | SEO plan, blog strategy, free tools for traffic |

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
3. Move completed tasks to "Completed" section in `Docs/TASKS.md`
4. Follow the database schema exactly as defined in `Docs/DATABASE_SCHEMA.md`
5. Follow API structure as defined in `Docs/API_ENDPOINTS.md`
6. Settlement logic MUST follow the algorithm in `Docs/SETTLEMENT_LOGIC.md`
7. SEO pages use Blade, App pages use Inertia.js + Vue.js
8. All pages must be responsive (mobile-first approach)
9. PWA setup required for installability and offline support
10. ZERO code duplication - every reusable section MUST be a component
11. Layouts handle common structure (header, footer, sidebar, nav)
12. One component per responsibility - reuse everywhere via props
