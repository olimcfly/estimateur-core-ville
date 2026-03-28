# Nettoyage des connexions base de données (backlog)

## Source d'autorité

- **Unique source recommandée**: `App\Core\Database`.
- Méthode standard runtime: `Database::connection()`.
- Méthode standard pour connexion admin explicite (autres credentials): `Database::connectWithCredentials(...)`.

## État actuel

- Le cœur applicatif (models/controllers/services) utilise majoritairement `Database::connection()`.
- Un ancien exemple de doc mentionnait encore `config/database.php` (fichier absent dans ce repo).
- `AdminDatabaseController` avait des instanciations `new PDO(...)` en direct (désormais migrées vers `Database::connectWithCredentials(...)`).

## Fichiers à surveiller ensuite

1. `docs/image-generator-examples.md`  
   - Remplacer les snippets legacy `require_once '../config/database.php';`
   - Basculer vers bootstrap + `Database::connection()`.

2. `app/controllers/AdminDatabaseController.php`  
   - À terme, extraire la logique de session/connexion admin dans un service dédié (`AdminDbSessionConnection`).

3. `app/core/Database.php`  
   - Si besoin futur multi-tenant strict, introduire un provider de credentials (tenant-aware) au lieu de lire directement `Config`.

## Règle d'équipe

- Interdit d'ajouter un `new PDO(...)` dans models/controllers/services hors `App\Core\Database`.
- Toute nouvelle connexion explicite doit passer par `Database::connectWithCredentials(...)`.
