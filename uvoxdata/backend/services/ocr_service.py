from pathlib import Path
import pdfplumber
import pytesseract
from pdf2image import convert_from_path
from PIL import Image, ImageFilter, ImageEnhance

_LANG = "spa+eng"
_TESSERACT_CONFIG = "--oem 3 --psm 6"

_TIPOS_IMAGEN = {"image/jpeg", "image/jpg", "image/png", "image/webp", "image/heic", "image/heif"}
_TIPOS_PDF = {"application/pdf"}


def _preprocesar(img: Image.Image) -> Image.Image:
    """Mejora la imagen para aumentar precisión del OCR."""
    img = img.convert("L")                                    # Escala de grises
    img = ImageEnhance.Contrast(img).enhance(2.0)            # Aumentar contraste
    img = ImageEnhance.Sharpness(img).enhance(2.0)           # Aumentar nitidez
    img = img.filter(ImageFilter.MedianFilter(size=3))        # Reducir ruido
    return img


def _ocr_imagen(img: Image.Image) -> str:
    return pytesseract.image_to_string(
        _preprocesar(img), lang=_LANG, config=_TESSERACT_CONFIG
    ).strip()


def _ocr_pdf_escaneado(ruta: Path) -> str:
    """Convierte páginas del PDF a imágenes, preprocesa y aplica OCR."""
    imagenes = convert_from_path(str(ruta), dpi=300)
    partes = [_ocr_imagen(img) for img in imagenes]
    return "\n\n".join(p for p in partes if p)


def extraer_texto_pdf(ruta: Path) -> str:
    """Extrae texto de un PDF combinando pdfplumber y OCR para mayor precisión."""
    partes_plumber = []

    with pdfplumber.open(ruta) as pdf:
        for pagina in pdf.pages:
            texto = (pagina.extract_text() or "").strip()
            partes_plumber.append(texto)

    texto_plumber = "\n\n".join(p for p in partes_plumber if p)

    # Siempre correr OCR también para capturar lo que pdfplumber pierda
    print(">> Aplicando OCR para complementar extracción...")
    texto_ocr = _ocr_pdf_escaneado(ruta)

    # Combinar ambos — OCR como fuente principal si pdfplumber dio resultado corto
    if len(texto_ocr) > len(texto_plumber):
        return texto_ocr.strip()
    return (texto_plumber + "\n\n" + texto_ocr).strip()


def extraer_texto_imagen(ruta: Path) -> str:
    """Aplica OCR a una imagen (JPG, PNG, HEIC, foto de cámara)."""
    img = Image.open(ruta).convert("RGB")
    return _ocr_imagen(img)


def extraer_texto(ruta: str | Path, content_type: str) -> str:
    """Extrae texto según el tipo de archivo."""
    ruta = Path(ruta)

    if content_type in _TIPOS_PDF:
        return extraer_texto_pdf(ruta)
    elif content_type in _TIPOS_IMAGEN:
        return extraer_texto_imagen(ruta)
    else:
        raise ValueError(f"Tipo de archivo no soportado: {content_type}")
