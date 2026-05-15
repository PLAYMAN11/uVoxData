import os
from langchain_groq import ChatGroq

_GROQ_MODEL = os.getenv("GROQ_MODEL", "llama-3.3-70b-versatile")
_GROQ_API_KEY = os.getenv("GROQ_API_KEY", "")

llm = ChatGroq(
    model=_GROQ_MODEL,
    api_key=_GROQ_API_KEY,
    temperature=0.2,
)
