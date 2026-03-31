# Audit pré-production des bases de données (Licence 1)

Date: 2026-03-31

## Résumé exécutif

- Répartition fonctionnelle globalement cohérente (core vs estimations vs environnements), mais l'isolation **Prod / Test / Demo** n'est pas stricte dans la configuration applicative actuelle.
- La configuration applicative ne définissait qu'une seule connexion DB, ce qui augmentait le risque d'erreur de routage de données.
- Deux migrations fondamentales (`001_create_villes.sql`, `002_create_leads.sql`) sont actuellement vides, ce qui bloque une initialisation fiable du schéma.

## Constats bloquants

1. Migrations de base vides (`001` et `002`).
2. Dépendances SQL vers tables non garanties (`users`, `biens`, `photos_biens`, `leads`) sans ordre d'exécution complet.
3. Vue `v_articles_publies` référence des noms de colonnes/tables incohérents (`publie_le`, `categories_articles`, `commentaires_articles`) par rapport aux tables créées dans les migrations blog.

## Architecture cible recommandée

- **DB 1: saas_main (prod)**: identité, abonnements, facturation, RBAC, audit sécurité.
- **DB 2: saas_estimations (prod)**: estimations, rapports, scoring, logs IA, événements techniques.
- **DB 3-8**: strictement non-prod (test/demo), avec comptes DB distincts et permissions minimales.
- **Cross-licences**: via API signée + file d'événements, jamais via accès DB direct inter-licences.

## Plan de création des tables (ordre)

1. `users`, `roles`, `permissions`, `user_roles`
2. `plans`, `subscriptions`, `invoices`, `payments`
3. `biens`, `photos_biens`
4. `estimations`, `estimation_reports`, `ai_logs`
5. `favoris`, `alertes`, `messages`, `conversations`, `contacts_agents`
6. `leads`, `lead_events`, `webhooks_outbox`
7. tables blog/SEO + vues
8. `audit_logs`, `security_events`, `jobs`, `failed_jobs`

## Variables d'environnement minimales

- Une variable de connexion par base logique.
- Séparer secrets prod/test/demo.
- Préfixer clairement par environnement (`PROD_`, `TEST_`, `DEMO_`) ou par domaine (`SAAS_MAIN_`, `IMMO_TEST_`, etc.).

