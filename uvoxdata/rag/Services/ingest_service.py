from pathlib import Path
import pdfplumber
from langchain_core.documents import Document
from langchain_text_splitters import RecursiveCharacterTextSplitter

from Core.vector_store import vector_store

_DOCS_PATH = Path("/app/docs_oficiales")

_splitter = RecursiveCharacterTextSplitter(
    chunk_size=500,
    chunk_overlap=50,
)


def ingestar_texto(texto: str, fuente: str) -> int:
    """Indexa texto plano enviado por la aplicación (uploads del usuario)."""
    doc = Document(page_content=texto, metadata={"fuente": fuente})
    chunks = _splitter.split_documents([doc])

    if chunks:
        vector_store.add_documents(chunks)

    return len(chunks)


def ingestar_pdf(ruta: str | Path, fuente: str | None = None) -> int:
    """Indexa un PDF directamente desde el servidor."""
    ruta = Path(ruta)
    fuente = fuente or ruta.name
    docs = []

    with pdfplumber.open(ruta) as pdf:
        for i, pagina in enumerate(pdf.pages):
            texto = (pagina.extract_text() or "").strip()
            if texto:
                docs.append(Document(
                    page_content=texto,
                    metadata={"fuente": fuente, "pagina": i + 1},
                ))

    chunks = _splitter.split_documents(docs)
    if chunks:
        vector_store.add_documents(chunks)

    return len(chunks)


def ingestar_docs_oficiales() -> dict:
    """Indexa todos los PDFs en la carpeta docs_oficiales al arrancar."""
    resultados = {}
    for pdf in _DOCS_PATH.glob("*.pdf"):
        n = ingestar_pdf(pdf)
        resultados[pdf.name] = n
    return resultados
