from typing import Literal, Optional
from pydantic import BaseModel


class Status(BaseModel):
    priority: Literal["urgente", "requiere_atencion", "informativo"]
    headline: str
    remaining_time: str


class DocumentInfo(BaseModel):
    type: str
    authority: str


class Orientation(BaseModel):
    why_you_received_this: str
    risk_if_no_action: str
    what_you_can_do_now: list[str]


class ClaritySupport(BaseModel):
    message: str
    cta_primary: str
    cta_secondary: str


class Confidence(BaseModel):
    status: Literal["high", "medium", "low"]
    message: str


class SystemInfo(BaseModel):
    source: list[str]
    mode: str
    links: list[str]


class RAGResponse(BaseModel):
    status: Status
    document: DocumentInfo
    orientation: Orientation
    clarity_support: ClaritySupport
    confidence: Confidence
    system: SystemInfo
