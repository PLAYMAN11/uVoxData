from functools import lru_cache
from langchain_chroma import Chroma
from Core.embedder import get_embedder

_CHROMA_PATH = "/app/chroma_db"
_COLLECTION_NAME = "documentos_electorales"


@lru_cache(maxsize=1)
def get_vector_store() -> Chroma:
    print(">> Iniciando vector store...")
    store = Chroma(
        collection_name=_COLLECTION_NAME,
        embedding_function=get_embedder(),
        persist_directory=_CHROMA_PATH,
    )
    print(">> Vector store listo.")
    return store
