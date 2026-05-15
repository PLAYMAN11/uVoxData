from functools import lru_cache
from langchain_huggingface import HuggingFaceEmbeddings

_MODEL_NAME = "sentence-transformers/paraphrase-multilingual-mpnet-base-v2"


@lru_cache(maxsize=1)
def get_embedder() -> HuggingFaceEmbeddings:
    print(">> Cargando modelo de embeddings...")
    embedder = HuggingFaceEmbeddings(model_name=_MODEL_NAME)
    print(">> Modelo cargado.")
    return embedder
