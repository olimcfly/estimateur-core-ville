from __future__ import annotations

import json
from dataclasses import dataclass

import httpx

from app.core.config import settings


@dataclass
class MarketRange:
    low: float
    mid: float
    high: float


class PerplexityService:
    API_URL = "https://api.perplexity.ai/chat/completions"

    async def fetch_market_range(self, city: str, property_type: str) -> MarketRange:
        if not settings.perplexity_api_key:
            return self._fallback(city, property_type)

        prompt = (
            "Donne un JSON strict avec low, mid, high (prix m2 en EUR) "
            f"pour {property_type} à {city}."
        )
        headers = {
            "Authorization": f"Bearer {settings.perplexity_api_key}",
            "Content-Type": "application/json",
        }
        payload = {
            "model": "sonar-pro",
            "messages": [{"role": "user", "content": prompt}],
            "temperature": 0.1,
        }
        async with httpx.AsyncClient(timeout=20) as client:
            response = await client.post(self.API_URL, headers=headers, json=payload)
            response.raise_for_status()
            data = response.json()

        content = data["choices"][0]["message"]["content"]
        parsed = json.loads(content)
        return MarketRange(low=float(parsed["low"]), mid=float(parsed["mid"]), high=float(parsed["high"]))

    def _fallback(self, city: str, property_type: str) -> MarketRange:
        baseline = 4200
        if "bordeaux" in city.lower():
            baseline = 4800
        if "maison" in property_type.lower():
            baseline *= 1.08
        return MarketRange(low=baseline * 0.85, mid=baseline, high=baseline * 1.18)
