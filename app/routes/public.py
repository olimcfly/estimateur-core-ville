from fastapi import APIRouter, Depends, Form, Request
from fastapi.responses import HTMLResponse
from fastapi.templating import Jinja2Templates
from sqlalchemy.orm import Session

from app.core.database import get_db
from app.models.entities import Lead
from app.schemas.estimation import AdvancedLeadCreate, PublicEstimateRequest
from app.services.estimation_service import EstimationService
from app.services.perplexity_service import PerplexityService

router = APIRouter()
templates = Jinja2Templates(directory="app/templates")


@router.get("/", response_class=HTMLResponse)
async def home(request: Request):
    return templates.TemplateResponse("estimation/home.html", {"request": request})


@router.post("/estimate", response_class=HTMLResponse)
async def estimate(
    request: Request,
    city: str = Form(...),
    property_type: str = Form(...),
    area_sqm: float = Form(...),
    rooms: int = Form(...),
):
    service = EstimationService(PerplexityService())
    result = await service.estimate(
        PublicEstimateRequest(city=city, property_type=property_type, area_sqm=area_sqm, rooms=rooms)
    )
    context = {
        "request": request,
        "estimate": result,
        "form_data": {"city": city, "property_type": property_type, "area_sqm": area_sqm, "rooms": rooms},
    }
    return templates.TemplateResponse("estimation/result.html", context)


@router.post("/lead", response_class=HTMLResponse)
async def save_lead(
    request: Request,
    db: Session = Depends(get_db),
    city: str = Form(...),
    property_type: str = Form(...),
    area_sqm: float = Form(...),
    rooms: int = Form(...),
    estimated_low: float = Form(...),
    estimated_mid: float = Form(...),
    estimated_high: float = Form(...),
    name: str = Form(...),
    email: str = Form(...),
    phone: str = Form(...),
    address: str = Form(...),
    urgency: str = Form(...),
    motivation: str = Form(...),
):
    payload = AdvancedLeadCreate(
        city=city,
        property_type=property_type,
        area_sqm=area_sqm,
        rooms=rooms,
        estimated_low=estimated_low,
        estimated_mid=estimated_mid,
        estimated_high=estimated_high,
        name=name,
        email=email,
        phone=phone,
        address=address,
        urgency=urgency,
        motivation=motivation,
    )
    service = EstimationService(PerplexityService())
    temp = service.score_temperature(payload.urgency, payload.motivation)

    lead = Lead(
        city=payload.city,
        property_type=payload.property_type,
        area_sqm=payload.area_sqm,
        rooms=payload.rooms,
        estimated_low=payload.estimated_low,
        estimated_mid=payload.estimated_mid,
        estimated_high=payload.estimated_high,
        name=payload.name,
        email=payload.email,
        phone=payload.phone,
        address=payload.address,
        urgency=payload.urgency,
        motivation=payload.motivation,
        owner_confirmed=payload.owner_confirmed,
        bant_budget=payload.estimated_mid,
        bant_authority="propriétaire" if payload.owner_confirmed else "intermédiaire",
        bant_need=payload.motivation,
        bant_timing=payload.urgency,
        temperature=temp,
    )
    db.add(lead)
    db.commit()

    return templates.TemplateResponse("estimation/lead_saved.html", {"request": request, "lead": lead})
