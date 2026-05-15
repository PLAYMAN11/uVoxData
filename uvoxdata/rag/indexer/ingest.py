import pdfplumber
from pathlib import Path
from indexer.embedder import agregar_fragmentos


def ingestar_pdf(ruta: str | Path, fuente: str | None = None) -> int:
    ruta = Path(ruta)
    fuente = fuente or ruta.name
    fragmentos = []

    with pdfplumber.open(ruta) as pdf:
        for i, pagina in enumerate(pdf.pages):
            texto = pagina.extract_text() or ""
            texto = texto.strip()
            if texto:
                fragmentos.append({"texto": texto, "fuente": fuente, "pagina": i + 1})

    if fragmentos:
        agregar_fragmentos(fragmentos)

    return len(fragmentos)
