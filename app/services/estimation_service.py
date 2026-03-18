from app.schemas.estimation import PublicEstimateRequest, PublicEstimateResponse
from app.services.perplexity_service import PerplexityService


class EstimationService:
    def __init__(self, perplexity_service: PerplexityService):
        self.perplexity_service = perplexity_service

    async def estimate(self, payload: PublicEstimateRequest) -> PublicEstimateResponse:
        market_range = await self.perplexity_service.fetch_market_range(
            city=payload.city,
            property_type=payload.property_type,
        )
        area = payload.area_sqm

        low_value = round(market_range.low * area, 2)
        mid_value = round(((market_range.low + market_range.mid + market_range.high) / 3) * area, 2)
        high_value = round(market_range.high * area, 2)

        return PublicEstimateResponse(
            low=low_value,
            mid=mid_value,
            high=high_value,
            per_sqm_low=round(market_range.low, 2),
            per_sqm_mid=round(market_range.mid, 2),
            per_sqm_high=round(market_range.high, 2),
        )

    @staticmethod
    def score_temperature(urgency: str, motivation: str) -> str:
        urgency_points = {"rapide": 3, "moyen": 2, "long": 1}
        motivation_points = {
            "vente": 3,
            "succession": 2,
            "divorce": 3,
            "investissement": 1,
        }
        score = urgency_points.get(urgency, 1) + motivation_points.get(motivation, 1)
        if score >= 5:
            return "chaud"
        if score >= 3:
            return "tiède"
        return "froid"
