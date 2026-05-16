# Checklist demo (hackathon)

## Prerrequisitos

1. `BACKEND_URL` apunta al FastAPI en ejecución (p. ej. `http://127.0.0.1:8001`).
2. FastAPI y servicio RAG según `uvoxdata/README.md` o `docker-compose up`.
3. `composer install` y `npm install` en `frontend/`.
4. `php artisan serve` (o servidor configurado) sirviendo la app Laravel.

## Online

1. Abrir `/` → **Revisar documento** → subir PDF o imagen válida → **procesando** → **resultado** con datos mapeados desde RAG.
2. Flujo sin archivo: `/consulta/descripcion` → enviar texto → mismo resultado (mapeo desde `POST /consulta` FastAPI).

## Offline

1. Cargar la app una vez online (para que el SW instale caché `uvox-offline-v2`).
2. DevTools → **Offline** (o desconectar red).
3. Abrir `/demo` o vista que use `consultarBackend` desde `app.js` — debe responder modo degradado con `scenarios.json` / intents.
4. Comprobar que el banner offline aparece si `layouts/app.blade.php` incluye el partial del banner.

## Fallos controlados

- Con backend apagado: wizard debe responder **503** y el usuario ve alerta (mejora futura: fallback automático a offline en Blade).
