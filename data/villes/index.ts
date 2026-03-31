// data/villes/index.ts

import { Ville } from "@/types/ville"
import { saintEtienne } from "./saint-etienne"

// ─── REGISTRE CENTRAL ────────────────────────────────────────────────────────
// Ajouter chaque nouvelle ville ici

export const villes: Ville[] = [
  saintEtienne,
  // lyon,
  // grenoble,
  // clermont,
]

// ─── HELPERS ─────────────────────────────────────────────────────────────────

/** Récupère une ville par son slug */
export function getVilleBySlug(slug: string): Ville | undefined {
  return villes.find((v) => v.slug === slug)
}

/** Récupère toutes les villes actives */
export function getVillesActives(): Ville[] {
  return villes.filter((v) => v.active)
}

/** Récupère tous les slugs · utilisé par getStaticPaths */
export function getAllVillesSlugs(): string[] {
  return getVillesActives().map((v) => v.slug)
}

/** Récupère une ville + ses quartiers */
export function getQuartierBySlug(villeSlug: string, quartierSlug: string) {
  const ville = getVilleBySlug(villeSlug)
  if (!ville) return undefined
  const quartier = ville.quartiers.find((q) => q.slug === quartierSlug)
  return { ville, quartier }
}
