from pathlib import Path
import pdfplumber
from langchain_core.documents import Document
from langchain_text_splitters import RecursiveCharacterTextSplitter

from Core.vector_store import get_vector_store

_DOCS_PATH = Path("/app/docs_oficiales")

_splitter = RecursiveCharacterTextSplitter(
    chunk_size=500,
    chunk_overlap=50,
)


def _ya_indexado(fuente: str) -> bool:
    """Verifica si una fuente ya está indexada en ChromaDB."""
    resultado = get_vector_store().get(where={"fuente": fuente}, limit=1)
    return len(resultado.get("ids", [])) > 0


def ingestar_texto(texto: str, fuente: str) -> int:
    """Indexa texto plano enviado por la aplicación (uploads del usuario)."""
    if _ya_indexado(fuente):
        print(f">> '{fuente}' ya indexado, saltando.")
        return 0

    doc = Document(page_content=texto, metadata={"fuente": fuente})
    chunks = _splitter.split_documents([doc])

    if chunks:
        get_vector_store().add_documents(chunks)

    return len(chunks)


def ingestar_pdf(ruta: str | Path, fuente: str | None = None) -> int:
    """Indexa un PDF directamente desde el servidor."""
    ruta = Path(ruta)
    fuente = fuente or ruta.name

    if _ya_indexado(fuente):
        print(f">> '{fuente}' ya indexado, saltando.")
        return 0

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
        get_vector_store().add_documents(chunks)

    return len(chunks)


def ingestar_docs_oficiales() -> dict:
    """Indexa todos los PDFs en la carpeta docs_oficiales al arrancar."""
    if not _DOCS_PATH.exists():
        print(f">> Carpeta {_DOCS_PATH} no encontrada, saltando.")
        return {}

    resultados = {}
    for pdf in _DOCS_PATH.glob("*.pdf"):
        print(f">> Indexando {pdf.name}...")
        n = ingestar_pdf(pdf)
        resultados[pdf.name] = n
    return resultados
