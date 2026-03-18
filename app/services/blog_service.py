from __future__ import annotations

from sqlalchemy.orm import Session
from slugify import slugify

from app.models.entities import BlogArticle
from app.schemas.blog import ArticleCreate, ArticleGenerationInput
from app.services.ai_service import AIService
from app.services.perplexity_service import PerplexityService


class BlogService:
    def __init__(self, ai_service: AIService, perplexity_service: PerplexityService):
        self.ai_service = ai_service
        self.perplexity_service = perplexity_service

    async def generate_article(self, db: Session, payload: ArticleGenerationInput) -> BlogArticle:
        trend = await self.perplexity_service.fetch_market_range(payload.city, "appartement")
        snapshot = (
            f"Prix m² bas: {trend.low:.0f}€, moyen: {trend.mid:.0f}€, haut: {trend.high:.0f}€ "
            f"sur {payload.city}."
        )
        generated = await self.ai_service.generate_seo_article(payload.city, snapshot, payload.topic_hint)
        return self.create_article(
            db,
            ArticleCreate(
                title=generated.title,
                intro=generated.intro,
                body_markdown=generated.body_markdown,
                conclusion=generated.conclusion,
                faq=generated.faq,
                is_published=True,
            ),
            source_snapshot=snapshot,
        )

    def create_article(self, db: Session, payload: ArticleCreate, source_snapshot: str = "") -> BlogArticle:
        base_slug = slugify(payload.title)
        slug = base_slug
        idx = 1
        while db.query(BlogArticle).filter(BlogArticle.slug == slug).first():
            idx += 1
            slug = f"{base_slug}-{idx}"

        article = BlogArticle(
            title=payload.title,
            slug=slug,
            intro=payload.intro,
            body_markdown=payload.body_markdown,
            conclusion=payload.conclusion,
            faq=payload.faq,
            source_snapshot=source_snapshot,
            is_published=payload.is_published,
        )
        db.add(article)
        db.commit()
        db.refresh(article)
        return article
