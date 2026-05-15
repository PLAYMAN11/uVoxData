from fastapi import FastAPI, UploadFile, File
from pydantic import BaseModel
import tempfile, os

from indexer.ingest import ingestar_pdf
from retriever.search import buscar
from retriever.reranker import rerank

app = FastAPI(title="uVoxData RAG", version="0.1.0")


class SearchRequest(BaseModel):
    query: str
    top_k: int = 5


@app.get("/health")
def health():
    return {"status": "ok"}


@app.post("/ingest")
async def ingest(archivo: UploadFile = File(...)):
    with tempfile.NamedTemporaryFile(delete=False, suffix=".pdf") as tmp:
        tmp.write(await archivo.read())
        tmp_path = tmp.name

    try:
        n = ingestar_pdf(tmp_path, fuente=archivo.filename)
    finally:
        os.unlink(tmp_path)

    return {"mensaje": f"Indexados {n} fragmentos de '{archivo.filename}'."}


@app.post("/search")
def search(req: SearchRequest):
    resultados = buscar(req.query, top_k=req.top_k)
    resultados = rerank(resultados, req.query)
    return {"resultados": resultados}
