# Plan de nettoyage repo (production / white-label)

## A. Fichiers à retirer immédiatement

- `routing.php` (legacy, non utilisé, ne doit pas être exposé en HTTP)
- `front/error_log` (artefact runtime)
- `diagnostic-*.php` (diagnostics ad hoc)
- vrais fichiers de secrets/config runtime (`.env`, `.env.local`, overrides locaux)
- dumps/archives temporaires (`*.sql.gz`, `*.dump`, `*.zip`, `*.tar.gz`)

## B. Politique `.gitignore` recommandée

Principes:
- versionner uniquement les templates (`.env.example`), jamais les secrets réels.
- ignorer tous les artefacts runtime (logs, dumps, backup, fichiers éditeur).
- ignorer explicitement les scripts diagnostics exposables en HTTP.

## C. Commandes Git recommandées

### Retirer du suivi (sans supprimer localement)
```bash
git rm --cached routing.php 2>/dev/null || true
git rm --cached front/error_log 2>/dev/null || true
git rm --cached diagnostic-*.php 2>/dev/null || true
git rm --cached .env .env.local config/config.local.php 2>/dev/null || true
```

### Supprimer réellement du repo (si inutile)
```bash
git rm -f routing.php front/error_log diagnostic-*.php 2>/dev/null || true
```

### Vérifier l'index
```bash
git status --short
git ls-files | rg -n '(^|/)(routing\.php|error_log|diagnostic-.*\.php|\.env(\.|$)|\.log$|\.dump$|\.sql\.gz$)'
```

### Historique (si secrets déjà commités)
```bash
# BFG ou git filter-repo, puis rotation obligatoire des secrets
git filter-repo --path .env --path-glob 'diagnostic-*.php' --invert-paths
```

## D. Risques de bord

- suppression de `routing.php` peut casser un vhost legacy qui pointe encore dessus.
- scripts diagnostics supprimés: plus de debug web rapide (préférer CLI + IP allowlist temporaire).
- réécriture historique: nécessite `push --force` et resynchronisation de toutes les branches locales.

## E. Vérifications post-nettoyage

1. `git ls-files` ne retourne plus de secrets/diagnostics/logs.
2. en prod, URLs `routing.php`, `diagnostic-*.php`, `front/error_log` répondent 403/404.
3. rotation des secrets déjà exposés (DB, SMTP, API keys).
4. CI/CD OK après nettoyage.
5. duplication white-label testée avec un `.env` neuf uniquement.
