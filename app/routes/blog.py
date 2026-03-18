from fastapi import APIRouter, Depends, Form, HTTPException, Query, Request
from fastapi.responses import HTMLResponse, RedirectResponse
from fastapi.templating import Jinja2Templates
from sqlalchemy.orm import Session

from app.core.config import settings
from app.core.database import get_db
from app.models.entities import BlogArticle
from app.schemas.blog import ArticleCreate, ArticleGenerationInput
from app.services.ai_service import AIService
from app.services.blog_service import BlogService
from app.services.perplexity_service import PerplexityService

router = APIRouter(tags=["blog"])
templates = Jinja2Templates(directory="app/templates")


def protect(token: str | None):
    if token != settings.admin_token:
        raise HTTPException(status_code=401, detail="Token admin invalide")


@router.get("/blog", response_class=HTMLResponse)
def blog_index(request: Request, db: Session = Depends(get_db)):
    articles = db.query(BlogArticle).filter(BlogArticle.is_published.is_(True)).order_by(BlogArticle.created_at.desc()).all()
    return templates.TemplateResponse("blog/index.html", {"request": request, "articles": articles})


@router.get("/blog/{slug}", response_class=HTMLResponse)
def blog_detail(slug: str, request: Request, db: Session = Depends(get_db)):
    article = db.query(BlogArticle).filter(BlogArticle.slug == slug).first()
    if not article:
        raise HTTPException(status_code=404, detail="Article introuvable")
    return templates.TemplateResponse("blog/detail.html", {"request": request, "article": article})


@router.get("/admin/blog", response_class=HTMLResponse)
def blog_admin(request: Request, token: str = Query(...), db: Session = Depends(get_db)):
    protect(token)
    articles = db.query(BlogArticle).order_by(BlogArticle.created_at.desc()).all()
    return templates.TemplateResponse("admin/blog_admin.html", {"request": request, "articles": articles, "token": token})


@router.post("/admin/blog/create")
def blog_create(
    token: str = Query(...),
    title: str = Form(...),
    intro: str = Form(...),
    body_markdown: str = Form(...),
    conclusion: str = Form(...),
    faq: str = Form(...),
    db: Session = Depends(get_db),
):
    protect(token)
    service = BlogService(AIService(), PerplexityService())
    service.create_article(
        db,
        ArticleCreate(
            title=title,
            intro=intro,
            body_markdown=body_markdown,
            conclusion=conclusion,
            faq=faq,
            is_published=True,
        ),
    )
    return RedirectResponse(url=f"/admin/blog?token={token}", status_code=303)


@router.post("/admin/blog/generate")
async def blog_generate(
    token: str = Query(...),
    city: str = Form(...),
    topic_hint: str = Form(...),
    db: Session = Depends(get_db),
):
    protect(token)
    service = BlogService(AIService(), PerplexityService())
    await service.generate_article(db, ArticleGenerationInput(city=city, topic_hint=topic_hint))
    return RedirectResponse(url=f"/admin/blog?token={token}", status_code=303)


@router.post("/admin/blog/{article_id}/delete")
def blog_delete(article_id: int, token: str = Query(...), db: Session = Depends(get_db)):
    protect(token)
    article = db.query(BlogArticle).filter(BlogArticle.id == article_id).first()
    if article:
        db.delete(article)
        db.commit()
    return RedirectResponse(url=f"/admin/blog?token={token}", status_code=303)
