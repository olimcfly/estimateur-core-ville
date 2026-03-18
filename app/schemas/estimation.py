from pydantic import BaseModel, EmailStr, Field


class PublicEstimateRequest(BaseModel):
    city: str
    property_type: str
    area_sqm: float = Field(gt=0)
    rooms: int = Field(gt=0)


class PublicEstimateResponse(BaseModel):
    low: float
    mid: float
    high: float
    per_sqm_low: float
    per_sqm_mid: float
    per_sqm_high: float


class AdvancedLeadCreate(BaseModel):
    city: str
    property_type: str
    area_sqm: float = Field(gt=0)
    rooms: int = Field(gt=0)
    estimated_low: float
    estimated_mid: float
    estimated_high: float

    name: str
    email: EmailStr
    phone: str
    address: str
    urgency: str
    motivation: str
    owner_confirmed: bool = True
