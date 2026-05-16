"""Mapeo RAG → ConsultaResponse (sin levantar servicios)."""
import unittest

from services.respuesta import (
    _merge_pregunta_contexto,
    rag_payload_to_consulta_response,
)


class TestMergeContexto(unittest.TestCase):
    def test_solo_pregunta(self):
        self.assertEqual(_merge_pregunta_contexto("Hola", None), "Hola")
        self.assertEqual(_merge_pregunta_contexto("Hola", ""), "Hola")

    def test_con_contexto(self):
        out = _merge_pregunta_contexto("¿Y el plazo?", "Usuario preguntó por multa.")
        self.assertIn("¿Y el plazo?", out)
        self.assertIn("Usuario preguntó", out)
        self.assertIn("Contexto de la conversación", out)


class TestRagPayloadMapper(unittest.TestCase):
    def test_mapeo_completo_modo_full(self):
        rag = {
            "document": {
                "type": "Notificación",
                "expedient": "123",
                "authority": "TEE Chihuahua",
            },
            "status": {
                "priority": "urgente",
                "headline": "Actuar pronto",
                "remaining_time": "5 días hábiles",
            },
            "orientation": {
                "why_you_received_this": "Por un recurso interpuesto.",
                "risk_if_no_action": "Puede perderse el derecho.",
                "what_you_can_do_now": ["Presentar pruebas", "Acudir al módulo"],
            },
            "clarity_support": {"message": "Si tienes dudas, acude al TEE."},
            "confidence": {"status": "high", "message": "ok"},
            "system": {"source": ["Doc A p.1", "Doc B p.2"], "mode": "online", "links": []},
        }
        r = rag_payload_to_consulta_response(rag)
        self.assertEqual(r.modo, "full")
        self.assertEqual(len(r.fuentes), 2)
        self.assertIn("Notificación", r.respuesta or "")
        self.assertIn("TEE Chihuahua", r.respuesta or "")
        self.assertIn("Presentar pruebas", r.respuesta or "")

    def test_sin_fuentes_degradado(self):
        rag = {
            "document": {"type": "X", "expedient": "No se encontró", "authority": "No se encontró"},
            "status": {
                "priority": "informativo",
                "headline": "No se encontró",
                "remaining_time": "No se encontró",
            },
            "orientation": {
                "why_you_received_this": "Sin detalle.",
                "risk_if_no_action": "",
                "what_you_can_do_now": [],
            },
            "clarity_support": {},
            "confidence": {"status": "low", "message": "sin contexto"},
            "system": {"source": [], "mode": "online", "links": []},
        }
        r = rag_payload_to_consulta_response(rag)
        self.assertEqual(r.modo, "degradado")


if __name__ == "__main__":
    unittest.main()
