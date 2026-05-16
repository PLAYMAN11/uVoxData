import tempfile
import os
from fastapi import APIRouter, UploadFile, File, HTTPException
from pydantic import BaseModel, field_validator

from Services.ingest_service import ingestar_texto, ingestar_pdf

router = APIRouter()


class IngestRequest(BaseModel):
    texto: str
    fuente: str

    @field_validator("texto", "fuente")
    @classmethod
    def sanitize(cls, v: str) -> str:
        return " ".join(v.split())


@router.post("/ingest")
def ingest(req: IngestRequest):
    try:
        n = ingestar_texto(req.texto, req.fuente)
        return {"mensaje": f"Indexados {n} fragmentos de '{req.fuente}'."}
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Error al indexar: {str(e)}")


@router.post("/ingest/oficial")
async def ingest_oficial(archivo: UploadFile = File(...)):
    tmp_path = None
    try:
        with tempfile.NamedTemporaryFile(delete=False, suffix=".pdf") as tmp:
            tmp.write(await archivo.read())
            tmp_path = tmp.name

        n = ingestar_pdf(tmp_path, fuente=archivo.filename)
        return {"mensaje": f"Indexados {n} fragmentos de '{archivo.filename}'."}
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Error al indexar PDF: {str(e)}")
    finally:
        if tmp_path and os.path.exists(tmp_path):
            os.unlink(tmp_path)
