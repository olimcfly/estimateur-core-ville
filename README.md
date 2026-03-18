# Estimateur Immobilier Intelligent (FastAPI + HTMX)

Application SaaS immobilière modulaire avec :

- Estimateur gratuit (sans inscription)
- Estimation avancée et qualification BANT
- CRM admin (leads, filtres, statut, notes)
- Blog automatique IA (Perplexity + OpenAI)

## Lancer le projet

```bash
python -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
cp .env.example .env
uvicorn app.main:app --reload
```

## Endpoints principaux

- `/` : formulaire d'estimation gratuite
- `/estimate` : calcul HTMX d'estimation
- `/lead` : création lead qualifié
- `/admin/leads?token=...` : CRM admin
- `/blog` : liste des articles publiés
- `/blog/{slug}` : détail article
- `/admin/blog?token=...` : gestion blog + génération IA

## Architecture

```text
app/
  core/            # config + db
  models/          # SQLAlchemy entities
  routes/          # endpoints web
  schemas/         # validation Pydantic
  services/        # estimation, Perplexity, OpenAI, blog
  templates/       # Jinja2 + HTMX views
  static/          # CSS
```

## Scalabilité

- Config pilotée par variables d'environnement
- SQLAlchemy compatible PostgreSQL (changer `DATABASE_URL`)
- Services AI isolés pour faciliter retries, queue, caching
- Routes séparées pour migrer vers API REST stricte
