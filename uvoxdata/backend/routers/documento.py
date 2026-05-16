import os
import tempfile
from pathlib import Path
import httpx
from fastapi import APIRouter, UploadFile, File, HTTPException

from services.ocr_service import extraer_texto, _TIPOS_PDF, _TIPOS_IMAGEN

router = APIRouter()

RAG_URL = os.getenv("RAG_URL", "http://rag:8002")

_TIPOS_ACEPTADOS = _TIPOS_PDF | _TIPOS_IMAGEN


@router.post("/documento/debug")
async def debug_ocr(archivo: UploadFile = File(...)):
    """Devuelve el texto extraído por OCR sin mandarlo al RAG."""
    tmp_path = None
    try:
        suffix = Path(archivo.filename).suffix if archivo.filename else ".bin"
        with tempfile.NamedTemporaryFile(delete=False, suffix=suffix) as tmp:
            tmp.write(await archivo.read())
            tmp_path = tmp.name
        texto = extraer_texto(tmp_path, archivo.content_type)
        return {"texto_extraido": texto, "caracteres": len(texto)}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))
    finally:
        if tmp_path and os.path.exists(tmp_path):
            os.unlink(tmp_path)


@router.post("/documento")
async def subir_documento(archivo: UploadFile = File(...)):
    if archivo.content_type not in _TIPOS_ACEPTADOS:
        raise HTTPException(
            status_code=400,
            detail=f"Tipo no soportado: {archivo.content_type}. Se aceptan PDF e imágenes (JPG, PNG, WEBP, HEIC)."
        )

    tmp_path = None
    try:
        suffix = Path(archivo.filename).suffix if archivo.filename else ".bin"
        with tempfile.NamedTemporaryFile(delete=False, suffix=suffix) as tmp:
            tmp.write(await archivo.read())
            tmp_path = tmp.name

        texto = extraer_texto(tmp_path, archivo.content_type)

        if not texto:
            raise HTTPException(status_code=422, detail="No se pudo extraer texto del archivo.")

        async with httpx.AsyncClient(timeout=120) as client:
            resp = await client.post(
                f"{RAG_URL}/search",
                json={"query": texto},
            )

        if resp.is_error:
            raise HTTPException(status_code=502, detail="Error al consultar el RAG.")

        return resp.json()

    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Error al procesar el archivo: {str(e)}")
    finally:
        if tmp_path and os.path.exists(tmp_path):
            os.unlink(tmp_path)


