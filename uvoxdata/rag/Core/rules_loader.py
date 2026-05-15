import json
from pathlib import Path
from functools import lru_cache

_RULES_PATH = Path("/app/rules.json")


@lru_cache(maxsize=1)
def _load_rules() -> list[dict]:
    if not _RULES_PATH.exists():
        return []
    with open(_RULES_PATH, encoding="utf-8") as f:
        data = json.load(f)
    return data if isinstance(data, list) else [data]


def get_rules(doc_type: str | None = None) -> dict | None:
    rules = _load_rules()
    if not rules:
        return None
    if doc_type is None:
        return rules[0]
    for rule in rules:
        if rule.get("doc_type") == doc_type:
            return rule
    return None
