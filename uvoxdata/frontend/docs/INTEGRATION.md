# Integración Frontend (Laravel) ↔ Backend (FastAPI) ↔ RAG ↔ Offline

Mapa técnico del monorepo `uvoxdata/` para el flujo **online / offline** y el wizard de consulta.

## Capas

| Componente | Ruta / archivo | Rol |
|------------|----------------|-----|
| PWA + UI | `frontend/` — Blade, Vite, `public/sw.js` | Wizard: documento → procesando → resultado; `sessionStorage.rag_resultado` con `{ ok?, resultado: { … } }`. |
| Proxy BFF | `POST /consulta`, `POST /documento`, `POST /consulta/subir`, `POST /consulta/describir` → [`ConsultaController`](app/Http/Controllers/ConsultaController.php) | Laravel reenvía a `BACKEND_URL` (`.env` / [`config/services.php`](config/services.php)). |
| API Python | `backend/` — `POST /consulta`, `POST /documento` | Orquestación + RAG vía `RAG_URL` en `services/respuesta.py` y `routers/documento.py`. |
| RAG | Servicio en `rag/` (p. ej. `POST /search`) | Recuperación de fragmentos; respuesta estructurada (`RAGResponse` en `rag/Core/schemas.py`). |
| Offline | `offline/` (fuente) + `public/offline/*.json` (servidos al SW) | `scenarios.json` (keywords) + `intents.json` (segundo nivel en cliente). |

## Contratos JSON

### Wizard — `sessionStorage.rag_resultado`

El cliente guarda el JSON tal cual lo devuelve Laravel:

- **`POST /consulta/subir`**: `{ ok, mensaje, tipo, resultado }` — `resultado` mapeado desde la respuesta RAG de `/documento` ([`ResultadoMapper::fromRagSearch`](app/Support/ResultadoMapper.php)).
- **`POST /consulta/describir`**: `{ ok, resultado }` — `resultado` mapeado desde `ConsultaResponse` ([`ResultadoMapper::fromConsultaIa`](app/Support/ResultadoMapper.php)).

Campos usados por [`resources/views/resultado.blade.php`](../resources/views/resultado.blade.php): `documento_tipo`, `autoridad`, `urgencia`, `acciones[]`, `por_que_lo_recibiste`, `consecuencias`, `fecha_limite_texto`, `fecha_limite_iso`.

### Demo / `api.js` — `POST /consulta` y `POST /documento`

- **`POST /documento`**: JSON crudo del microservicio RAG (`RAGResponse` vía `rag/Services/rag_service.responder`).
- **`POST /consulta`**: [`ConsultaResponse`](../../backend/schemas/consulta_schema.py). El backend FastAPI llama a `RAG_URL/search` y **mapea** el dump de `RAGResponse` en [`services/respuesta.py`](../../backend/services/respuesta.py) (`rag_payload_to_consulta_response`): `fuentes` ← `system.source`, texto compuesto desde `document` / `status` / `orientation`, `modo` según `confidence` y fuentes. El campo opcional **`contexto`** se fusiona en el string enviado al RAG (`_merge_pregunta_contexto`) para historial en chat.

### Chat (`/consulta/chat`)

- Acordeón superior: sigue leyendo `sessionStorage.rag_resultado` (resumen del caso).
- Panel inferior: [`resources/js/chat.js`](../resources/js/chat.js) montado desde [`app.js`](../resources/js/app.js) — envía cada mensaje a `consultarBackend(pregunta, contexto)` → `POST /consulta` con historial reciente en `contexto`.
- Sin red: mismo comportamiento que `api.js` (catálogo local); aviso `#chat-offline-hint`.

## Verificación backend (unitario)

Desde `uvoxdata/backend/` (con Python disponible):

```bash
python -m unittest tests.test_rag_mapper -v
```

Comprueba el mapeo RAG → `ConsultaResponse` sin levantar servicios.

## Offline

1. [`resources/js/connectivity.js`](../resources/js/connectivity.js): sonda opcional a `/manifest.json` para el **banner**; `window.__uvox_offline` sigue a **`navigator.onLine`** (no se fuerza offline solo porque falle la sonda con internet).
2. [`resources/js/api.js`](../resources/js/api.js): con **sin red** (`!navigator.onLine`) se usa `buscarOffline()` (caché `uvox-offline-v2`). Con red pero error de red/HTTP en `/consulta`, **no** se simulan intents: mensaje de servidor no disponible.
3. [`public/sw.js`](../public/sw.js) precachea esos JSON.

**Fuente de verdad** para editar contenido offline: `uvoxdata/offline/` — copiar o sincronizar a `frontend/public/offline/` antes de desplegar.

## Variables de entorno

- `BACKEND_URL` — URL base del FastAPI (p. ej. `http://localhost:8001`).
- En Docker: ver `uvoxdata/README.md` y `docker-compose`.

## IndexedDB (pendiente)

Especificación en `uvoxdata/offline/indexeddb/README.md` (módulo `knowledge-db.js` aún no implementado en este repo).
