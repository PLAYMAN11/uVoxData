const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

const OFFLINE_CACHE = 'uvox-offline-v2';

/** Respuesta alineada a ConsultaResponse (demo / IA). */
function consultaOfflinePayload(respuesta, fuentes, modo = 'degradado') {
    return {
        respuesta,
        fuentes: fuentes ?? [],
        modo,
        necesita_aclaracion: false,
        pregunta_aclaracion: null,
        opciones: [],
    };
}

/** Con red pero servidor caído / error HTTP: no simular catálogo offline por intents. */
function respuestaAnalisisEnLineaNoDisponible() {
    return consultaOfflinePayload(
        'El análisis en línea no está disponible: el servidor no respondió o devolvió error. Comprueba BACKEND_URL, que Docker esté en marcha e inténtalo de nuevo.',
        [],
        'degradado'
    );
}

export async function consultarBackend(pregunta, contexto = null) {
    if (!navigator.onLine) {
        return buscarOffline(pregunta);
    }

    const controller = new AbortController();
    const t = setTimeout(() => controller.abort(), 110000);

    let res;
    try {
        res = await fetch('/consulta', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({ pregunta, contexto }),
            signal: controller.signal,
        });
    } catch {
        clearTimeout(t);
        return !navigator.onLine ? buscarOffline(pregunta) : respuestaAnalisisEnLineaNoDisponible();
    }
    clearTimeout(t);

    if (!res.ok) {
        return !navigator.onLine ? buscarOffline(pregunta) : respuestaAnalisisEnLineaNoDisponible();
    }

    return res.json();
}

async function buscarOffline(pregunta) {
    const q = (pregunta || '').toLowerCase();

    try {
        const cache = await caches.open(OFFLINE_CACHE);

        const fromScenarios = await matchScenarios(cache, q);
        if (fromScenarios) return fromScenarios;

        const fromIntents = await matchIntents(cache, q);
        if (fromIntents) return fromIntents;
    } catch {
        /* continuar a fallback */
    }

    return fallback();
}

async function matchScenarios(cache, q) {
    const resp = await cache.match('/offline/scenarios.json');
    if (!resp) return null;

    const scenarios = await resp.json();
    if (!Array.isArray(scenarios)) return null;

    const match = scenarios.find((s) =>
        Array.isArray(s.palabras_clave) &&
        s.palabras_clave.some((k) => q.includes(String(k).toLowerCase()))
    );

    if (!match) return null;

    return consultaOfflinePayload(match.respuesta, match.fuentes ?? []);
}

async function matchIntents(cache, q) {
    const resp = await cache.match('/offline/intents.json');
    if (!resp) return null;

    const data = await resp.json();
    const intents = data?.intents;
    if (!Array.isArray(intents)) return null;

    let best = null;
    let bestPri = -1;

    for (const it of intents) {
        const pri = typeof it.priority === 'number' ? it.priority : 0;
        const keys = [...(it.keywords || []), ...(it.aliases || [])].map((k) =>
            String(k).toLowerCase()
        );
        if (keys.some((k) => q.includes(k)) && pri >= bestPri) {
            best = it;
            bestPri = pri;
        }
    }

    if (!best) return null;

    const msg =
        `Modo offline: coincidencia con la intención «${best.label || best.id}». ` +
        'La respuesta es orientativa; con conexión obtendrás análisis con RAG y documentos oficiales.';

    return consultaOfflinePayload(msg, ['Catálogo offline uVoxData']);
}

function fallback() {
    return consultaOfflinePayload(
        'Sin conexión y sin coincidencia en escenarios offline. Intenta cuando recuperes el acceso a internet.',
        [],
        'degradado'
    );
}

/**
 * Misma lógica que `App\Support\ResultadoMapper::fromConsultaIa` para rellenar
 * `sessionStorage.rag_resultado` en el asistente (p. ej. paso “describir” sin red).
 *
 * @param {Record<string, unknown>} j
 * @returns {Record<string, unknown>}
 */
export function mapConsultaResponseToResultado(j) {
    if (j?.necesita_aclaracion) {
        const opciones = Array.isArray(j.opciones) ? j.opciones : [];
        return {
            documento_tipo: 'Aclaración requerida',
            autoridad: '',
            urgencia: 'media',
            acciones:
                opciones.length > 0
                    ? opciones
                    : ['Elige la opción que mejor describa tu situación.'],
            por_que_lo_recibiste:
                j.pregunta_aclaracion || 'Necesitamos un poco más de contexto para orientarte.',
            consecuencias: 'Sin esta aclaración la orientación podría ser incompleta.',
            fecha_limite_texto: '',
            fecha_limite_iso: '',
            _necesita_aclaracion: true,
            _opciones: opciones,
        };
    }

    const texto = String(j?.respuesta ?? '');
    const fuentes = Array.isArray(j?.fuentes) ? j.fuentes : [];
    const lines = texto.split(/\r\n|\r|\n/).filter((l) => l.length > 0);
    const acciones =
        lines.length > 1
            ? lines.slice(1, 11)
            : texto
              ? [texto]
              : ['Consulta las fuentes oficiales indicadas.'];
    const modo = j?.modo ?? 'degradado';

    return {
        documento_tipo: 'Orientación',
        autoridad: fuentes.filter(Boolean).slice(0, 3).join(', ') || 'Fuentes oficiales',
        urgencia: modo === 'full' ? 'media' : 'sin_plazo',
        acciones,
        por_que_lo_recibiste: (lines[0] ?? texto) || '—',
        consecuencias: fuentes.length ? `Fuentes: ${fuentes.join('; ')}` : '—',
        fecha_limite_texto: '',
        fecha_limite_iso: '',
    };
}

export async function orientacionDesdeTexto(pregunta) {
    const ia = await consultarBackend(pregunta);
    return { ok: true, resultado: mapConsultaResponseToResultado(ia) };
}

export async function subirDocumento(file) {
    if (!navigator.onLine) {
        return {
            error:
                'Sin conexión no podemos enviar el archivo al servidor para analizarlo. Conéctate a internet o usa “Describir la situación” para una orientación offline.',
        };
    }

    const form = new FormData();
    form.append('archivo', file);
    form.append('_token', CSRF);

    const res = await fetch('/documento', { method: 'POST', body: form });
    return res.json();
}
