from langchain_core.prompts import ChatPromptTemplate
from langchain_core.output_parsers import StrOutputParser
from langchain_core.runnables import RunnablePassthrough

from Core.vector_store import vector_store
from Core.llm_client import llm

_retriever = vector_store.as_retriever(search_kwargs={"k": 5})

_prompt = ChatPromptTemplate.from_template(
    """Eres un asistente experto en datos electorales mexicanos.
Responde de forma clara y precisa usando únicamente el contexto proporcionado.
Si la información no está en el contexto, indícalo explícitamente.

Contexto:
{context}

Pregunta: {question}
"""
)


def _format_docs(docs) -> str:
    return "\n\n".join(
        f"[{d.metadata.get('fuente', '')} p.{d.metadata.get('pagina', '')}]\n{d.page_content}"
        for d in docs
    )


_chain = (
    {"context": _retriever | _format_docs, "question": RunnablePassthrough()}
    | _prompt
    | llm
    | StrOutputParser()
)


def responder(query: str) -> dict:
    docs = _retriever.invoke(query)
    respuesta = _chain.invoke(query)

    fuentes = [
        {"fuente": d.metadata.get("fuente", ""), "pagina": d.metadata.get("pagina", 0)}
        for d in docs
    ]

    return {"respuesta": respuesta, "fuentes": fuentes}
