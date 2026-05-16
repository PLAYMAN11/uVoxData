import json
from langchain_core.prompts import ChatPromptTemplate
from langchain_core.output_parsers import StrOutputParser

from Core.vector_store import get_vector_store
from Core.llm_client import get_llm
from Core.schemas import RAGResponse
from Core.rules_loader import get_rules, _load_rules


_detect_prompt = ChatPromptTemplate.from_template(
    """Eres un clasificador de documentos electorales.
Dado el siguiente texto, determina a qué tipo de documento corresponde.

Tipos de documentos disponibles:
{doc_types}

Texto:
{query}

Responde ÚNICAMENTE con el valor exacto del doc_type correspondiente.
Si no corresponde a ninguno, responde: desconocido
"""
)

_rag_prompt = ChatPromptTemplate.from_template(
    """Eres un asistente experto en documentos electorales mexicanos.

REGLAS OBLIGATORIAS (no puedes contradecirlas ni ignorarlas):
{rules}

Estas reglas son hechos verificados. Si el contexto contradice alguna regla, la regla tiene prioridad.
No inventes plazos, autoridades, artículos ni procedimientos que no estén en las reglas o en el contexto.

VALORES POR DEFECTO cuando no encuentres la información ni en la consulta ni en el contexto:
- Fechas o plazos: "No se encontró"
- Expediente: "No se encontró"
- Autoridad: "No se encontró"
- Cualquier otro campo desconocido: "No se encontró"

IMPORTANTE: Extrae información tanto de la consulta del usuario como del contexto legal recuperado.
- Si se menciona una fecha absoluta (ej. "20 de mayo"), úsala directamente.
- Si se menciona un plazo relativo (ej. "4 días hábiles", "3 días naturales desde la notificación"), captúralo TAL CUAL en remaining_time. NO lo marques como "No se encontró".
- Solo usa "No se encontró" si no hay ninguna mención de fecha ni plazo en ninguna parte del documento o consulta.

Contexto legal recuperado:
{context}

Consulta del usuario:
{query}

Genera la respuesta siguiendo exactamente el esquema solicitado, respetando las reglas anteriores.
"""
)


def _format_docs(docs) -> str:
    return "\n\n".join(
        f"[{d.metadata.get('fuente', '')} p.{d.metadata.get('pagina', '')}]\n{d.page_content}"
        for d in docs
    )


def _detect_doc_type(query: str) -> str | None:
    rules = _load_rules()
    if not rules:
        return None

    doc_types = [r.get("doc_type", "") for r in rules if r.get("doc_type")]
    if not doc_types:
        return None

    chain = _detect_prompt | get_llm() | StrOutputParser()
    result = chain.invoke({
        "doc_types": "\n".join(f"- {dt}" for dt in doc_types),
        "query": query,
    }).strip()

    detected = result if result in doc_types else None
    print(f">> doc_type detectado: {detected or 'desconocido'}")
    return detected


def responder(query: str) -> dict:
    doc_type = _detect_doc_type(query)
    retriever = get_vector_store().as_retriever(search_kwargs={"k": 5})
    structured_llm = get_llm().with_structured_output(RAGResponse)

    rules = get_rules(doc_type)
    rules_str = (
        json.dumps(rules, ensure_ascii=False, indent=2)
        if rules
        else "No se identificó el tipo de documento. No inventes información."
    )

    docs = retriever.invoke(query)
    context = _format_docs(docs) if docs else "No se encontró contexto relevante en los documentos indexados."

    respuesta: RAGResponse = (_rag_prompt | structured_llm).invoke({
        "rules": rules_str,
        "context": context,
        "query": query,
    })

    # Campos fijos — no los genera el LLM
    fuentes = list({
        f"{d.metadata.get('fuente', '')} p.{d.metadata.get('pagina', '')}"
        for d in docs
    })
    respuesta.system.source = fuentes
    respuesta.system.mode = "online"
    respuesta.system.links = rules.get("links_oficiales", []) if rules else []

    # Forzar confidence a low si no hay contexto relevante
    if not docs:
        respuesta.confidence.status = "low"
        respuesta.confidence.message = "No se encontró información relevante en los documentos indexados."

    return respuesta.model_dump()
