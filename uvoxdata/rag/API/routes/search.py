from fastapi import APIRouter, HTTPException
from pydantic import BaseModel, field_validator

from Services.rag_service import responder

router = APIRouter()


class SearchRequest(BaseModel):
    query: str

    @field_validator("query")
    @classmethod
    def sanitize_query(cls, v: str) -> str:
        return " ".join(v.split())


@router.post("/search")
def search(req: SearchRequest):
    try:
        return responder(req.query)
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Error al procesar consulta: {str(e)}")
