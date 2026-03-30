import BienDetailClient from './BienDetailClient';

const MOCK_BIENS = [
  {
    id: '1',
    titre: 'Appartement 3 pièces lumineux',
    type: 'Appartement',
    ville: 'Lyon',
    prix: 285000,
    surface: 68,
    pieces: 3,
    chambres: 2,
    etage: 3,
    dpe: 'C',
    statut: 'À vendre',
    description:
      "Bel appartement traversant et lumineux, idéalement situé à proximité des commerces, transports et écoles. Il dispose d'un séjour spacieux, d'une cuisine équipée, de deux chambres confortables et d'un balcon agréable.",
    photos: ['/img1.jpg', '/img2.jpg', '/img3.jpg', '/img4.jpg'],
    lat: 45.75,
    lng: 4.85,
    pointsForts: ['Parking', 'Balcon', 'Cave', 'Ascenseur'],
  },
  {
    id: '2',
    titre: 'Maison familiale avec jardin',
    type: 'Maison',
    ville: 'Lyon',
    prix: 465000,
    surface: 122,
    pieces: 5,
    chambres: 4,
    etage: 1,
    dpe: 'D',
    statut: 'Sous compromis',
    description:
      'Maison familiale rénovée offrant de beaux volumes, un jardin arboré et un garage. Quartier résidentiel calme.',
    photos: ['/img2.jpg', '/img3.jpg'],
    lat: 45.756,
    lng: 4.87,
    pointsForts: ['Jardin', 'Garage', 'Terrasse'],
  },
  {
    id: '3',
    titre: 'Studio idéal investissement',
    type: 'Studio',
    ville: 'Lyon',
    prix: 149000,
    surface: 24,
    pieces: 1,
    chambres: 1,
    etage: 4,
    dpe: 'E',
    statut: 'Vendu',
    description: 'Studio vendu meublé, proche universités et transports. Très bonne rentabilité locative.',
    photos: ['/img3.jpg', '/img4.jpg'],
    lat: 45.748,
    lng: 4.842,
    pointsForts: ['Meublé', 'Métro proche'],
  },
  {
    id: '4',
    titre: 'Appartement standing avec terrasse',
    type: 'Appartement',
    ville: 'Lyon',
    prix: 535000,
    surface: 97,
    pieces: 4,
    chambres: 3,
    etage: 5,
    dpe: 'B',
    statut: 'À vendre',
    description:
      'Appartement haut de gamme, terrasse plein sud et prestations premium. Résidence récente avec ascenseur.',
    photos: ['/img1.jpg', '/img4.jpg'],
    lat: 45.761,
    lng: 4.853,
    pointsForts: ['Terrasse', 'Vue dégagée', 'Parking'],
  },
];

function slugify(value) {
  return value
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/(^-|-$)+/g, '');
}

function parseIdFromParam(idParam) {
  return String(idParam).split('-')[0];
}

function getBienByParam(idParam) {
  const plainId = parseIdFromParam(idParam);
  return MOCK_BIENS.find((item) => item.id === plainId) ?? MOCK_BIENS[0];
}

function getCanonicalUrl(bien) {
  return `/bien/${bien.id}-${slugify(`${bien.type} ${bien.ville} ${bien.titre}`)}`;
}

export async function generateMetadata({ params }) {
  const bien = getBienByParam(params.id);

  return {
    title: `${bien.type} à vendre ${bien.ville} - ${bien.surface}m² | ${bien.prix}€`,
    description: `${bien.titre} à ${bien.ville}, ${bien.surface}m², ${bien.pieces} pièces, ${bien.prix}€.`,
    alternates: {
      canonical: getCanonicalUrl(bien),
    },
    openGraph: {
      title: `${bien.type} à vendre ${bien.ville} - ${bien.surface}m² | ${bien.prix}€`,
      description: bien.description,
      images: bien.photos?.length ? [bien.photos[0]] : [],
      url: getCanonicalUrl(bien),
      type: 'website',
    },
  };
}

export default function BienPage({ params }) {
  const bien = getBienByParam(params.id);

  const jsonLd = {
    '@context': 'https://schema.org',
    '@type': 'RealEstateListing',
    name: bien.titre,
    description: bien.description,
    url: getCanonicalUrl(bien),
    datePosted: '2026-03-30',
    offers: {
      '@type': 'Offer',
      priceCurrency: 'EUR',
      price: bien.prix,
      availability: bien.statut === 'Vendu' ? 'https://schema.org/SoldOut' : 'https://schema.org/InStock',
    },
    itemOffered: {
      '@type': 'Apartment',
      name: `${bien.type} à ${bien.ville}`,
      floorSize: {
        '@type': 'QuantitativeValue',
        value: bien.surface,
        unitCode: 'MTK',
      },
      numberOfRooms: bien.pieces,
      address: {
        '@type': 'PostalAddress',
        addressLocality: bien.ville,
        addressCountry: 'FR',
      },
      geo: {
        '@type': 'GeoCoordinates',
        latitude: bien.lat,
        longitude: bien.lng,
      },
    },
  };

  const similar = MOCK_BIENS.filter((item) => item.id !== bien.id).slice(0, 3);

  return (
    <>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }}
      />
      <BienDetailClient bien={bien} similarBiens={similar} canonicalUrl={getCanonicalUrl(bien)} />
    </>
  );
}
