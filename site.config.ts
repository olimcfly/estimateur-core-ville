// site.config.ts
// ============================================================
// CONFIGURATION CENTRALE — 1 fichier = 1 ville = 1 site
// Toutes les pages/composants lisent ICI, jamais en dur
// ============================================================

import { z } from "zod";

// ============================================================
// 1) SCHÉMA DE VALIDATION (zod)
// ============================================================

const SiteConfigSchema = z.object({
  // — Identité
  siteId: z.string().min(1),
  citySlug: z.string().min(1),
  cityName: z.string().min(1),
  department: z.string().min(2).max(3),
  region: z.string().min(1),

  // — Branding
  brandName: z.string().min(1),
  brandTagline: z.string(),
  logo: z.object({
    src: z.string(),
    alt: z.string(),
    width: z.number(),
    height: z.number(),
  }),
  palette: z.object({
    primary: z.string().regex(/^#[0-9a-fA-F]{6}$/),
    secondary: z.string().regex(/^#[0-9a-fA-F]{6}$/),
    accent: z.string().regex(/^#[0-9a-fA-F]{6}$/),
    background: z.string().regex(/^#[0-9a-fA-F]{6}$/),
    text: z.string().regex(/^#[0-9a-fA-F]{6}$/),
  }),

  // — Domaine & URLs
  domain: z.string().url(),
  baseUrl: z.string().url(),

  // — SEO globaux
  seo: z.object({
    titleTemplate: z.string(), // ex: "%s | Estimation Bordeaux"
    defaultTitle: z.string(),
    defaultDescription: z.string(),
    defaultKeywords: z.array(z.string()),
    ogImage: z.string(),
    twitterHandle: z.string().optional(),
    googleVerification: z.string().optional(),
  }),

  // — Données marché immobilier
  market: z.object({
    prixMoyen: z.number(),          // €/m²
    prixAppartement: z.number(),
    prixMaison: z.number(),
    tendance: z.enum(["hausse", "stable", "baisse"]),
    tendancePourcentage: z.number(), // ex: 2.3 pour +2.3%
    derniereMiseAJour: z.string(),   // ISO date
  }),

  // — Contact & local
  contact: z.object({
    telephone: z.string().optional(),
    email: z.string().email().optional(),
    adresse: z.string().optional(),
    horaires: z.string().optional(),
  }),

  // — Feature flags
  features: z.object({
    blogActif: z.boolean(),
    alertesActives: z.boolean(),
    agentsActifs: z.boolean(),
    carteActive: z.boolean(),
    avisActifs: z.boolean(),
    comparateurActif: z.boolean(),
  }),

  // — Analytics
  analytics: z.object({
    googleTagId: z.string().optional(),
    hotjarId: z.string().optional(),
    facebookPixelId: z.string().optional(),
  }),
});

// Type exporté
export type SiteConfig = z.infer<typeof SiteConfigSchema>;

// ============================================================
// 2) LECTURE DES VARIABLES D'ENVIRONNEMENT
// ============================================================

function loadConfig(): SiteConfig {
  const raw = {
    // — Identité
    siteId:     process.env.NEXT_PUBLIC_SITE_ID      ?? "bordeaux",
    citySlug:   process.env.NEXT_PUBLIC_CITY_SLUG    ?? "bordeaux",
    cityName:   process.env.NEXT_PUBLIC_CITY_NAME    ?? "Bordeaux",
    department: process.env.NEXT_PUBLIC_DEPARTMENT   ?? "33",
    region:     process.env.NEXT_PUBLIC_REGION       ?? "Nouvelle-Aquitaine",

    // — Branding
    brandName:    process.env.NEXT_PUBLIC_BRAND_NAME    ?? "Estimation Bordeaux",
    brandTagline: process.env.NEXT_PUBLIC_BRAND_TAGLINE ?? "Estimez votre bien en 2 minutes",
    logo: {
      src:    process.env.NEXT_PUBLIC_LOGO_SRC    ?? "/logo.svg",
      alt:    process.env.NEXT_PUBLIC_LOGO_ALT    ?? "Estimation Bordeaux",
      width:  Number(process.env.NEXT_PUBLIC_LOGO_WIDTH  ?? 180),
      height: Number(process.env.NEXT_PUBLIC_LOGO_HEIGHT ?? 40),
    },
    palette: {
      primary:    process.env.NEXT_PUBLIC_COLOR_PRIMARY    ?? "#1a56db",
      secondary:  process.env.NEXT_PUBLIC_COLOR_SECONDARY  ?? "#1e429f",
      accent:     process.env.NEXT_PUBLIC_COLOR_ACCENT     ?? "#ff6b35",
      background: process.env.NEXT_PUBLIC_COLOR_BG         ?? "#ffffff",
      text:       process.env.NEXT_PUBLIC_COLOR_TEXT       ?? "#111827",
    },

    // — Domaine
    domain:  process.env.NEXT_PUBLIC_DOMAIN   ?? "https://estimation-bordeaux.fr",
    baseUrl: process.env.NEXT_PUBLIC_BASE_URL ?? "https://estimation-bordeaux.fr",

    // — SEO
    seo: {
      titleTemplate:      process.env.NEXT_PUBLIC_SEO_TITLE_TEMPLATE  ?? `%s | Estimation Bordeaux`,
      defaultTitle:       process.env.NEXT_PUBLIC_SEO_DEFAULT_TITLE   ?? "Estimation immobilière gratuite à Bordeaux",
      defaultDescription: process.env.NEXT_PUBLIC_SEO_DEFAULT_DESC    ?? "Obtenez une estimation gratuite et fiable de votre bien immobilier à Bordeaux en moins de 2 minutes.",
      defaultKeywords:    (process.env.NEXT_PUBLIC_SEO_KEYWORDS ?? "estimation immobilier bordeaux,prix immobilier bordeaux,vendre appartement bordeaux").split(","),
      ogImage:            process.env.NEXT_PUBLIC_OG_IMAGE             ?? "/og-image.jpg",
      twitterHandle:      process.env.NEXT_PUBLIC_TWITTER_HANDLE,
      googleVerification: process.env.NEXT_PUBLIC_GOOGLE_VERIFICATION,
    },

    // — Marché
    market: {
      prixMoyen:           Number(process.env.NEXT_PUBLIC_PRIX_MOYEN          ?? 4200),
      prixAppartement:     Number(process.env.NEXT_PUBLIC_PRIX_APPARTEMENT    ?? 4500),
      prixMaison:          Number(process.env.NEXT_PUBLIC_PRIX_MAISON         ?? 3900),
      tendance:            (process.env.NEXT_PUBLIC_TENDANCE ?? "stable") as "hausse" | "stable" | "baisse",
      tendancePourcentage: Number(process.env.NEXT_PUBLIC_TENDANCE_PCT        ?? 1.2),
      derniereMiseAJour:   process.env.NEXT_PUBLIC_MARCHE_MAJ                ?? "2024-01-01",
    },

    // — Contact
    contact: {
      telephone: process.env.NEXT_PUBLIC_TELEPHONE,
      email:     process.env.NEXT_PUBLIC_EMAIL,
      adresse:   process.env.NEXT_PUBLIC_ADRESSE,
      horaires:  process.env.NEXT_PUBLIC_HORAIRES,
    },

    // — Features
    features: {
      blogActif:        process.env.NEXT_PUBLIC_FEATURE_BLOG        === "true",
      alertesActives:   process.env.NEXT_PUBLIC_FEATURE_ALERTES     === "true",
      agentsActifs:     process.env.NEXT_PUBLIC_FEATURE_AGENTS      === "true",
      carteActive:      process.env.NEXT_PUBLIC_FEATURE_CARTE       === "true",
      avisActifs:       process.env.NEXT_PUBLIC_FEATURE_AVIS        === "true",
      comparateurActif: process.env.NEXT_PUBLIC_FEATURE_COMPARATEUR === "true",
    },

    // — Analytics
    analytics: {
      googleTagId:      process.env.NEXT_PUBLIC_GTM_ID,
      hotjarId:         process.env.NEXT_PUBLIC_HOTJAR_ID,
      facebookPixelId:  process.env.NEXT_PUBLIC_FB_PIXEL_ID,
    },
  };

  // Validation stricte au démarrage
  const result = SiteConfigSchema.safeParse(raw);

  if (!result.success) {
    console.error("❌ ERREUR site.config.ts — Configuration invalide :");
    console.error(result.error.flatten());
    throw new Error(
      "⛔ Déploiement bloqué : configuration site invalide. Vérifie tes variables d'environnement."
    );
  }

  return result.data;
}

// ============================================================
// 3) EXPORT SINGLETON
// ============================================================

export const siteConfig = loadConfig();

// ============================================================
// 4) HELPERS UTILITAIRES
// ============================================================

/** Retourne le titre formaté pour une page */
export function getSiteTitle(pageTitle?: string): string {
  if (!pageTitle) return siteConfig.seo.defaultTitle;
  return siteConfig.seo.titleTemplate.replace("%s", pageTitle);
}

/** Retourne le prix moyen formaté */
export function getPrixFormate(type?: "appartement" | "maison"): string {
  const prix =
    type === "appartement" ? siteConfig.market.prixAppartement
    : type === "maison"    ? siteConfig.market.prixMaison
    :                        siteConfig.market.prixMoyen;
  return new Intl.NumberFormat("fr-FR").format(prix) + " €/m²";
}

/** Retourne l'URL absolue d'un chemin */
export function getAbsoluteUrl(path: string): string {
  return `${siteConfig.baseUrl}${path.startsWith("/") ? path : "/" + path}`;
}

/** Vérifie si une feature est active */
export function isFeatureActive(feature: keyof SiteConfig["features"]): boolean {
  return siteConfig.features[feature];
}
