// types/ville.ts

export interface Quartier {
  slug: string
  nom: string
  description: string
  prixMoyenM2: number
  typeBien: "appartement" | "maison" | "mixte"
  ambiance: string
}

export interface MarqueImmobilier {
  prixMoyenM2Appart: number
  prixMoyenM2Maison: number
  prixMin: number
  prixMax: number
  evolutionAnnuelle: number      // en % · ex: 2.5
  tendance: "hausse" | "baisse" | "stable"
  derniereMiseAJour: string      // format ISO · ex: "2024-01-15"
}

export interface SEOVille {
  metaTitle: string              // 60 chars max
  metaDescription: string        // 160 chars max
  h1: string
  canonicalUrl: string
  ogImage: string                // chemin image Open Graph
  keywords: string[]
}

export interface Ville {
  // ─── IDENTITÉ ────────────────────────────────────────────────────────────
  nom: string
  slug: string
  codePostal: string
  codeInsee: string
  departement: string
  numeroDepartement: string
  region: string
  population: number
  superficieKm2: number
  coordonnees: {
    lat: number
    lng: number
  }

  // ─── MARCHÉ ──────────────────────────────────────────────────────────────
  marche: MarqueImmobilier

  // ─── QUARTIERS ───────────────────────────────────────────────────────────
  quartiers: Quartier[]

  // ─── SEO ─────────────────────────────────────────────────────────────────
  seo: SEOVille

  // ─── CONTENU ─────────────────────────────────────────────────────────────
  accroche: string               // phrase intro page ville
  descriptionCourte: string      // 2-3 lignes · cards et previews
  descriptionLongue: string      // contenu complet page ville

  // ─── MÉDIAS ──────────────────────────────────────────────────────────────
  imageHero: string
  imageOg: string

  // ─── CONFIG ──────────────────────────────────────────────────────────────
  active: boolean                // false = ville masquée
  dateAjout: string              // format ISO
}
