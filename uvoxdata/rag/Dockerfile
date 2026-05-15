FROM python:3.12-slim

WORKDIR /app

RUN apt-get update && apt-get install -y --no-install-recommends \
    poppler-utils \
    build-essential \
    g++ \
    && rm -rf /var/lib/apt/lists/*

COPY requirements.txt .

# Instalar PyTorch CPU-only primero para evitar descargar drivers NVIDIA
RUN pip install --no-cache-dir \
    torch==2.3.1 --index-url https://download.pytorch.org/whl/cpu

RUN pip install --no-cache-dir -r requirements.txt

COPY . .

CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8002"]
