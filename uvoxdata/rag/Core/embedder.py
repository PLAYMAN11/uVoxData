from langchain_huggingface import HuggingFaceEmbeddings

_MODEL_NAME = "sentence-transformers/paraphrase-multilingual-mpnet-base-v2"

embedder = HuggingFaceEmbeddings(model_name=_MODEL_NAME)
