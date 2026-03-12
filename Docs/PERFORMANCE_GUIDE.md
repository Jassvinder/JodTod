# Performance Optimization Guide
## Laravel (Backend) + Vue.js / Inertia.js (Frontend)

> Ye guide **Laravel + Inertia.js + Vue.js project** ke liye hai.
> Iska goal: Har page fast load ho, DB pe kam pressure pade, aur scalable architecture bane.

---

## Table of Contents
1. [Backend — Laravel](#1-backend--laravel)
   - 1.1 [Caching Strategy](#11-caching-strategy)
   - 1.2 [Eager Loading & N+1 Queries](#12-eager-loading--n1-queries)
   - 1.3 [Database Indexes](#13-database-indexes)
   - 1.4 [Query Optimization](#14-query-optimization)
   - 1.5 [Rate Limiting](#15-rate-limiting)
   - 1.6 [HTTP Response Headers](#16-http-response-headers)
   - 1.7 [Middleware Optimization](#17-middleware-optimization)
   - 1.8 [Pagination](#18-pagination)
   - 1.9 [Queue Jobs](#19-queue-jobs)
2. [Frontend — Vue.js / Inertia.js](#2-frontend--vuejs--inertiajs)
   - 2.1 [Inertia Partial Reloads](#21-inertia-partial-reloads)
   - 2.2 [Client-side Caching (Composable)](#22-client-side-caching-composable)
   - 2.3 [Image Optimization](#23-image-optimization)
   - 2.4 [Code Splitting & Lazy Loading](#24-code-splitting--lazy-loading)
   - 2.5 [Bundle Optimization (Vite)](#25-bundle-optimization-vite)
3. [Database](#3-database)
   - 3.1 [Index Strategy](#31-index-strategy)
   - 3.2 [Query Patterns](#32-query-patterns)
4. [Infrastructure](#4-infrastructure)
   - 4.1 [Cache Store Selection](#41-cache-store-selection)
   - 4.2 [Environment-wise Settings](#42-environment-wise-settings)
5. [Performance Checklist](#5-performance-checklist)
6. [Monitoring & Debugging](#6-monitoring--debugging)

---

## 1. Backend — Laravel

### 1.1 Caching Strategy

#### Rule: Har wo data jo rarely change ho, usse cache karo.

**Categories aur TTL (Time-To-Live):**

| Data Type | TTL | Cache Key Pattern | Flush When |
|-----------|-----|-------------------|------------|
| Countries | 7 days | `locations:countries` | Never (manual) |
| States by country | 7 days | `locations:states:{countryId}` | Never (manual) |
| Cities by state | 7 days | `locations:cities:{stateId}` | Never (manual) |
| Specializations | 24 hours | `specializations:all` | New specialization added |
| Home page data | 30 minutes | `home:featured` | Doctor/Hospital updated |
| Home page stats | 1 hour | `home:stats` | New user/doctor registered |
| Filter options | 1 hour | `filters:{type}:{params}` | New record added |
| Blog categories | 2 hours | `blog:categories` | Category added/edited |
| FAQs | 6 hours | `faqs:all` | FAQ updated |
| Static pages | 12 hours | `page:{slug}` | Page edited |
| Product categories | 2 hours | `products:categories` | Category changed |

**Implementation Pattern:**

```php
// Controller mein aise use karo:
use Illuminate\Support\Facades\Cache;

// Simple cache (immutable data)
$countries = Cache::remember('locations:countries', now()->addDays(7), function () {
    return DB::table('countries')
        ->where('is_active', true)
        ->orderByRaw("CASE WHEN iso2 = 'IN' THEN 0 ELSE 1 END")
        ->orderBy('name')
        ->select('id', 'name', 'iso2', 'phone_code')
        ->get();
});

// Cache with tags (easy invalidation)
$posts = Cache::tags(['blog'])->remember('blog:posts:page:1', now()->addHours(1), function () {
    return BlogPost::published()->paginate(12);
});

// Cache flush on model change (Observer ya Event se)
Cache::forget('blog:categories');
Cache::tags(['blog'])->flush();
```

**Cache Keys Convention:**
```
{module}:{entity}:{identifier}
Examples:
  home:stats
  locations:countries
  locations:states:101       (India ke states)
  blog:categories
  doctor:profile:dr-john-doe (slug-based)
  filters:doctors:pune
```

**Cache Invalidation — Model Observer Pattern:**
```php
// app/Observers/DoctorObserver.php
class DoctorObserver {
    public function saved(Doctor $doctor): void {
        Cache::forget('home:featured');
        Cache::forget('home:stats');
        Cache::forget("filters:doctors:*");  // wildcard flush with Redis
    }
}

// AppServiceProvider mein register karo:
Doctor::observe(DoctorObserver::class);
```

---

### 1.2 Eager Loading & N+1 Queries

#### Rule: Kabhi bhi loop ke andar DB query mat karo.

**N+1 Problem — Wrong:**
```php
// ❌ BAD - 1 query for doctors + N queries for each doctor's specialty
$doctors = Doctor::all();
foreach ($doctors as $doctor) {
    echo $doctor->specialization->name;  // N+1 here!
}
```

**Correct Approach:**
```php
// ✅ GOOD - Single join query
$doctors = Doctor::with('specialization:id,name')->get();
```

**Common Patterns:**

```php
// 1. Nested relationships
Doctor::with([
    'user:id,email',
    'specialization:id,name',
    'city:id,name',
    'state:id,name',
])->paginate(12);

// 2. Count without loading all records (withCount instead of count in loop)
// ❌ BAD
$hospitals->each(function($hospital) {
    $hospital->doctor_count = $hospital->doctors()->count();  // N queries
});
// ✅ GOOD
$hospitals = Hospital::withCount('doctors')->get();

// 3. Conditional eager loading
$query->with(['reviews' => function($q) {
    $q->approved()->latest()->limit(3)->select('id', 'reviewable_id', 'rating', 'comment');
}]);

// 4. Polymorphic relationships
$reviews = Review::with('reviewable')->get();  // loads polymorphic relation

// 5. Has() vs Exists subquery
// ❌ BAD
$doctors = Doctor::all()->filter(fn($d) => $d->reviews->count() > 0);
// ✅ GOOD
$doctors = Doctor::has('reviews')->get();

// 6. Avoid select * — always specify columns
// ❌ BAD
Doctor::with('user')->get();
// ✅ GOOD
Doctor::with('user:id,email,role')->select('id', 'user_id', 'first_name', 'last_name', 'slug')->get();
```

**Debugging N+1 Queries:**
```php
// AppServiceProvider mein (DEVELOPMENT ONLY)
DB::listen(function ($query) {
    if (config('app.debug')) {
        \Log::info($query->sql, $query->bindings);
    }
});

// Ya Debugbar package use karo
// composer require barryvdh/laravel-debugbar --dev
```

---

### 1.3 Database Indexes

#### Rule: Har frequently queried column pe index hona chahiye.

**Index Types:**

```php
// 1. Simple Index — single column search
$table->index('status');
$table->index('created_at');

// 2. Composite Index — multi-column WHERE
// Query: WHERE status = 'published' AND published_at <= NOW()
$table->index(['status', 'published_at']);

// 3. Unique Index
$table->unique('slug');
$table->unique(['user_id', 'reviewable_id', 'reviewable_type']);

// 4. Foreign Key + Index (always together)
$table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
$table->index('doctor_id');  // ya foreignId automatically creates index

// 5. Full-Text Search Index (PostgreSQL)
// Migration mein:
DB::statement("CREATE INDEX idx_doctors_search ON doctors USING gin(to_tsvector('english', first_name || ' ' || last_name))");
```

**Index Strategy — Kab lagao:**

| Condition | Index Needed |
|-----------|-------------|
| `WHERE column = value` | Single index on column |
| `WHERE col1 = x AND col2 = y` | Composite index `(col1, col2)` |
| `ORDER BY col` | Index on col |
| `WHERE status='active' ORDER BY created_at` | Composite `(status, created_at)` |
| Foreign key join | Index on FK column |
| `LIKE 'value%'` | Index works (prefix match) |
| `LIKE '%value%'` | Index useless, use Full-Text |
| Polymorphic `(type, id)` | Composite index on both |

**Index Anti-patterns (Avoid):**
```php
// ❌ Index on boolean with 50/50 distribution — useless
$table->index('is_deleted');  // low cardinality

// ❌ Index on very high cardinality text with LIKE '%x%'
$table->index('description');  // useless for LIKE

// ✅ Partial index is better than full index for filtered queries (PostgreSQL)
DB::statement("CREATE INDEX idx_active_doctors ON doctors(status) WHERE status = 'active'");
```

---

### 1.4 Query Optimization

#### Rule: Jitna kam data lao DB se, utna acha.

**Select Optimization:**
```php
// ❌ Always avoid SELECT *
User::all();

// ✅ Only needed columns
User::select('id', 'email', 'role', 'created_at')->get();

// ✅ API responses ke liye specific columns
$doctors = Doctor::select([
    'id', 'slug', 'first_name', 'last_name', 'profile_image',
    'specialization_id', 'city_id', 'consultation_fee', 'average_rating'
])->with(['specialization:id,name', 'city:id,name'])->paginate(12);
```

**Avoid Duplicate Queries:**
```php
// ❌ BAD — same data queried twice
$count = Review::where('doctor_id', $id)->where('status', 'approved')->count();
$reviews = Review::where('doctor_id', $id)->where('status', 'approved')->paginate(10);

// ✅ GOOD — run paginate only, get total from paginator
$reviews = Review::where('doctor_id', $id)->where('status', 'approved')->paginate(10);
$count = $reviews->total();  // no extra query
```

**Subqueries over multiple queries:**
```php
// ❌ BAD — 2 queries
$categoryIds = BlogCategory::where('is_active', true)->pluck('id');
$posts = BlogPost::whereIn('category_id', $categoryIds)->get();

// ✅ GOOD — 1 query with subquery
$posts = BlogPost::whereHas('category', fn($q) => $q->where('is_active', true))->get();
```

**Chunking for large datasets:**
```php
// ❌ BAD — loads all in memory
$allDoctors = Doctor::all();  // 10k rows in RAM

// ✅ GOOD — chunk processing
Doctor::chunk(500, function ($doctors) {
    foreach ($doctors as $doctor) {
        // process
    }
});

// ✅ GOOD — lazy collection (cursor-based)
Doctor::lazy()->each(function ($doctor) {
    // memory efficient
});
```

**whereIn instead of multiple wheres:**
```php
// ❌ BAD
$cityId1 = City::where('name', 'Mumbai')->value('id');
$cityId2 = City::where('name', 'Delhi')->value('id');
$doctors = Doctor::where('city_id', $cityId1)->orWhere('city_id', $cityId2)->get();

// ✅ GOOD
$cityIds = City::whereIn('name', ['Mumbai', 'Delhi'])->pluck('id');
$doctors = Doctor::whereIn('city_id', $cityIds)->get();
```

---

### 1.5 Rate Limiting

#### Rule: Public APIs ko abuse se bachao.

```php
// routes/api.php mein
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

// AppServiceProvider ya RouteServiceProvider mein define:
RateLimiter::for('public-api', function (Request $request) {
    return Limit::perMinute(60)->by($request->ip());
});

RateLimiter::for('auth', function (Request $request) {
    return Limit::perMinute(10)->by($request->ip());  // Login/register strict
});

RateLimiter::for('upload', function (Request $request) {
    return Limit::perMinute(5)->by($request->user()?->id ?? $request->ip());
});

// Routes pe apply:
Route::middleware('throttle:public-api')->prefix('doctors')->group(function () {
    Route::get('/', [PublicDoctorController::class, 'index']);
});

Route::middleware('throttle:auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});
```

---

### 1.6 HTTP Response Headers

#### Rule: Browser aur CDN ko batao kitna cache karein.

**Middleware for Cache Headers:**
```php
// app/Http/Middleware/PublicApiCacheHeaders.php
class PublicApiCacheHeaders {
    public function handle(Request $request, Closure $next, int $maxAge = 300): Response {
        $response = $next($request);

        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            $response->headers->set('Cache-Control', "public, max-age={$maxAge}, stale-while-revalidate=60");
            $response->headers->set('Vary', 'Accept-Encoding');

            // ETag for conditional requests
            $etag = '"' . md5($response->getContent()) . '"';
            $response->headers->set('ETag', $etag);

            if ($request->headers->get('If-None-Match') === $etag) {
                return response('', 304);  // Not Modified
            }
        }

        return $response;
    }
}

// Routes pe apply:
Route::middleware(['public-cache:600'])->prefix('blog')->group(fn() => [...]);
Route::middleware(['public-cache:3600'])->get('/faqs', [...]);
```

---

### 1.7 Middleware Optimization

#### Rule: Middleware sirf wahan lagao jahan zaroorat ho.

```php
// ✅ Global middleware me sirf essential cheezein
// TrimStrings, ConvertEmptyStringsToNull — fine
// Authentication — only on protected routes

// ❌ BAD — auth middleware on public routes
Route::middleware('auth:sanctum')->get('/blog', [...]);  // Blog public hai!

// ✅ GOOD — public routes without auth
Route::prefix('blog')->group(function () {
    Route::get('/', [PublicBlogController::class, 'index']);
});

// Auth-optional routes (logged in user ko extra data de sako)
Route::middleware('auth:sanctum')->withoutMiddleware('...')...

// Avoid redundant middleware
$middleware->alias([
    'role' => RoleMiddleware::class,
]);
```

---

### 1.8 Pagination

#### Rule: Kabhi bhi full dataset return mat karo.

```php
// ✅ Paginate har listing
$results = Doctor::paginate(12);
$results = Doctor::simplePaginate(20);  // faster — no total count

// ✅ Cursor pagination for infinite scroll (very fast on large datasets)
$results = Doctor::orderBy('id')->cursorPaginate(20);

// ✅ Paginator response format
return response()->json([
    'status' => 'success',
    'data' => $results->items(),
    'meta' => [
        'current_page' => $results->currentPage(),
        'last_page' => $results->lastPage(),
        'total' => $results->total(),
        'per_page' => $results->perPage(),
    ],
]);
```

---

### 1.9 Queue Jobs

#### Rule: Heavy/slow operations ko background me karo.

```php
// Queue mein bhejo (main request slow nahi hoga):
SendEmailJob::dispatch($user);
UpdateDoctorRatingJob::dispatch($doctor);
GenerateInvoiceJob::dispatch($order);
ProcessImageUploadJob::dispatch($imagePath);
SendNotificationJob::dispatch($notification);

// Horizon ya supervisor se worker chalao production mein
// php artisan queue:work
```

---

## 2. Frontend — Vue.js / Inertia.js

> **Inertia.js ke saath Vue.js use karne ka matlab hai:** Data Laravel controller se props ke roop mein aata hai — koi separate API calls nahi hote. Isliye frontend caching strategy thodi different hai.

---

### 2.1 Inertia Partial Reloads

#### Rule: Poora page reload karne ki jagah sirf zaroori props reload karo.

**Problem:** Inertia by default saare props reload karta hai — jab sirf ek section update karna ho toh ye wasteful hai.

**Partial Reload — sirf specific props:**
```javascript
// ✅ Sirf 'expenses' prop reload karo, baaki same rahein
router.reload({ only: ['expenses'] });

// ✅ Filter apply karne pe partial reload
router.get(route('expenses.index'), filters, {
    preserveState: true,    // scroll position aur form state save
    preserveScroll: true,
    only: ['expenses'],     // sirf ye prop server se dobara fetch hoga
});

// ✅ Delete ke baad sirf list refresh karo
const deleteExpense = (id) => {
    router.delete(route('expenses.destroy', id), {
        preserveScroll: true,
        only: ['expenses'],
    });
};
```

**`preserveState` kab use karein:**
```javascript
// ✅ Filter/search ke saath — form state clear nahi honi chahiye
router.get(url, params, { preserveState: true });

// ❌ Fresh page navigate karte time — state clear honi chahiye
router.visit(route('dashboard'));
```

**Inertia `remember` — form state page reload pe survive kare:**
```javascript
// Vue component mein
import { useRemember } from '@inertiajs/vue3';

const form = useRemember({
    search: '',
    category: 'all',
    dateFrom: '',
    dateTo: '',
}, 'expense-filters');
// Browser back button pe bhi filters restore honge
```

---

### 2.2 Client-side Caching (Composable)

#### Rule: Inertia props Laravel se aate hain — baar baar same Axios calls avoid karo.

**Semi-static data ke liye localStorage cache composable:**
```javascript
// resources/js/composables/useLocalCache.js
export function useLocalCache(key, ttlMinutes = 60) {
    const get = () => {
        try {
            const item = localStorage.getItem(key);
            if (!item) return null;
            const { data, expiry } = JSON.parse(item);
            if (Date.now() > expiry) { localStorage.removeItem(key); return null; }
            return data;
        } catch { return null; }
    };

    const set = (data) => {
        localStorage.setItem(key, JSON.stringify({
            data,
            expiry: Date.now() + ttlMinutes * 60 * 1000,
        }));
    };

    const clear = () => localStorage.removeItem(key);

    return { get, set, clear };
}

// Usage — categories list baar baar fetch nahi hogi
const { get, set } = useLocalCache('categories', 120); // 2 hour TTL
let categories = get();
if (!categories) {
    const res = await axios.get('/api/categories');
    categories = res.data;
    set(categories);
}
```

**Reactive data ke liye `ref` + computed cache:**
```javascript
// resources/js/composables/useGroupBalances.js
import { ref, computed } from 'vue';
import axios from 'axios';

const balancesCache = ref({});  // module-level cache (survives component re-renders)

export function useGroupBalances(groupId) {
    const loading = ref(false);

    const balances = computed(() => balancesCache.value[groupId] ?? null);

    const fetchBalances = async (force = false) => {
        if (balancesCache.value[groupId] && !force) return;  // cache hit
        loading.value = true;
        const res = await axios.get(`/groups/${groupId}/balances`);
        balancesCache.value[groupId] = res.data;
        loading.value = false;
    };

    const invalidate = () => { delete balancesCache.value[groupId]; };

    return { balances, loading, fetchBalances, invalidate };
}
```

**Debounce for search inputs (avoid API spam):**
```javascript
// resources/js/composables/useDebounce.js
import { ref, watch } from 'vue';

export function useDebounce(value, delay = 400) {
    const debouncedValue = ref(value.value);
    let timer;
    watch(value, (newVal) => {
        clearTimeout(timer);
        timer = setTimeout(() => { debouncedValue.value = newVal; }, delay);
    });
    return debouncedValue;
}

// Component mein use:
const search = ref('');
const debouncedSearch = useDebounce(search, 400);

watch(debouncedSearch, (val) => {
    router.get(route('expenses.index'), { search: val }, {
        preserveState: true,
        only: ['expenses'],
    });
});
```

---

### 2.3 Image Optimization

#### Rule: Images web ki sabse badi performance killer hain.

**Lazy loading — below-fold images:**
```html
<!-- ✅ Browser native lazy loading — sab images pe lagao -->
<img
    :src="user.avatar"
    :alt="user.name"
    loading="lazy"
    width="80"
    height="80"
    class="rounded-full object-cover"
/>

<!-- ✅ Above-fold (hero/profile) — eager load karo LCP ke liye -->
<img
    :src="user.avatar"
    :alt="user.name"
    loading="eager"
    fetchpriority="high"
    width="80"
    height="80"
/>
```

**Explicit width/height — CLS (layout shift) rokne ke liye:**
```html
<!-- ❌ BAD — page layout shift hoga jab image load hogi -->
<img :src="expense.receipt" alt="Receipt" class="w-full" />

<!-- ✅ GOOD — space already reserved, no shift -->
<img :src="expense.receipt" alt="Receipt" width="400" height="300" class="w-full h-auto" />
```

**Vue reusable Avatar component with initials fallback:**
```vue
<!-- resources/js/Components/Shared/Avatar.vue -->
<template>
    <div class="relative inline-block">
        <img
            v-if="src && !imgError"
            :src="src"
            :alt="alt"
            :width="size"
            :height="size"
            loading="lazy"
            class="rounded-full object-cover"
            :class="`w-${sizeClass} h-${sizeClass}`"
            @error="imgError = true"
        />
        <div
            v-else
            class="rounded-full bg-indigo-500 flex items-center justify-center text-white font-semibold"
            :class="`w-${sizeClass} h-${sizeClass}`"
        >
            {{ initials }}
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
const props = defineProps({ src: String, alt: String, name: String, size: { default: 40 } });
const imgError = ref(false);
const sizeClass = computed(() => props.size <= 32 ? '8' : props.size <= 48 ? '12' : '16');
const initials = computed(() => props.name?.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase() ?? '?');
</script>
```

**Blade pages mein WebP format aur lazy load:**
```html
<!-- resources/views/components/blade/ mein use karo -->
<picture>
    <source srcset="{{ asset('images/hero.webp') }}" type="image/webp">
    <img src="{{ asset('images/hero.jpg') }}" alt="JodTod App" width="1200" height="630"
         loading="eager" fetchpriority="high" class="w-full h-auto">
</picture>

<!-- Blog post images — lazy load -->
<img src="{{ $post->featured_image }}" alt="{{ $post->title }}"
     loading="lazy" width="800" height="450" class="w-full h-auto rounded-xl">
```

---

### 2.4 Code Splitting & Lazy Loading

#### Rule: Heavy components tab hi load karo jab zaroorat ho.

**Vue `defineAsyncComponent` — heavy components ke liye:**
```javascript
// ✅ Charts — Chart.js heavy hai, sirf chart page pe load ho
import { defineAsyncComponent } from 'vue';

const PieChart = defineAsyncComponent({
    loader: () => import('@/Components/Charts/PieChart.vue'),
    loadingComponent: { template: '<div class="h-64 bg-gray-200 dark:bg-gray-700 animate-pulse rounded-xl"></div>' },
    delay: 200,       // 200ms baad loading component dikhao
    timeout: 10000,
});

const BarChart = defineAsyncComponent(() => import('@/Components/Charts/BarChart.vue'));

// ✅ TipTap Rich Text Editor — sirf admin pages pe load ho
const RichTextEditor = defineAsyncComponent(() => import('@/Components/RichTextEditor.vue'));

// ✅ Modals — tabhi load karo jab user click kare
const ExpenseFormModal = defineAsyncComponent(() => import('@/Components/Expenses/ExpenseForm.vue'));
```

**`v-if` ke saath lazy load — modal/dialog pattern:**
```vue
<template>
    <!-- ✅ Modal sirf tab mount hoga jab showModal = true -->
    <ExpenseFormModal
        v-if="showModal"
        :group="group"
        @close="showModal = false"
        @saved="handleSaved"
    />

    <button @click="showModal = true">Add Expense</button>
</template>

<script setup>
import { ref, defineAsyncComponent } from 'vue';
const ExpenseFormModal = defineAsyncComponent(() => import('@/Components/Expenses/ExpenseForm.vue'));
const showModal = ref(false);
</script>
```

**Inertia page-level code splitting — automatic:**
```javascript
// app.js mein Inertia setup karo — har Page apna chunk hoga
import { createInertiaApp } from '@inertiajs/vue3';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue');  // Vite auto code-splits
        return pages[`./Pages/${name}.vue`]();
    },
    // ...
});
// Har Pages/*.vue file apna separate JS chunk banega — unneeded code load nahi hoga
```

---

### 2.5 Bundle Optimization (Vite)

#### Rule: JS bundle chhota rakho — unused code ship mat karo.

**vite.config.js optimizations:**
```javascript
// vite.config.js
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({ input: ['resources/css/app.css', 'resources/js/app.js'], refresh: true }),
        vue({ template: { transformAssetUrls: { base: null, includeAbsolute: false } } }),
    ],
    build: {
        rollupOptions: {
            output: {
                // Vendor chunks manually split karo — better caching
                manualChunks: {
                    'vue-core': ['vue', '@inertiajs/vue3'],
                    'charts': ['chart.js'],
                    'tiptap': ['@tiptap/vue-3', '@tiptap/starter-kit'],
                },
            },
        },
        chunkSizeWarningLimit: 500,  // 500KB se bada chunk ho toh warn karo
    },
});
```

**Named imports — tree-shaking ke liye:**
```javascript
// ✅ Sirf jo chahiye wo import karo
import { ref, computed, watch, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

// ❌ Full library import — unused code bhi bundle mein aata hai
import * as Vue from 'vue';

// ✅ Date — dayjs use karo (2KB), moment.js avoid karo (67KB)
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
dayjs.extend(relativeTime);
// Usage: dayjs(expense.created_at).fromNow()  → "2 hours ago"

// ✅ Native JS debounce — lodash import karne ki zaroorat nahi
const debounce = (fn, delay) => {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), delay); };
};
```

**Bundle size analyze karo:**
```bash
# Bundle analyzer install karo
npm install --save-dev rollup-plugin-visualizer

# vite.config.js mein add karo (dev only):
import { visualizer } from 'rollup-plugin-visualizer';
plugins: [
    // ...existing plugins
    visualizer({ open: true, filename: 'dist/stats.html' }),  // build ke baad browser mein khuleg
]

# Build karo aur analyze karo:
npm run build
# stats.html automatically browser mein khulegi
```

**Unused Tailwind classes purge (automatic in production):**
```javascript
// Tailwind v4 mein Vite plugin automatic purging karta hai
// Sirf ensure karo ki content paths sahi hain:
// vite.config.js ya tailwind.config.js mein:
content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
    './resources/js/**/*.js',
]
// Production build mein unused CSS automatically remove ho jaati hai
```

---

## 3. Database

### 3.1 Index Strategy

**When to add indexes (PostgreSQL/MySQL):**

```sql
-- 1. Single column filter
CREATE INDEX idx_doctors_status ON doctors(status);
CREATE INDEX idx_doctors_city ON doctors(city_id);

-- 2. Composite — most selective column first
CREATE INDEX idx_reviews_composite ON reviews(status, reviewable_type, reviewable_id, created_at DESC);

-- 3. Partial index — only index useful rows
CREATE INDEX idx_active_doctors ON doctors(status) WHERE status = 'active';
CREATE INDEX idx_published_posts ON blog_posts(published_at) WHERE status = 'published';

-- 4. Full-Text Search (PostgreSQL)
CREATE INDEX idx_doctors_fts ON doctors USING gin(
    to_tsvector('english', first_name || ' ' || coalesce(last_name, ''))
);

-- 5. JSONB indexes
CREATE INDEX idx_tags ON blog_posts USING gin(tags);
```

**When NOT to add indexes:**
- Columns with very low cardinality (boolean `is_deleted` with 95% false)
- Columns that are rarely queried
- Small tables (< 1000 rows) — full scan is faster
- Frequently updated columns (index maintenance overhead)

---

### 3.2 Query Patterns

**PostgreSQL specific optimizations:**

```php
// 1. ILIKE for case-insensitive (PostgreSQL only — index-friendly)
->where('name', 'ILIKE', "%{$search}%")

// 2. JSONB query
->whereRaw("tags::jsonb @> ?", [json_encode([$tag])])  // containment operator

// 3. Full-text search
->whereRaw("to_tsvector('english', first_name || ' ' || last_name) @@ plainto_tsquery(?)", [$query])

// 4. Window functions for rankings
DB::select("SELECT *, RANK() OVER (PARTITION BY city_id ORDER BY average_rating DESC) as rank FROM doctors")

// 5. Avoid N+1 with subquery
Doctor::selectSub(
    Review::selectRaw('COUNT(*)')->whereColumn('reviewable_id', 'doctors.id'),
    'reviews_count'
)->get();
```

---

## 4. Infrastructure

### 4.1 Cache Store Selection

**Hierarchy (fastest to slowest):**

```
Redis > Memcached > File > Database > Array
```

| Store | Speed | Setup | Use When |
|-------|-------|-------|----------|
| `redis` | ⚡⚡⚡⚡ | Redis server needed | Production, high traffic |
| `file` | ⚡⚡⚡ | Zero setup | Development, small apps |
| `database` | ⚡⚡ | DB table needed | When Redis not available |
| `array` | ⚡⚡⚡⚡⚡ | No setup | Tests only (in-memory) |

**Environment wise .env:**
```bash
# Development
CACHE_STORE=file

# Production with Redis
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=your_password

# Production without Redis
CACHE_STORE=file
```

**Laravel Cache config (config/cache.php):**
```php
'default' => env('CACHE_STORE', 'file'),  // default file, not database
```

---

### 4.2 Environment-wise Settings

**Development .env:**
```bash
APP_DEBUG=true
CACHE_STORE=file
QUEUE_CONNECTION=sync    # immediate job execution
LOG_LEVEL=debug
```

**Production .env:**
```bash
APP_DEBUG=false
CACHE_STORE=redis
QUEUE_CONNECTION=redis
LOG_LEVEL=error
OPCACHE_ENABLE=1
```

**PHP OPcache (php.ini for production):**
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0  ; production mein 0 karo
```

---

## 5. Performance Checklist

### Backend Checklist

- [ ] **Caching**: Static/semi-static data cached with appropriate TTL
- [ ] **Eager Loading**: No N+1 queries in any controller (use `with()`)
- [ ] **Select Columns**: `select(['col1', 'col2'])` — no SELECT *
- [ ] **Pagination**: Har listing paginated (max 20 per page)
- [ ] **Indexes**: Foreign keys, filter columns, composite indexes in place
- [ ] **Rate Limiting**: Public APIs throttled (60 req/min per IP)
- [ ] **Queue**: Emails, notifications, image processing in background jobs
- [ ] **Cache Store**: `file` (dev) / `redis` (prod) — NOT `database`
- [ ] **No Duplicate Queries**: Same query nahi chalti ek request mein do baar
- [ ] **Chunk Large Operations**: 500+ records ke liye `chunk()` use karo

### Frontend Checklist (Vue.js + Inertia.js)

- [ ] **Inertia Partial Reloads**: Filter/search pe `only: ['propName']` use ho raha hai, poora page reload nahi ho raha
- [ ] **`preserveState`**: Search/filter requests pe `preserveState: true` set hai
- [ ] **Lazy Images**: Below-fold images pe `loading="lazy"` lagaya hai
- [ ] **Above-fold Images**: Hero/profile images pe `loading="eager" fetchpriority="high"` hai
- [ ] **Explicit Dimensions**: Saari images pe `width` aur `height` set hai (CLS rokne ke liye)
- [ ] **Avatar Fallback**: Images ke liye initials fallback handle ho raha hai (`@error`)
- [ ] **Async Components**: Charts, modals, editors `defineAsyncComponent` se lazy load hain
- [ ] **`v-if` Modals**: Modals `v-show` ki jagah `v-if` se conditionally mount ho rahe hain
- [ ] **Debounced Search**: Search input pe `useDebounce` composable use ho raha hai
- [ ] **Named Imports**: `import { ref, computed } from 'vue'` — no wildcard imports
- [ ] **dayjs over moment**: Date formatting ke liye `dayjs` use ho raha hai (moment.js nahi)
- [ ] **Bundle Split**: `vite.config.js` mein `manualChunks` se vendor chunks alag hain
- [ ] **No Memory Leaks**: `onUnmounted` mein event listeners aur intervals clear ho rahe hain

### Database Checklist

- [ ] **Foreign Keys Indexed**: Har FK column pe index hai
- [ ] **Composite Indexes**: Multi-column WHERE/ORDER BY ke liye composite index
- [ ] **Full-Text**: Search functionality ke liye FTS index (GIN)
- [ ] **Partial Indexes**: Frequently filtered subsets ke liye partial index
- [ ] **No Low-Cardinality Indexes**: Boolean columns pe index avoid karo

---

## 6. Monitoring & Debugging

### Query Monitoring (Laravel)

```php
// .env mein DEBUG mode pe query log dekho:
DB::listen(function ($query) {
    Log::channel('query')->info($query->sql, [
        'bindings' => $query->bindings,
        'time' => $query->time . 'ms'
    ]);
});

// Slow query threshold alert (>500ms)
DB::listen(function ($query) {
    if ($query->time > 500) {
        Log::warning('SLOW QUERY: ' . $query->time . 'ms', ['sql' => $query->sql]);
    }
});
```

### Cache Hit Rate Monitor

```php
// Controller ya middleware mein:
$cacheKey = 'locations:countries';
$hit = Cache::has($cacheKey);
Log::info('Cache ' . ($hit ? 'HIT' : 'MISS') . ': ' . $cacheKey);
```

### Tools

| Tool | Purpose | Install |
|------|---------|---------|
| Laravel Debugbar | Query/time profiling (dev only) | `composer require barryvdh/laravel-debugbar --dev` |
| Laravel Telescope | Full observability — requests, queries, jobs | `composer require laravel/telescope --dev` |
| Rollup Visualizer | Vite bundle size analysis | `npm install rollup-plugin-visualizer --save-dev` |
| Vue DevTools | Component state, Inertia props inspect | Browser extension |
| Chrome DevTools | Network/LCP/CLS/JS profiling | Built-in |
| PageSpeed Insights | Core Web Vitals | Google tool |

### Core Web Vitals Targets

| Metric | Good | Needs Work | Poor |
|--------|------|------------|------|
| LCP (Largest Contentful Paint) | < 2.5s | 2.5-4s | > 4s |
| FID (First Input Delay) | < 100ms | 100-300ms | > 300ms |
| CLS (Cumulative Layout Shift) | < 0.1 | 0.1-0.25 | > 0.25 |
| TTFB (Time to First Byte) | < 800ms | 800ms-1.8s | > 1.8s |

---

## Quick Reference — Most Common Fixes

```php
// 1. N+1 Fix
// Before: Doctor::all(); // then loop with $doctor->specialization
// After:
Doctor::with('specialization:id,name')->select('id', 'slug', 'first_name', 'specialization_id')->get();

// 2. Add Cache
$data = Cache::remember('key', now()->addHours(1), fn() => DB::table(...)->get());

// 3. Paginate
$results = Model::query()->paginate(12);

// 4. Rate Limit (routes/api.php)
Route::middleware('throttle:60,1')->group(fn() => [...]);

// 5. Index (migration)
$table->index(['status', 'created_at']);
```

---

*Last Updated: 2026-03-12*
*Applicable to: Laravel 11+, Vue 3+, Inertia.js 2+, Vite 5+*
