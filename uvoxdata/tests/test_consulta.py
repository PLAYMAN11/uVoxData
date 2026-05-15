import pytest
from httpx import AsyncClient, ASGITransport

import sys
import os
sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "backend"))

from main import app


@pytest.mark.asyncio
async def test_health():
    async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as client:
        resp = await client.get("/health")
    assert resp.status_code == 200
    assert resp.json() == {"status": "ok"}


@pytest.mark.asyncio
async def test_consulta_responde():
    async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as client:
        resp = await client.post("/consulta", json={"pregunta": "¿Cuál es el plazo para impugnar?"})
    assert resp.status_code == 200
    data = resp.json()
    assert "respuesta" in data or data.get("necesita_aclaracion") is True


@pytest.mark.asyncio
async def test_consulta_ambigua_genera_aclaracion():
    async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as client:
        resp = await client.post(
            "/consulta",
            json={"pregunta": "¿Cuál es el plazo para impugnar un recurso de resolución?"},
        )
    assert resp.status_code == 200
    data = resp.json()
    assert data.get("necesita_aclaracion") is True
    assert data.get("opciones")
