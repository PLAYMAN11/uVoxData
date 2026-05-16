import os
from typing import Any

import httpx

from schemas.consulta_schema import ConsultaRequest, ConsultaResponse

RAG_URL = os.getenv("RAG_URL", "http://rag:8002")


def _merge_pregunta_contexto(pregunta: str, contexto: str | None) -> str:
    """Fusiona historial/resumen en una sola consulta para el RAG (sin cambiar el microservicio)."""
    p = (pregunta or "").strip()
    c = (contexto or "").strip()
    if not c:
        return p
    return (
        "Contexto de la conversación anterior:\n"
        f"{c}\n\n"
        "Pregunta actual del usuario:\n"
        f"{p}"
    )


def _respuesta_texto_desde_rag(rag: dict[str, Any]) -> str:
    """Compone texto legible desde el dump de RAGResponse (rag_service.responder)."""
    orient = rag.get("orientation") or {}
    st = rag.get("status") or {}
    doc = rag.get("document") or {}
    parts: list[str] = []

    tipo = (doc.get("type") or "").strip()
    if tipo:
        parts.append(f"Documento referido: {tipo}.")
    auth = (doc.get("authority") or "").strip()
    if auth:
        parts.append(f"Autoridad: {auth}.")
    exp = (doc.get("expedient") or "").strip()
    if exp and exp != "No se encontró":
        parts.append(f"Expediente: {exp}.")

    why = (orient.get("why_you_received_this") or "").strip()
    if why:
        parts.append(why)
    risk = (orient.get("risk_if_no_action") or "").strip()
    if risk:
        parts.append(f"Si no actúas: {risk}")

    headline = (st.get("headline") or "").strip()
    remaining = (st.get("remaining_time") or "").strip()
    if headline and headline != "No se encontró":
        parts.append(f"Estado o plazo: {headline}.")
    if remaining and remaining != "No se encontró":
        parts.append(f"Tiempo o plazo: {remaining}.")

    actions = orient.get("what_you_can_do_now")
    if isinstance(actions, list) and actions:
        parts.append("Qué puedes hacer:")
        for a in actions[:10]:
            if isinstance(a, str) and a.strip():
                parts.append(f"• {a.strip()}")

    sup = rag.get("clarity_support") or {}
    msg = (sup.get("message") or "").strip()
    if msg:
        parts.append(msg)

    return (
        "\n\n".join(parts)
        if parts
        else "No se generó texto orientativo a partir del análisis. Revisa el documento o intenta reformular."
    )


def rag_payload_to_consulta_response(rag: dict[str, Any]) -> ConsultaResponse:
    """Mapea la salida estructurada del microservicio RAG a ConsultaResponse."""
    system = rag.get("system") or {}
    raw_fuentes = system.get("source")
    fuentes: list[str] = list(raw_fuentes) if isinstance(raw_fuentes, list) else []

    confidence = rag.get("confidence") or {}
    conf_status = confidence.get("status") or "low"

    if conf_status in ("high", "medium"):
        modo = "full"
    elif fuentes:
        modo = "full"
    else:
        modo = "degradado"

    texto = _respuesta_texto_desde_rag(rag)
    return ConsultaResponse(
        respuesta=texto,
        fuentes=fuentes,
        modo=modo,
    )


def _legacy_resultados_a_response(rag: dict[str, Any], pregunta: str) -> ConsultaResponse:
    """Compatibilidad si algún despliegue aún devolviera { resultados: [{ texto, fuente }] }."""
    resultados = rag.get("resultados") or []
    fragmentos: list[str] = []
    fuentes: list[str] = []
    if isinstance(resultados, list):
        for r in resultados:
            if isinstance(r, dict):
                t = r.get("texto")
                if isinstance(t, str) and t.strip():
                    fragmentos.append(t.strip())
                f = r.get("fuente")
                if isinstance(f, str) and f.strip():
                    fuentes.append(f.strip())
        fuentes = list(dict.fromkeys(fuentes))
    if not fragmentos:
        return ConsultaResponse(
            respuesta=(
                "No se encontró información suficiente en los documentos oficiales. "
                "Consulta directamente al TEE Chihuahua."
            ),
            fuentes=fuentes,
            modo="degradado",
        )
    ctx = "\n\n".join(fragmentos[:3])
    return ConsultaResponse(
        respuesta=f"Basado en los documentos oficiales:\n\n{ctx}",
        fuentes=fuentes,
        modo="full",
    )


async def generar_respuesta(payload: ConsultaRequest) -> ConsultaResponse:
    query = _merge_pregunta_contexto(payload.pregunta, payload.contexto)

    async with httpx.AsyncClient(timeout=120) as client:
        rag_resp = await client.post(
            f"{RAG_URL}/search",
            json={"query": query},
        )

    if not rag_resp.is_success:
        return ConsultaResponse(
            respuesta=(
                f"No se pudo contactar al servicio de análisis (HTTP {rag_resp.status_code}). "
                "Revisa RAG_URL y que el contenedor RAG esté en marcha."
            ),
            fuentes=[],
            modo="degradado",
        )

    try:
        rag = rag_resp.json()
    except Exception:
        return ConsultaResponse(
            respuesta="La respuesta del servicio RAG no es JSON válido.",
            fuentes=[],
            modo="degradado",
        )

    if not isinstance(rag, dict):
        return ConsultaResponse(
            respuesta="Formato de respuesta RAG inesperado.",
            fuentes=[],
            modo="degradado",
        )

    if isinstance(rag.get("detail"), str):
        return ConsultaResponse(
            respuesta=f"Error del servicio RAG: {rag['detail']}",
            fuentes=[],
            modo="degradado",
        )

    if isinstance(rag.get("resultados"), list) and rag.get("resultados"):
        return _legacy_resultados_a_response(rag, payload.pregunta)

    if "orientation" in rag or "document" in rag:
        return rag_payload_to_consulta_response(rag)

    return ConsultaResponse(
        respuesta="El servicio RAG devolvió un JSON sin campos reconocidos (orientation/document).",
        fuentes=[],
        modo="degradado",
    )
