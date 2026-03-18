from fastapi import APIRouter, Depends, Form, HTTPException, Query, Request
from fastapi.responses import HTMLResponse, RedirectResponse
from fastapi.templating import Jinja2Templates
from sqlalchemy.orm import Session

from app.core.config import settings
from app.core.database import get_db
from app.models.entities import Lead

router = APIRouter(prefix="/admin", tags=["admin"])
templates = Jinja2Templates(directory="app/templates")


def protect(token: str | None):
    if token != settings.admin_token:
        raise HTTPException(status_code=401, detail="Token admin invalide")


@router.get("/leads", response_class=HTMLResponse)
def list_leads(
    request: Request,
    token: str = Query(...),
    temperature: str | None = Query(default=None),
    db: Session = Depends(get_db),
):
    protect(token)
    query = db.query(Lead).order_by(Lead.created_at.desc())
    if temperature:
        query = query.filter(Lead.temperature == temperature)
    leads = query.all()
    return templates.TemplateResponse(
        "admin/leads.html",
        {"request": request, "leads": leads, "temperature": temperature, "token": token},
    )


@router.get("/leads/{lead_id}", response_class=HTMLResponse)
def lead_detail(lead_id: int, request: Request, token: str = Query(...), db: Session = Depends(get_db)):
    protect(token)
    lead = db.query(Lead).filter(Lead.id == lead_id).first()
    if not lead:
        raise HTTPException(status_code=404, detail="Lead introuvable")
    return templates.TemplateResponse("admin/lead_detail.html", {"request": request, "lead": lead, "token": token})


@router.post("/leads/{lead_id}/update")
def update_lead(
    lead_id: int,
    token: str = Query(...),
    status: str = Form(...),
    internal_notes: str = Form(""),
    db: Session = Depends(get_db),
):
    protect(token)
    lead = db.query(Lead).filter(Lead.id == lead_id).first()
    if not lead:
        raise HTTPException(status_code=404, detail="Lead introuvable")
    lead.status = status
    lead.internal_notes = internal_notes
    db.add(lead)
    db.commit()
    return RedirectResponse(url=f"/admin/leads/{lead_id}?token={token}", status_code=303)
