from pathlib import Path
import pdfplumber
import pytesseract
from pdf2image import convert_from_path
from PIL import Image

_LANG = "spa+eng"

_TIPOS_IMAGEN = {"image/jpeg", "image/jpg", "image/png", "image/webp", "image/heic", "image/heif"}
_TIPOS_PDF = {"application/pdf"}


def _ocr_pdf_escaneado(ruta: Path) -> str:
    """Convierte páginas del PDF a imágenes y aplica OCR."""
    imagenes = convert_from_path(str(ruta))
    partes = [pytesseract.image_to_string(img, lang=_LANG) for img in imagenes]
    return "\n\n".join(p.strip() for p in partes if p.strip())


def extraer_texto_pdf(ruta: Path) -> str:
    """Extrae texto de un PDF: texto nativo primero, OCR como fallback."""
    partes = []

    with pdfplumber.open(ruta) as pdf:
        for pagina in pdf.pages:
            texto = (pagina.extract_text() or "").strip()
            partes.append(texto)

    texto = "\n\n".join(p for p in partes if p)

    if not texto.strip():
        print(">> PDF sin texto nativo, aplicando OCR...")
        texto = _ocr_pdf_escaneado(ruta)

    return texto.strip()


def extraer_texto_imagen(ruta: Path) -> str:
    """Aplica OCR a una imagen (JPG, PNG, HEIC, foto de cámara)."""
    img = Image.open(ruta).convert("RGB")
    return pytesseract.image_to_string(img, lang=_LANG).strip()


def extraer_texto(ruta: str | Path, content_type: str) -> str:
    """Extrae texto según el tipo de archivo."""
    ruta = Path(ruta)

    if content_type in _TIPOS_PDF:
        return extraer_texto_pdf(ruta)
    elif content_type in _TIPOS_IMAGEN:
        return extraer_texto_imagen(ruta)
    else:
        raise ValueError(f"Tipo de archivo no soportado: {content_type}")
