import tempfile
import os
from fastapi import APIRouter, UploadFile, File
from pydantic import BaseModel

from Services.ingest_service import ingestar_texto, ingestar_pdf

router = APIRouter()


class IngestRequest(BaseModel):
    texto: str
    fuente: str


@router.post("/ingest")
def ingest(req: IngestRequest):
    n = ingestar_texto(req.texto, req.fuente)
    return {"mensaje": f"Indexados {n} fragmentos de '{req.fuente}'."}


@router.post("/ingest/oficial")
async def ingest_oficial(archivo: UploadFile = File(...)):
    with tempfile.NamedTemporaryFile(delete=False, suffix=".pdf") as tmp:
        tmp.write(await archivo.read())
        tmp_path = tmp.name

    try:
        n = ingestar_pdf(tmp_path, fuente=archivo.filename)
    finally:
        os.unlink(tmp_path)

    return {"mensaje": f"Indexados {n} fragmentos de '{archivo.filename}'."}
