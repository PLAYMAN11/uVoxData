# Modo Offline

`scenarios.json` contiene respuestas prefabricadas para las consultas más frecuentes.

El service worker cachea este archivo en la primera visita con conexión. Cuando el dispositivo está sin red, `api.js` busca coincidencias por `palabras_clave` y devuelve la respuesta con `modo: "degradado"`.

## Agregar escenarios

Añade un objeto al array con esta forma:

```json
{
  "id": "identificador_unico",
  "palabras_clave": ["término1", "término2"],
  "respuesta": "Texto de la respuesta prefabricada.",
  "fuentes": ["Referencia normativa"]
}
```
