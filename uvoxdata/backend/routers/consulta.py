from fastapi import APIRouter
from schemas.consulta_schema import ConsultaRequest, ConsultaResponse
from services.orquestador import orquestar

router = APIRouter()


@router.post("/consulta", response_model=ConsultaResponse)
async def consultar(payload: ConsultaRequest):
    return await orquestar(payload)
