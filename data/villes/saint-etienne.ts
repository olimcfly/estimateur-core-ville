// data/villes/saint-etienne.ts

import { Ville } from "@/types/ville"

export const saintEtienne: Ville = {
  // ─── IDENTITÉ ──────────────────────────────────────────────────────────────
  nom: "Saint-Étienne",
  slug: "saint-etienne",
  codePostal: "42000",
  codeInsee: "42218",
  departement: "Loire",
  numeroDepartement: "42",
  region: "Auvergne-Rhône-Alpes",
  population: 174000,
  superficieKm2: 79.97,
  coordonnees: {
    lat: 45.4397,
    lng: 4.3872,
  },

  // ─── MARCHÉ ────────────────────────────────────────────────────────────────
  marche: {
    prixMoyenM2Appart: 1450,
    prixMoyenM2Maison: 1800,
    prixMin: 800,
    prixMax: 3200,
    evolutionAnnuelle: 1.2,
    tendance: "stable",
    derniereMiseAJour: "2024-01-15",
  },

  // ─── QUARTIERS ─────────────────────────────────────────────────────────────
  quartiers: [
    {
      slug: "centre-ville",
      nom: "Centre-Ville",
      description:
        "Cœur historique de Saint-Étienne, idéal pour les investisseurs cherchant des biens à rénover avec un fort potentiel locatif.",
      prixMoyenM2: 1350,
      typeBien: "appartement",
      ambiance: "urbain, commerçant, animé",
    },
    {
      slug: "bellevue",
      nom: "Bellevue",
      description:
        "Quartier résidentiel prisé, calme et verdoyant, très recherché par les familles.",
      prixMoyenM2: 2100,
      typeBien: "mixte",
      ambiance: "résidentiel, familial, verdoyant",
    },
    {
      slug: "tarentaize",
      nom: "Tarentaize",
      description:
        "Quartier populaire en pleine mutation, idéal pour un premier investissement à petit budget.",
      prixMoyenM2: 900,
      typeBien: "appartement",
      ambiance: "populaire, en transition, abordable",
    },
    {
      slug: "montplaisir",
      nom: "Montplaisir",
      description:
        "Secteur bourgeois avec de belles maisons individuelles, très stable sur le marché.",
      prixMoyenM2: 2400,
      typeBien: "maison",
      ambiance: "bourgeois, calme, prestige",
    },
    {
      slug: "carnot-jacquard",
      nom: "Carnot - Jacquard",
      description:
        "Proche des universités et grandes écoles, fort potentiel locatif étudiant.",
      prixMoyenM2: 1200,
      typeBien: "appartement",
      ambiance: "étudiant, dynamique, central",
    },
  ],

  // ─── SEO ───────────────────────────────────────────────────────────────────
  seo: {
    metaTitle: "Immobilier Saint-Étienne · Prix, Quartiers & Investissement",
    metaDescription:
      "Tout savoir sur l'immobilier à Saint-Étienne : prix au m², meilleurs quartiers, conseils d'investissement. Données actualisées 2024.",
    h1: "Immobilier à Saint-Étienne : guide complet 2024",
    canonicalUrl: "https://votresite.fr/villes/saint-etienne",
    ogImage: "/images/villes/saint-etienne/og.jpg",
    keywords: [
      "immobilier saint-etienne",
      "prix immobilier saint-etienne",
      "investissement immobilier saint-etienne",
      "acheter appartement saint-etienne",
      "prix m2 saint-etienne",
      "quartier saint-etienne immobilier",
    ],
  },

  // ─── CONTENU ───────────────────────────────────────────────────────────────
  accroche:
    "Saint-Étienne, ville aux prix accessibles et au fort potentiel locatif, attire de plus en plus d'investisseurs.",

  descriptionCourte:
    "Avec des prix parmi les plus bas de France et une demande locative soutenue, Saint-Étienne offre des opportunités d'investissement immobilier exceptionnelles. Idéale pour les primo-accédants et les investisseurs à la recherche de rentabilité.",

  descriptionLongue: `
    Saint-Étienne est une ville en pleine transformation. Ancienne capitale industrielle 
    du textile et de l'armurerie, elle s'est réinventée autour du design, du numérique 
    et de l'enseignement supérieur.

    Avec une population de 174 000 habitants et un bassin de vie de plus de 400 000 personnes, 
    Saint-Étienne dispose d'un marché immobilier accessible avec des prix moyens autour de 
    1 450 €/m² pour les appartements — soit 3 à 4 fois moins cher que Lyon, distante 
    seulement de 60 km.

    Les investisseurs y trouvent des rendements locatifs bruts pouvant atteindre 8 à 10% 
    dans certains quartiers, notamment grâce à la forte population étudiante (25 000 étudiants) 
    et aux nombreux programmes de rénovation urbaine en cours.

    Les quartiers les plus recherchés sont Bellevue et Montplaisir pour les familles, 
    tandis que Carnot-Jacquard et le Centre-Ville séduisent les investisseurs locatifs.
  `,

  // ─── MÉDIAS ────────────────────────────────────────────────────────────────
  imageHero: "/images/villes/saint-etienne/hero.jpg",
  imageOg: "/images/villes/saint-etienne/og.jpg",

  // ─── CONFIG ────────────────────────────────────────────────────────────────
  active: true,
  dateAjout: "2024-01-15",
}
