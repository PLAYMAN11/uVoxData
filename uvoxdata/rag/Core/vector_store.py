from langchain_chroma import Chroma
from Core.embedder import embedder

_CHROMA_PATH = "/app/chroma_db"
_COLLECTION_NAME = "documentos_electorales"

vector_store = Chroma(
    collection_name=_COLLECTION_NAME,
    embedding_function=embedder,
    persist_directory=_CHROMA_PATH,
)
