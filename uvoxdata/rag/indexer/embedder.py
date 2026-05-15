import chromadb
from sentence_transformers import SentenceTransformer

_client = chromadb.PersistentClient(path="/app/chroma_db")
_collection = _client.get_or_create_collection("documentos_electorales")
_model = SentenceTransformer("sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2")


def agregar_fragmentos(fragmentos: list[dict]) -> None:
    textos = [f["texto"] for f in fragmentos]
    embeddings = _model.encode(textos).tolist()
    ids = [f"{f['fuente']}_p{f['pagina']}" for f in fragmentos]
    metadatas = [{"fuente": f["fuente"], "pagina": f["pagina"]} for f in fragmentos]

    _collection.add(
        ids=ids,
        embeddings=embeddings,
        documents=textos,
        metadatas=metadatas,
    )
