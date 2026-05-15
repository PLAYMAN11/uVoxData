# uVoxData

Sistema de consulta electoral con soporte offline y RAG para el TEE Chihuahua.

## Estructura

- **frontend/** — Interfaz Laravel + Vite (PWA con service worker)
- **backend/** — API FastAPI (orquestación, aclaración, respuesta)
- **rag/** — Indexación y recuperación de documentos oficiales con ChromaDB
- **offline/** — Respuestas prefabricadas para modo sin conexión
- **tests/** — Pruebas de integración y fixtures

## Inicio rápido

```bash
cp .env.example .env
docker-compose up --build
```

## Requisitos

- Docker + Docker Compose
- Node.js 20+
- PHP 8.2+ / Composer
