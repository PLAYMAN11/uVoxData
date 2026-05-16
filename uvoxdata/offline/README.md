# Datos offline

Esta carpeta contiene la información que permite que uVoxData entregue respuestas básicas cuando no hay conexión o cuando el backend no puede responder. La idea es tener un catálogo pequeño, precargado y prudente: no intenta resolver casos complejos, sino orientar al usuario con mensajes, fuentes y rutas generales.

Hay dos niveles de contenido:

- `scenarios.json`: archivo simple que usa actualmente el frontend para responder en modo degradado.
- `fallback/`: catálogo modular más completo, organizado por intenciones, casos, sinónimos y paquetes de reglas.

## Flujo actual

1. El service worker de `frontend/public/sw.js` guarda en caché `/offline/scenarios.json` junto con los assets principales de la app.
2. Cuando `frontend/resources/js/api.js` detecta modo offline o falla la petición a `/consulta`, ejecuta `buscarOffline(pregunta)`.
3. `buscarOffline` abre el caché `uvox-offline-v1`, lee `/offline/scenarios.json` y compara la pregunta del usuario contra cada lista de `palabras_clave`.
4. Si encuentra coincidencia, devuelve la `respuesta`, las `fuentes` y `modo: "degradado"`.
5. Si no encuentra coincidencia, devuelve un mensaje genérico indicando que no hay conexión ni escenario offline aplicable.

## Contenido de la carpeta

### `scenarios.json`

Es el catálogo simple que consume el frontend en el flujo offline actual. Contiene un arreglo de escenarios prefabricados.

Cada escenario tiene:

- `id`: identificador único del escenario.
- `palabras_clave`: palabras o frases que se buscan dentro de la pregunta del usuario.
- `respuesta`: texto que se devuelve cuando hay coincidencia.
- `fuentes`: referencias breves usadas para respaldar la respuesta.

Qué hace: permite respuestas rápidas sin backend ni red. Es útil para consultas frecuentes como plazos, glosario básico, autoridad competente, recursos de apelación o recomendaciones para conectarse cuando la pregunta depende de datos cambiantes.

### `schema.json`

Define el esquema JSON del catálogo offline modular. Sirve como referencia para saber qué estructura debe tener un caso completo dentro de `fallback/cases/`.

Describe campos como:

- `version`: versión del catálogo.
- `cases`: lista de casos offline.
- `id`, `title`, `keywords`, `summary`: identificación y búsqueda del caso.
- `intentId`: intención asociada.
- `doc_type`, `proceso`, `rule_pack_id`: metadatos para relacionar el caso con reglas.
- `questions`: preguntas guiadas que pueden ayudar a precisar la orientación.
- `output`: respuesta estructurada que debe seguir `output_format.json`.
- `sources`: fuentes oficiales o de referencia.
- `suggested_questions`: preguntas sugeridas para continuar la conversación.

Qué hace: ayuda a validar y mantener consistente el formato del catálogo modular.

### `rules.json`

Es un ejemplo de reglas para un caso de `cedula_notificacion` dentro del proceso `medio_impugnacion`.

Incluye:

- `doc_type`: tipo de documento al que aplican las reglas.
- `proceso`: proceso jurídico/electoral relacionado.
- `reglas`: datos concretos como plazo, inicio del plazo, fundamento y consecuencia.
- `autoridad_default`: autoridad sugerida por defecto.
- `ruta_default`: ruta general recomendada.
- `tramite`: descripción del trámite.
- `links_oficiales`: enlaces de referencia.

Qué hace: documenta cómo se puede representar una regla legal reutilizable para alimentar una respuesta offline.

### `output_format.json`

Es la plantilla de salida estructurada que deben seguir los casos offline completos.

Campos principales:

- `tipo_doc`: tipo de documento relacionado.
- `autoridad_competente`: autoridad que podría atender el asunto.
- `ruta_sugerida`: camino recomendado.
- `tramite`: trámite o acción sugerida.
- `modalidad`: si es presencial, en línea o depende de la autoridad.
- `plazo`: plazo aplicable o advertencia de verificación.
- `consecuencias`: posibles efectos de no actuar.
- `links_oficiales`: enlaces de apoyo.
- `respuesta`: explicación final para el usuario.

Qué hace: mantiene el mismo formato de respuesta entre distintos casos y reduce respuestas improvisadas.

## Carpeta `fallback/`

Contiene el catálogo modular de respaldo. A diferencia de `scenarios.json`, separa la información por tipo de dato para que sea más fácil mantenerla y crecerla.

### `fallback/intents.json`

Lista las intenciones que el sistema puede reconocer en modo offline.

Cada intención tiene:

- `id`: identificador interno, por ejemplo `intent.glossary`.
- `label`: nombre legible.
- `priority`: prioridad para resolver empates o dar preferencia.
- `keywords`: palabras que activan esa intención.
- `aliases`: formas alternativas de decir lo mismo.

Qué hace: clasifica la pregunta del usuario antes de elegir un caso. Por ejemplo, distingue entre glosario, autoridad, plazos, notificación y ayuda en línea.

### `fallback/synonyms.json`

Define equivalencias de términos.

Ejemplos:

- `tee` se expande a `tribunal electoral`.
- `tepjf` se expande a `tribunal electoral`.
- `ine` se expande a `instituto nacional electoral`.

Qué hace: mejora la búsqueda offline al reconocer abreviaturas o términos comunes que significan lo mismo.

### `fallback/cases/`

Contiene casos offline completos. Cada archivo representa una situación que el sistema puede responder con más estructura que un escenario simple.

Un caso normalmente incluye:

- `id`: identificador del caso.
- `intentId`: intención asociada.
- `title`: título descriptivo.
- `keywords`: palabras que ayudan a encontrar el caso.
- `summary`: explicación corta del alcance.
- `questions`: preguntas opcionales para guiar al usuario.
- `output`: respuesta estructurada.
- `sources`: fuentes con etiqueta y URL.
- `suggested_questions`: preguntas de seguimiento.

#### `fallback/cases/consultar-en-linea-recomendacion.json`

Explica cuándo conviene conectarse a Internet. Cubre preguntas que dependen de datos cambiantes, como fechas, casillas, módulos, estados de trámite o criterios actualizados.

Qué hace: evita que el modo offline invente información cuando la respuesta requiere datos vivos o verificación oficial.

#### `fallback/cases/glosario-basico.json`

Contiene definiciones breves de conceptos como plazo, notificación, autoridad competente y medio de impugnación.

Qué hace: permite responder dudas generales de vocabulario electoral sin conexión.

#### `fallback/cases/impugnacion-cedula-notificacion.json`

Orienta sobre una cédula de notificación y un posible medio de impugnación. Está conectado con el paquete de reglas `cedula_notificacion_impugnacion`.

Incluye una pregunta guiada para distinguir si el usuario recibió una cédula/notificación formal u otro documento.

Qué hace: entrega una orientación prudente sobre autoridad, ruta, trámite, modalidad, plazo, consecuencias y enlaces oficiales, aclarando que el usuario debe verificar su caso concreto.

#### `fallback/cases/ubicar-autoridad.json`

Ayuda a decidir de forma general a qué autoridad acercarse según el tema: credencial/INE, quejas o denuncias electorales, impugnaciones, tribunales u otras autoridades.

Qué hace: da una ruta inicial cuando el usuario no sabe si debe acudir al INE, un OPLE, un tribunal electoral u otra autoridad.

### `fallback/rule-packs/`

Agrupa reglas reutilizables que pueden ser referenciadas por varios casos.

#### `fallback/rule-packs/cedula_notificacion_impugnacion.json`

Define reglas para el caso de cédula de notificación e impugnación.

Incluye:

- `id`: identificador del paquete.
- `doc_type`: documento al que aplica.
- `proceso`: proceso relacionado.
- `reglas.plazo_dias`: número de días del plazo.
- `reglas.inicio_plazo`: desde cuándo empieza a correr.
- `reglas.fundamento`: fundamento citado.
- `reglas.consecuencia_fuera_plazo`: consecuencia general.
- `autoridad_default`, `ruta_default`, `tramite` y `links_oficiales`.

Qué hace: centraliza reglas para no repetirlas manualmente dentro de cada caso.

## Carpeta `indexeddb/`

Contiene documentación sobre el almacenamiento offline del navegador.

### `indexeddb/README.md`

Describe una base de datos IndexedDB llamada `electoral-orientacion`, pensada para guardar metadatos, intenciones, entradas, sinónimos y paquetes de reglas.

Stores documentados:

- `meta`: metadatos como versión del catálogo.
- `intents`: intenciones del catálogo.
- `entries`: casos o entradas offline.
- `synonyms`: expansiones léxicas.
- `rulePacks`: paquetes de reglas.

Qué hace: explica cómo debería persistirse un catálogo offline más completo en el navegador cuando se use IndexedDB, incluyendo la idea de comparar versiones y usar una copia local si está actualizada.

## Cómo agregar contenido

### Agregar una respuesta al flujo offline actual

Edita `scenarios.json` y añade un objeto al arreglo:

```json
{
  "id": "identificador_unico",
  "palabras_clave": ["termino1", "termino2"],
  "respuesta": "Texto de la respuesta prefabricada.",
  "fuentes": ["Referencia normativa"]
}
```

Usa este camino cuando quieras que el frontend actual responda de inmediato en modo offline.

### Agregar un caso modular

1. Crea un archivo en `fallback/cases/`.
2. Usa la estructura definida en `schema.json`.
3. Relaciona el caso con una intención de `fallback/intents.json` mediante `intentId`.
4. Si el caso usa reglas reutilizables, agrega o reutiliza un archivo en `fallback/rule-packs/`.
5. Mantén la respuesta dentro del formato de `output_format.json`.

Usa este camino cuando el contenido requiera más estructura, fuentes con URL, preguntas guiadas o reglas compartidas.

## Recomendaciones de mantenimiento

- Mantén las respuestas offline prudentes: si el dato puede cambiar, indica que debe verificarse con Internet o ante la autoridad competente.
- No agregues plazos, fundamentos o autoridades sin fuente.
- Usa `palabras_clave`, `keywords`, `aliases` y `synonyms` con lenguaje real de usuarios, incluyendo abreviaturas comunes.
- Evita duplicar reglas legales en muchos casos; usa `rule-packs` cuando una regla pueda compartirse.
- Si modificas `scenarios.json`, revisa que cada elemento tenga `id`, `palabras_clave` y `respuesta`, porque eso es lo que validan las pruebas offline.

## Reglas para autores del catálogo offline

Estas reglas son obligatorias para cualquier persona (o agente) que modifique archivos dentro de `offline/`.

### Alcance permitido

Solo se pueden tocar archivos dentro de `offline/`. Está prohibido:

- modificar `frontend/`, `backend/`, `rag/`, `tests/`,
- cambiar `frontend/public/sw.js` o `frontend/resources/js/api.js`,
- alterar configuración de Vite, Laravel o PWA,
- crear endpoints o servicios nuevos,
- renombrar archivos existentes o mover carpetas,
- cambiar el contrato del catálogo (`scenarios.json`).

El modo offline orienta y responde preguntas frecuentes. No reemplaza al backend, no resuelve casos complejos y no hace interpretación jurídica.

### Compatibilidad obligatoria de `scenarios.json`

`scenarios.json` es crítico para el flujo degradado del frontend. Cada escenario debe seguir exactamente esta forma:

```json
{
  "id": "identificador_unico",
  "palabras_clave": ["termino1", "termino2"],
  "respuesta": "Texto breve y claro.",
  "fuentes": ["Referencia"]
}
```

No eliminar ni renombrar `id`, `palabras_clave`, `respuesta`, `fuentes`. No cambiar sus tipos. El archivo sigue siendo un array.

### Convención de `palabras_clave`

- Escenarios específicos van antes que escenarios genéricos en el array.
- Evita palabras muy abiertas (`plazo`, `notificacion`, `impugnacion`, `autoridad`) dentro de escenarios generales como `glosario-basico` o `consultar-en-linea-recomendacion`.
- Prefiere frases multi-palabra cuando ayuden a desambiguar (`plazo para impugnar`, `cedula de notificacion`, `recurso de apelacion`).
- Incluye variantes con y sin acento solo cuando son comunes.

### Compatibilidad obligatoria de los casos en `fallback/cases/`

Cada caso conserva su estructura externa: `id`, `intentId`, `title`, `keywords`, `summary`, `questions` (si aplica), `output`, `sources`, `suggested_questions`.

El subobjeto `output` debe seguir EXACTAMENTE el formato oficial y único definido en `output_format.json`:

```json
{
  "tipo_doc": "",
  "autoridad_competente": "",
  "ruta_sugerida": "",
  "tramite": "",
  "modalidad": "",
  "plazo": "",
  "consecuencias": "",
  "links_oficiales": [],
  "respuesta": ""
}
```

Reglas obligatorias del `output`:

- Usar únicamente esas nueve propiedades. No agregar propiedades extra (sin `status`, `document`, `orientation`, `clarity_support`, `confidence`, `system`, etc.).
- No renombrar campos ni cambiar tipos. `links_oficiales` siempre es arreglo de strings.
- `respuesta` breve y clara, máximo 2 a 4 oraciones. Sin bloques narrativos largos ni explicaciones jurídicas extensas.
- Si falta un dato, usar string vacío (`""`) o arreglo vacío (`[]`); no inventar fechas ni autoridades.
- Mantener compatibilidad total con `schema.json`, `output_format.json` y `scenarios.json`.

Propiedades prohibidas dentro de `output` (lista cerrada):

- sin `modo`,
- sin `fuentes` ni `sources`,
- sin `opciones`,
- sin `necesita_aclaracion` ni `pregunta_aclaracion`,
- sin metadata extra de ningún tipo,
- sin texto fuera del JSON.

Estas propiedades pueden existir a nivel del wrapper del case (`sources`, `questions`, etc.), pero nunca dentro de `output`. El frontend recibe únicamente las 9 claves de `output_format.json`.

### Intents

- Prefijo obligatorio `intent.`.
- Cada intent con `id`, `label`, `priority`, `keywords`, `aliases`.
- Sin intents ambiguos. Cada uno debe representar una intención clara del usuario.

### Synonyms

- Mantener el formato `{ "synonyms": [ { "from": "...", "to": "..." } ] }`.
- Expandir abreviaturas comunes y términos reales de usuarios.
- No duplicar entradas.

### Rule-packs

- Las reglas reutilizables (`plazo`, `fundamento`, `autoridad`, `consecuencia`, `tramite`) viven sólo en `fallback/rule-packs/`.
- Si una regla aparece en varios casos, extraerla a un rule-pack en lugar de duplicarla.

### Frases prohibidas en respuestas y orientaciones

Para mantener el tono prudente y evitar promesas indebidas, no usar:

- "IA jurídica"
- "asesoría legal"
- "chatbot"
- "análisis inteligente"
- "motor legal"
- "GPT"
- "consulta legal automática"

### Tono del contenido

- Respuestas cortas y en lenguaje humano (2 a 4 oraciones).
- Sin afirmaciones absolutas cuando dependan del caso particular.
- No inventar fechas, expedientes ni autoridades específicas.
- Cuando haya incertidumbre, usar mensajes prudentes y sugerir verificación en línea o con la autoridad competente.

### Nunca transcribir documentos completos

El catálogo offline no es un visor documental. Está prohibido devolver, almacenar o pegar:

- transcripciones completas de documentos,
- texto OCR sin procesar,
- acuerdos, oficios o resoluciones íntegros,
- contenido íntegro de un PDF,
- bloques largos citados de fuentes oficiales.

En lugar de eso, cada respuesta debe:

1. identificar la información clave,
2. resumirla,
3. estructurarla en los campos correspondientes del `output`,
4. y descartar el resto del contenido.

Extraer únicamente:

- tipo de documento,
- autoridad,
- trámite,
- plazo,
- consecuencia principal,
- orientación breve.

Cualquier otra información del documento fuente se descarta.

La propiedad `respuesta` debe:

- tener máximo 2 a 4 oraciones,
- mantenerse cerca de 300 caracteres como objetivo UX (no validación estricta, pequeños excesos toleran si conservan claridad),
- usar lenguaje simple,
- explicar únicamente lo esencial,
- nunca contener texto jurídico completo,
- sin citas textuales,
- sin lenguaje jurídico complejo,
- sentirse como un resumen humano breve, no como contenido documental,
- nunca convertirse en transcripción, OCR ni cita extensa.

Ejemplo correcto de `respuesta`:

> "El documento contiene un requerimiento relacionado con un procedimiento electoral y establece un plazo breve para responder. Verifica la fecha exacta con la autoridad competente."

### Prioridades del catálogo

El catálogo offline prioriza, en este orden:

1. claridad,
2. brevedad,
3. orientación,
4. compatibilidad con el frontend,
5. lectura rápida bajo presión,
6. estabilidad estructural.
