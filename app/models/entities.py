from datetime import datetime

from sqlalchemy import Boolean, DateTime, Float, Integer, String, Text
from sqlalchemy.orm import Mapped, mapped_column

from app.core.database import Base


class Lead(Base):
    __tablename__ = "leads"

    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    city: Mapped[str] = mapped_column(String(120), nullable=False)
    property_type: Mapped[str] = mapped_column(String(80), nullable=False)
    area_sqm: Mapped[float] = mapped_column(Float, nullable=False)
    rooms: Mapped[int] = mapped_column(Integer, nullable=False)
    estimated_low: Mapped[float] = mapped_column(Float, nullable=False)
    estimated_mid: Mapped[float] = mapped_column(Float, nullable=False)
    estimated_high: Mapped[float] = mapped_column(Float, nullable=False)

    name: Mapped[str] = mapped_column(String(120), nullable=False)
    email: Mapped[str] = mapped_column(String(160), nullable=False)
    phone: Mapped[str] = mapped_column(String(40), nullable=False)
    address: Mapped[str] = mapped_column(String(255), nullable=False)
    urgency: Mapped[str] = mapped_column(String(40), nullable=False)
    motivation: Mapped[str] = mapped_column(String(80), nullable=False)
    owner_confirmed: Mapped[bool] = mapped_column(Boolean, default=True)

    bant_budget: Mapped[float] = mapped_column(Float, nullable=False)
    bant_authority: Mapped[str] = mapped_column(String(120), nullable=False)
    bant_need: Mapped[str] = mapped_column(String(120), nullable=False)
    bant_timing: Mapped[str] = mapped_column(String(120), nullable=False)

    temperature: Mapped[str] = mapped_column(String(20), default="froid")
    status: Mapped[str] = mapped_column(String(20), default="nouveau")
    internal_notes: Mapped[str] = mapped_column(Text, default="")
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)


class BlogArticle(Base):
    __tablename__ = "blog_articles"

    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    title: Mapped[str] = mapped_column(String(220), nullable=False)
    slug: Mapped[str] = mapped_column(String(220), unique=True, nullable=False, index=True)
    intro: Mapped[str] = mapped_column(Text, nullable=False)
    body_markdown: Mapped[str] = mapped_column(Text, nullable=False)
    conclusion: Mapped[str] = mapped_column(Text, nullable=False)
    faq: Mapped[str] = mapped_column(Text, nullable=False)
    source_snapshot: Mapped[str] = mapped_column(Text, default="")
    is_published: Mapped[bool] = mapped_column(Boolean, default=True)
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    updated_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
