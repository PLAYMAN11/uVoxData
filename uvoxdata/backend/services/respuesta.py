import httpx
import os

from schemas.consulta_schema import ConsultaRequest, ConsultaResponse

RAG_URL = os.getenv("RAG_URL", "http://rag:8002")


async def generar_respuesta(payload: ConsultaRequest) -> ConsultaResponse:
    async with httpx.AsyncClient(timeout=30) as client:
        rag_resp = await client.post(
            f"{RAG_URL}/search",
            json={"query": payload.pregunta, "top_k": 5},
        )

    fragmentos = []
    fuentes = []
    if rag_resp.is_success:
        resultados = rag_resp.json().get("resultados", [])
        fragmentos = [r["texto"] for r in resultados]
        fuentes = list({r["fuente"] for r in resultados})

    respuesta_texto = _sintetizar(payload.pregunta, fragmentos)

    return ConsultaResponse(
        respuesta=respuesta_texto,
        fuentes=fuentes,
        modo="full" if fragmentos else "degradado",
    )


def _sintetizar(pregunta: str, fragmentos: list[str]) -> str:
    if not fragmentos:
        return (
            "No se encontró información suficiente en los documentos oficiales. "
            "Consulta directamente al TEE Chihuahua."
        )
    contexto = "\n\n".join(fragmentos[:3])
    return f"Basado en los documentos oficiales:\n\n{contexto}"
