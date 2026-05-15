def rerank(resultados: list[dict], query: str) -> list[dict]:
    """Ordena resultados por score descendente (puede reemplazarse con un modelo cross-encoder)."""
    return sorted(resultados, key=lambda r: r.get("score", 0), reverse=True)
