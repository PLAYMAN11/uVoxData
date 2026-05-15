# IndexedDB — catálogo offline en el navegador

Implementación: `frontend/resources/js/offline/knowledge-db.js`.

## Base de datos

- **Nombre:** `electoral-orientacion`
- **Versión:** `DB_VERSION = 1` (incrementar solo si cambia el esquema de stores)

## Stores

| Store       | KeyPath | Uso                                       |
|------------|---------|-------------------------------------------|
| `meta`     | —       | p. ej. `catalogVersion`                   |
| `intents`  | `id`    | Lista de intents del catálogo remoto/local |
| `entries`  | `id`    | Casos / entradas offline                  |
| `synonyms` | `from`  | Expansiones léxicas                       |
| `rulePacks`| `id`    | Rule packs inlined                        |

## Flujo

1. El cliente intenta cargar `/catalog.json` (versión y entradas fusionadas).
2. Si la versión coincide con lo guardado en IndexedDB, se usa la copia local.
3. Si no hay red o falla el fetch, el motor usa JSON embebido en el bundle (fallback Vite) emparejado con `offline/fallback/`.
