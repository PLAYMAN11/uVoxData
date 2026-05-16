from contextlib import asynccontextmanager
from fastapi import FastAPI

from API.routes import health, ingest, search
from Services.ingest_service import ingestar_docs_oficiales


@asynccontextmanager
async def lifespan(app: FastAPI):
    try:
        print(">> Iniciando indexado de docs_oficiales...")
        resultado = ingestar_docs_oficiales()
        print(f">> Docs indexados: {resultado}")
    except Exception as e:
        print(f">> ERROR en lifespan: {type(e).__name__}: {e}")
    yield


app = FastAPI(title="uVoxData RAG", version="0.2.0", lifespan=lifespan)

app.include_router(health.router)
app.include_router(ingest.router)
app.include_router(search.router)
