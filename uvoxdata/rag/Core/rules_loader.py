import json
import os
from pathlib import Path

_RULES_PATH = Path("/app/rules.json")
_cache: list[dict] = []
_cache_mtime: float = 0.0


def _load_rules() -> list[dict]:
    global _cache, _cache_mtime

    if not _RULES_PATH.exists():
        return []

    mtime = os.path.getmtime(_RULES_PATH)
    if mtime != _cache_mtime:
        with open(_RULES_PATH, encoding="utf-8") as f:
            data = json.load(f)
        _cache = data if isinstance(data, list) else [data]
        _cache_mtime = mtime
        print(">> rules.json recargado.")

    return _cache


def get_rules(doc_type: str | None = None) -> dict | None:
    rules = _load_rules()
    if not rules:
        return None
    if doc_type is None or doc_type == "desconocido":
        return None
    for rule in rules:
        if rule.get("doc_type") == doc_type:
            return rule
    return None
