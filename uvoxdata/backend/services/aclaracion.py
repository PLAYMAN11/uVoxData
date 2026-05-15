from dataclasses import dataclass, field

AMBIGUOUS_KEYWORDS = ["impugnar", "recurso", "plazo", "resolución"]


@dataclass
class Aclaracion:
    pregunta: str
    opciones: list[str] = field(default_factory=list)


def necesita_aclaracion(pregunta: str) -> bool:
    q = pregunta.lower()
    return sum(1 for kw in AMBIGUOUS_KEYWORDS if kw in q) >= 2


async def generar_aclaracion(pregunta: str) -> Aclaracion:
    return Aclaracion(
        pregunta="¿A qué tipo de proceso electoral se refiere tu consulta?",
        opciones=[
            "Elección local (Chihuahua)",
            "Elección federal",
            "Proceso interno de partido",
        ],
    )
