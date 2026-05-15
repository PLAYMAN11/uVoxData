# Datos offline (catálogo precargado)

- **`fallback/`** — Fuentes JSON modulares (`intents.json`, `synonyms.json`, `cases/*.json`, `rule-packs/*.json`). Edita estos archivos para ampliar el modo avión / PWA offline.
- **`schema.json`** — Esquema JSON del catálogo (referencia para autores).
- **`rules.json`**, **`output_format.json`** — Ejemplo de reglas y forma de respuesta estructurada para alinear contenido entre online y offline.
- **`indexeddb/`** — Documentación del almacén del navegador (ver `indexeddb/README.md`).

El artefacto que consume el frontend es **`frontend/public/catalog.json`**, generado con:

```bash
cd frontend && npm run build:catalog
```

(o `node ../scripts/build-catalog.mjs` desde cualquier ubicación usando la raíz del repo).
