const banner = document.getElementById('offline-banner');

let probeSeq = 0;

/**
 * Comprueba si podemos alcanzar el propio origen (app instalada / SW / servidor).
 * Evita mostrar “sin conexión” cuando `navigator.onLine` miente pero sí hay red.
 */
async function probeReachable() {
    try {
        const c = new AbortController();
        const t = setTimeout(() => c.abort(), 3500);
        const res = await fetch('/manifest.json', {
            method: 'GET',
            cache: 'no-store',
            signal: c.signal,
        });
        clearTimeout(t);
        return res.ok;
    } catch {
        return false;
    }
}

/**
 * - Si el origen responde: ocultamos el banner (hay ruta hasta la app).
 * - `window.__uvox_offline`: solo `true` cuando el navegador reporta `offline`
 *   (sin confiar en la sonda para forzar modo offline con internet).
 * - Si hay red pero el backend falla, `api.js` intenta primero el servidor y
 *   no usa el catálogo de intents como si fuera “sin internet”.
 */
async function updateStatus() {
    const seq = ++probeSeq;
    const reachable = await probeReachable();
    if (seq !== probeSeq) return;

    if (reachable) {
        if (banner) banner.hidden = true;
        window.__uvox_offline = !navigator.onLine;
        return;
    }

    window.__uvox_offline = !navigator.onLine;

    if (banner) {
        banner.hidden = Boolean(navigator.onLine);
    }
}

function scheduleUpdate() {
    setTimeout(() => updateStatus(), 150);
}

window.addEventListener('online', scheduleUpdate);
window.addEventListener('offline', scheduleUpdate);
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') updateStatus();
});

updateStatus();
