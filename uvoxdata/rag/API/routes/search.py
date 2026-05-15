from fastapi import APIRouter
from pydantic import BaseModel

from Services.rag_service import responder

router = APIRouter()


class SearchRequest(BaseModel):
    query: str


@router.post("/search")
def search(req: SearchRequest):
    return responder(req.query)
