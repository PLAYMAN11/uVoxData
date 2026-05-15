import json
import os
import pytest


SCENARIOS_PATH = os.path.join(os.path.dirname(__file__), "..", "offline", "scenarios.json")


def load_scenarios():
    with open(SCENARIOS_PATH) as f:
        return json.load(f)


def test_scenarios_validos():
    scenarios = load_scenarios()
    assert isinstance(scenarios, list)
    assert len(scenarios) > 0


def test_estructura_escenario():
    for s in load_scenarios():
        assert "id" in s, f"Falta 'id' en escenario: {s}"
        assert "palabras_clave" in s, f"Falta 'palabras_clave' en escenario: {s}"
        assert "respuesta" in s, f"Falta 'respuesta' en escenario: {s}"
        assert isinstance(s["palabras_clave"], list)
        assert len(s["palabras_clave"]) > 0


def test_busqueda_offline():
    scenarios = load_scenarios()
    query = "plazo para impugnar"
    match = next(
        (s for s in scenarios if any(k in query for k in s["palabras_clave"])),
        None,
    )
    assert match is not None
    assert "respuesta" in match
