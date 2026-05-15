const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

export async function consultarBackend(pregunta, contexto = null) {
    if (window.__uvox_offline) {
        return buscarOffline(pregunta);
    }

    const res = await fetch('/consulta', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
        },
        body: JSON.stringify({ pregunta, contexto }),
    });

    if (!res.ok) {
        return buscarOffline(pregunta);
    }

    return res.json();
}

export async function subirDocumento(file) {
    const form = new FormData();
    form.append('documento', file);
    form.append('_token', CSRF);

    const res = await fetch('/documento', { method: 'POST', body: form });
    return res.json();
}

async function buscarOffline(pregunta) {
    try {
        const cache = await caches.open('uvox-offline-v1');
        const resp = await cache.match('/offline/scenarios.json');
        if (!resp) return fallback();

        const scenarios = await resp.json();
        const q = pregunta.toLowerCase();
        const match = scenarios.find((s) =>
            s.palabras_clave.some((k) => q.includes(k))
        );

        return match
            ? { respuesta: match.respuesta, modo: 'degradado', fuentes: match.fuentes ?? [] }
            : fallback();
    } catch {
        return fallback();
    }
}

function fallback() {
    return {
        respuesta:
            'Sin conexión y sin coincidencia en escenarios offline. Intenta cuando recuperes el acceso a internet.',
        modo: 'degradado',
        fuentes: [],
    };
}
