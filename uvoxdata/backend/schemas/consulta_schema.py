from pydantic import BaseModel
from typing import Optional


class ConsultaRequest(BaseModel):
    pregunta: str
    contexto: Optional[str] = None


class ConsultaResponse(BaseModel):
    respuesta: Optional[str] = None
    fuentes: list[str] = []
    modo: Optional[str] = None
    necesita_aclaracion: bool = False
    pregunta_aclaracion: Optional[str] = None
    opciones: list[str] = []
