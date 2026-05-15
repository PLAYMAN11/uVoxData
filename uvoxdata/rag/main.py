from contextlib import asynccontextmanager
from fastapi import FastAPI

from API.routes import health, ingest, search
from Services.ingest_service import ingestar_docs_oficiales


@asynccontextmanager
async def lifespan(app: FastAPI):
    ingestar_docs_oficiales()
    yield


app = FastAPI(title="uVoxData RAG", version="0.2.0", lifespan=lifespan)

app.include_router(health.router)
app.include_router(ingest.router)
app.include_router(search.router)
