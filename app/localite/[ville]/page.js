import LocaliteClient from './LocaliteClient';

const LOCALITE_DATA = {
  paris: {
    prixM2: 10890,
    center: [48.8566, 2.3522],
    biens: [
      {
        id: 'par-1',
        titre: 'Appartement lumineux - Marais',
        type: 'Appartement',
        prix: 825000,
        surface: 68,
        pieces: 3,
        image: 'https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1200&q=80',
        lat: 48.8618,
        lng: 2.3622,
      },
      {
        id: 'par-2',
        titre: 'Maison avec terrasse - 16e',
        type: 'Maison',
        prix: 1650000,
        surface: 142,
        pieces: 6,
        image: 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=80',
        lat: 48.8549,
        lng: 2.2742,
      },
      {
        id: 'par-3',
        titre: 'Studio investisseur - Nation',
        type: 'Studio',
        prix: 259000,
        surface: 24,
        pieces: 1,
        image: 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80',
        lat: 48.8483,
        lng: 2.398,
      },
    ],
    faq: [
      {
        q: 'Quel est le prix moyen au m² à Paris ?',
        a: 'Le prix moyen est estimé à 10 890 €/m², avec des écarts selon les arrondissements et la proximité des transports.',
      },
      {
        q: 'Quels quartiers sont les plus recherchés ?',
        a: 'Les secteurs centraux et les zones bien connectées restent les plus demandés, notamment pour les appartements familiaux.',
      },
      {
        q: 'Paris est-elle adaptée à un investissement locatif ?',
        a: 'La demande locative est structurellement forte, ce qui en fait une ville attractive malgré des prix d\'entrée élevés.',
      },
    ],
  },
};

const toTitleCase = (value = '') =>
  value
    .split('-')
    .filter(Boolean)
    .map((part) => part.charAt(0).toUpperCase() + part.slice(1).toLowerCase())
    .join(' ');

export async function generateMetadata({ params }) {
  const villeSlug = params?.ville || 'ville';
  const ville = toTitleCase(villeSlug);

  return {
    title: `Immobilier à ${ville}`,
    description: `Prix, biens disponibles, carte et FAQ pour investir ou acheter à ${ville}.`,
    alternates: {
      canonical: `/localite/${villeSlug}`,
    },
    openGraph: {
      title: `Immobilier à ${ville}`,
      description: `Explorez le marché immobilier de ${ville} : prix au m², carte interactive et annonces filtrables.`,
      url: `/localite/${villeSlug}`,
      type: 'website',
    },
  };
}

export default function LocalitePage({ params }) {
  const villeSlug = params?.ville || 'ville';
  const ville = toTitleCase(villeSlug);
  const cityData = LOCALITE_DATA[villeSlug] || {
    prixM2: 0,
    center: [48.8566, 2.3522],
    biens: [],
    faq: [
      {
        q: `Y a-t-il déjà des données pour ${ville} ?`,
        a: 'Aucune donnée locale n\'est encore disponible. Ajoutez des annonces et statistiques pour enrichir cette page.',
      },
    ],
  };

  const jsonLd = {
    '@context': 'https://schema.org',
    '@type': 'WebPage',
    name: `Immobilier à ${ville}`,
    url: `/localite/${villeSlug}`,
    description: `Données immobilières locales pour ${ville}`,
    mainEntity: {
      '@type': 'FAQPage',
      mainEntity: cityData.faq.map((item) => ({
        '@type': 'Question',
        name: item.q,
        acceptedAnswer: {
          '@type': 'Answer',
          text: item.a,
        },
      })),
    },
  };

  return (
    <>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }}
      />
      <LocaliteClient ville={ville} villeSlug={villeSlug} cityData={cityData} />
    </>
  );
}
