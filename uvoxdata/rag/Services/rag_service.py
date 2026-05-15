import json
from langchain_core.prompts import ChatPromptTemplate
from langchain_core.runnables import RunnablePassthrough

from Core.vector_store import get_vector_store
from Core.llm_client import get_llm
from Core.schemas import RAGResponse
from Core.rules_loader import get_rules


_prompt = ChatPromptTemplate.from_template(
    """Eres un asistente experto en documentos electorales mexicanos.

REGLAS OBLIGATORIAS (no puedes contradecirlas ni ignorarlas):
{rules}

Estas reglas son hechos verificados. Si el contexto contradice alguna regla, la regla tiene prioridad.
No inventes plazos, autoridades, artículos ni procedimientos que no estén en las reglas o en el contexto.

Contexto legal recuperado:
{context}

Consulta del usuario:
{question}

Genera la respuesta siguiendo exactamente el esquema solicitado, respetando las reglas anteriores.
"""
)


def _format_docs(docs) -> str:
    return "\n\n".join(
        f"[{d.metadata.get('fuente', '')} p.{d.metadata.get('pagina', '')}]\n{d.page_content}"
        for d in docs
    )


def responder(query: str, doc_type: str | None = None) -> dict:
    retriever = get_vector_store().as_retriever(search_kwargs={"k": 5})
    structured_llm = get_llm().with_structured_output(RAGResponse)

    rules = get_rules(doc_type)
    rules_str = json.dumps(rules, ensure_ascii=False, indent=2) if rules else "No hay reglas específicas disponibles."

    docs = retriever.invoke(query)
    context = _format_docs(docs)

    chain = (
        _prompt
        | structured_llm
    )

    respuesta: RAGResponse = chain.invoke({
        "rules": rules_str,
        "context": context,
        "question": query,
    })

    return respuesta.model_dump()
