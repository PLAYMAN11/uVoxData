const CACHE_NAME = 'uvox-offline-v1';

const OFFLINE_ASSETS = [
    '/',
    '/offline.html',
    '/offline/scenarios.json',
];

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

    // 🟡 cache normal
    event.respondWith(
        fetch(event.request)
            .then((resp) => {
                const clone = resp.clone();

                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(event.request, clone);
                });

                return resp;
            })
            .catch(() => caches.match(event.request))
    );
});

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