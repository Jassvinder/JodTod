const CACHE_NAME = 'jodtod-v1';
const STATIC_CACHE = 'jodtod-static-v1';
const DYNAMIC_CACHE = 'jodtod-dynamic-v1';

// Static assets to pre-cache
const PRECACHE_URLS = [
    '/offline',
];

// Install event - pre-cache essential resources
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => cache.addAll(PRECACHE_URLS))
            .then(() => self.skipWaiting())
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== STATIC_CACHE && name !== DYNAMIC_CACHE)
                    .map((name) => caches.delete(name))
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch event - caching strategies
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') return;

    // Skip external requests
    if (url.origin !== location.origin) return;

    // Skip admin routes
    if (url.pathname.startsWith('/admin')) return;

    // API/Inertia requests - Network First
    if (request.headers.get('X-Inertia') || url.pathname.startsWith('/api')) {
        event.respondWith(networkFirst(request));
        return;
    }

    // Static assets (JS, CSS, images, fonts) - Cache First
    if (isStaticAsset(url.pathname)) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // HTML pages - Network First with offline fallback
    event.respondWith(networkFirst(request));
});

// Network First strategy
async function networkFirst(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await caches.match(request);
        if (cached) return cached;

        // Return offline page for navigation requests
        if (request.mode === 'navigate') {
            const offlinePage = await caches.match('/offline');
            if (offlinePage) return offlinePage;
        }

        return new Response('Offline', { status: 503, statusText: 'Offline' });
    }
}

// Cache First strategy
async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;

    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        return new Response('', { status: 503 });
    }
}

// Check if the request is for a static asset
function isStaticAsset(pathname) {
    return /\.(js|css|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$/i.test(pathname)
        || pathname.startsWith('/build/');
}

// Background sync for offline expenses (future enhancement)
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-expenses') {
        event.waitUntil(syncOfflineExpenses());
    }
});

async function syncOfflineExpenses() {
    // Future: Read from IndexedDB and POST to server
    // For now, this is a placeholder for background sync
}

// Push notification support (future enhancement)
self.addEventListener('push', (event) => {
    if (!event.data) return;

    const data = event.data.json();
    event.waitUntil(
        self.registration.showNotification(data.title || 'JodTod', {
            body: data.body || '',
            icon: '/icons/icon-192x192.png',
            badge: '/icons/icon-72x72.png',
            data: data.url ? { url: data.url } : {},
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data?.url || '/dashboard';
    event.waitUntil(clients.openWindow(url));
});
