import chromadb
from sentence_transformers import SentenceTransformer

_client = chromadb.PersistentClient(path="/app/chroma_db")
_collection = _client.get_or_create_collection("documentos_electorales")
_model = SentenceTransformer("sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2")


def buscar(query: str, top_k: int = 5) -> list[dict]:
    embedding = _model.encode([query]).tolist()
    resultados = _collection.query(
        query_embeddings=embedding,
        n_results=top_k,
        include=["documents", "metadatas", "distances"],
    )

    items = []
    for doc, meta, dist in zip(
        resultados["documents"][0],
        resultados["metadatas"][0],
        resultados["distances"][0],
    ):
        items.append({
            "texto": doc,
            "fuente": meta.get("fuente", ""),
            "pagina": meta.get("pagina", 0),
            "score": round(1 - dist, 4),
        })

    return items
