from schemas.consulta_schema import ConsultaRequest, ConsultaResponse
from services.aclaracion import necesita_aclaracion, generar_aclaracion
from services.respuesta import generar_respuesta


async def orquestar(payload: ConsultaRequest) -> ConsultaResponse:
    if necesita_aclaracion(payload.pregunta):
        aclaracion = await generar_aclaracion(payload.pregunta)
        return ConsultaResponse(
            necesita_aclaracion=True,
            pregunta_aclaracion=aclaracion.pregunta,
            opciones=aclaracion.opciones,
        )

    return await generar_respuesta(payload)
