from __future__ import annotations

import re
from dataclasses import dataclass

import httpx

from app.core.config import settings


@dataclass
class GeneratedArticle:
    title: str
    intro: str
    body_markdown: str
    conclusion: str
    faq: str


class AIService:
    OPENAI_URL = "https://api.openai.com/v1/responses"

    async def generate_seo_article(self, city: str, market_snapshot: str, topic_hint: str) -> GeneratedArticle:
        if not settings.openai_api_key:
            return self._fallback(city, topic_hint, market_snapshot)

        prompt = f"""
Tu es un expert SEO immobilier.
Rédige un article structuré avec :
- Titre
- Intro
- Sections H2/H3
- Conclusion
- FAQ
Ville: {city}
Sujet: {topic_hint}
Tendances: {market_snapshot}
Réponds en markdown avec des sections explicites:
# TITLE
# INTRO
# BODY
# CONCLUSION
# FAQ
"""
        headers = {
            "Authorization": f"Bearer {settings.openai_api_key}",
            "Content-Type": "application/json",
        }
        payload = {
            "model": "gpt-4.1-mini",
            "input": prompt,
            "temperature": 0.4,
        }
        async with httpx.AsyncClient(timeout=30) as client:
            response = await client.post(self.OPENAI_URL, headers=headers, json=payload)
            response.raise_for_status()
            text = response.json()["output"][0]["content"][0]["text"]

        return self._parse_markdown_sections(text)

    def _parse_markdown_sections(self, text: str) -> GeneratedArticle:
        def section(name: str) -> str:
            pattern = rf"# {name}\\n(.*?)(?:\\n# |$)"
            match = re.search(pattern, text, re.DOTALL)
            return (match.group(1).strip() if match else "")

        title = section("TITLE").splitlines()[0] if section("TITLE") else "Article Immobilier"
        return GeneratedArticle(
            title=title,
            intro=section("INTRO"),
            body_markdown=section("BODY"),
            conclusion=section("CONCLUSION"),
            faq=section("FAQ"),
        )

    def _fallback(self, city: str, topic_hint: str, market_snapshot: str) -> GeneratedArticle:
        return GeneratedArticle(
            title=f"Marché immobilier à {city}: {topic_hint}",
            intro=f"Analyse locale du marché immobilier à {city}.",
            body_markdown=(
                "## Tendances locales\n"
                f"{market_snapshot}\n\n"
                "## Conseils vendeurs\n"
                "Préparer un dossier complet améliore la vitesse de vente.\n\n"
                "### Points de vigilance\n"
                "Suivre les taux de crédit et la tension de la demande locale."
            ),
            conclusion="Un accompagnement par un expert local optimise la stratégie de vente.",
            faq="Q: Est-ce le bon moment pour vendre ?\nR: Cela dépend de l'urgence et du quartier.",
        )
