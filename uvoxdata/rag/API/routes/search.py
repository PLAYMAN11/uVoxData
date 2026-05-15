from fastapi import APIRouter
from pydantic import BaseModel

from Services.rag_service import responder

router = APIRouter()


class SearchRequest(BaseModel):
    query: str
    doc_type: str | None = None


@router.post("/search")
def search(req: SearchRequest):
    return responder(req.query, req.doc_type)
