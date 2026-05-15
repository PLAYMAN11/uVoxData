const CACHE_NAME = 'uvox-offline-v1';
const OFFLINE_ASSETS = [
    '/',
    '/build/app.css',
    '/build/app.js',
    '/offline/scenarios.json',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(OFFLINE_ASSETS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k))
            )
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;

    event.respondWith(
        fetch(event.request)
            .then((resp) => {
                const clone = resp.clone();
                caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                return resp;
            })
            .catch(() => caches.match(event.request))
    );
});
