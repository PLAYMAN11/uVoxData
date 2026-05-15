from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware

from routers import consulta, documento

app = FastAPI(title="uVoxData Backend", version="0.1.0")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

app.include_router(consulta.router)
app.include_router(documento.router)


@app.get("/health")
def health():
    return {"status": "ok"}
