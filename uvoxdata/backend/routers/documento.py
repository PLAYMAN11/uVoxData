import httpx
import os

from fastapi import APIRouter, UploadFile, File, HTTPException

router = APIRouter()

RAG_URL = os.getenv("RAG_URL", "http://rag:8002")


@router.post("/documento")
async def subir_documento(archivo: UploadFile = File(...)):
    if archivo.content_type != "application/pdf":
        raise HTTPException(status_code=400, detail="Solo se aceptan archivos PDF.")

    contenido = await archivo.read()
    async with httpx.AsyncClient(timeout=60) as client:
        resp = await client.post(
            f"{RAG_URL}/ingest",
            files={"archivo": (archivo.filename, contenido, "application/pdf")},
        )

    if resp.is_error:
        raise HTTPException(status_code=502, detail="Error al indexar el documento.")

    return resp.json()
