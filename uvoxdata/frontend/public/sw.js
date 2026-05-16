const CACHE_NAME = 'uvox-offline-v2';

const OFFLINE_ASSETS = [
    '/',
    '/offline.html',
    '/offline/scenarios.json',
    '/offline/intents.json',
];

const OFFLINE_JSON = /^\/offline\/.*\.json$/;

// INSTALL (seguro: no falla si algo no existe)
self.addEventListener('install', (event) => {
    event.waitUntil(safeCache());
    self.skipWaiting();
});

// ACTIVATE
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((k) => k !== CACHE_NAME)
                    .map((k) => caches.delete(k))
            )
        )
    );

    self.clients.claim();
});

// FETCH (no tocar Vite)
self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);

    // ❌ ignorar Vite y assets dinámicos
    if (
        url.pathname.startsWith('/build/') ||
        event.request.destination === 'script' ||
        event.request.destination === 'style'
    ) {
        return;
    }

    // 🟢 navegación offline fallback
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() =>
                caches.match('/offline.html')
            )
        );
        return;
    }

    // JSON offline: cache-first, luego red (stale-while-revalidate ligero)
    if (OFFLINE_JSON.test(url.pathname)) {
        event.respondWith(cacheFirstJson(event.request));
        return;
    }

    // 🟡 resto: red primero, fallback a caché
    event.respondWith(
        fetch(event.request)
            .then((resp) => {
                const clone = resp.clone();

                caches.open(CACHE_NAME).then((cache) => {
                    if (resp.ok) cache.put(event.request, clone);
                });

                return resp;
            })
            .catch(() => caches.match(event.request))
    );
});

async function cacheFirstJson(request) {
    const cache = await caches.open(CACHE_NAME);
    const cached = await cache.match(request);
    if (cached) {
        fetch(request)
            .then((resp) => {
                if (resp.ok) cache.put(request, resp.clone());
            })
            .catch(() => {});
        return cached;
    }
    const resp = await fetch(request);
    if (resp.ok) await cache.put(request, resp.clone());
    return resp;
}

// 🧠 CACHE SEGURO (NO revienta si un asset falla)
async function safeCache() {
    const cache = await caches.open(CACHE_NAME);

    for (const url of OFFLINE_ASSETS) {
        try {
            const res = await fetch(url);

            if (res && res.ok) {
                await cache.put(url, res);
            }
        } catch (e) {
            console.warn('[SW] Skip cache:', url);
        }
    }
}
