'use client';

import { useCallback, useEffect, useMemo, useState } from 'react';
import { usePathname, useRouter } from 'next/navigation';

const BIENS_MOCK = [
  {
    id: 1,
    titre: 'Appartement rénové avec balcon',
    type: 'Appartement',
    ville: 'Lyon',
    codePostal: '69003',
    prix: 289000,
    surface: 67,
    pieces: 3,
    chambres: 2,
    dpe: 'C',
    options: { parking: true, balcon: true, cave: false, ascenseur: true },
    statut: 'À vendre',
    dateAjout: '2026-03-20',
    image: '/img1.jpg',
    lat: 45.7601,
    lng: 4.858,
  },
  {
    id: 2,
    titre: 'Maison familiale avec jardin',
    type: 'Maison',
    ville: 'Lyon',
    codePostal: '69005',
    prix: 598000,
    surface: 145,
    pieces: 6,
    chambres: 4,
    dpe: 'D',
    options: { parking: true, balcon: false, cave: true, ascenseur: false },
    statut: 'À vendre',
    dateAjout: '2026-03-12',
    image: '/img2.jpg',
    lat: 45.7575,
    lng: 4.822,
  },
  {
    id: 3,
    titre: 'Local commercial centre-ville',
    type: 'Local',
    ville: 'Paris',
    codePostal: '75011',
    prix: 410000,
    surface: 78,
    pieces: 2,
    chambres: 0,
    dpe: 'E',
    options: { parking: false, balcon: false, cave: true, ascenseur: false },
    statut: 'Sous compromis',
    dateAjout: '2026-03-15',
    image: '/img3.jpg',
    lat: 48.857,
    lng: 2.382,
  },
  {
    id: 4,
    titre: 'Terrain constructible 950m²',
    type: 'Terrain',
    ville: 'Bordeaux',
    codePostal: '33000',
    prix: 198000,
    surface: 950,
    pieces: 1,
    chambres: 0,
    dpe: 'A',
    options: { parking: false, balcon: false, cave: false, ascenseur: false },
    statut: 'À vendre',
    dateAjout: '2026-02-28',
    image: '/img4.jpg',
    lat: 44.8389,
    lng: -0.5792,
  },
  {
    id: 5,
    titre: 'Appartement standing avec ascenseur',
    type: 'Appartement',
    ville: 'Lille',
    codePostal: '59000',
    prix: 255000,
    surface: 59,
    pieces: 2,
    chambres: 1,
    dpe: 'B',
    options: { parking: true, balcon: false, cave: true, ascenseur: true },
    statut: 'À vendre',
    dateAjout: '2026-03-22',
    image: '/img5.jpg',
    lat: 50.6292,
    lng: 3.0573,
  },
  {
    id: 6,
    titre: 'Maison contemporaine avec garage',
    type: 'Maison',
    ville: 'Nantes',
    codePostal: '44000',
    prix: 472000,
    surface: 132,
    pieces: 5,
    chambres: 3,
    dpe: 'C',
    options: { parking: true, balcon: true, cave: false, ascenseur: false },
    statut: 'Sous compromis',
    dateAjout: '2026-03-03',
    image: '/img6.jpg',
    lat: 47.2184,
    lng: -1.5536,
  },
  {
    id: 7,
    titre: 'Appartement étudiant proche transports',
    type: 'Appartement',
    ville: 'Toulouse',
    codePostal: '31000',
    prix: 119000,
    surface: 24,
    pieces: 1,
    chambres: 1,
    dpe: 'F',
    options: { parking: false, balcon: false, cave: false, ascenseur: true },
    statut: 'À vendre',
    dateAjout: '2026-03-26',
    image: '/img1.jpg',
    lat: 43.6045,
    lng: 1.444,
  },
  {
    id: 8,
    titre: 'Loft atypique en hypercentre',
    type: 'Appartement',
    ville: 'Marseille',
    codePostal: '13006',
    prix: 334000,
    surface: 84,
    pieces: 4,
    chambres: 2,
    dpe: 'D',
    options: { parking: false, balcon: true, cave: true, ascenseur: false },
    statut: 'À vendre',
    dateAjout: '2026-03-09',
    image: '/img2.jpg',
    lat: 43.289,
    lng: 5.379,
  },
  {
    id: 9,
    titre: 'Maison avec piscine',
    type: 'Maison',
    ville: 'Montpellier',
    codePostal: '34000',
    prix: 615000,
    surface: 168,
    pieces: 7,
    chambres: 5,
    dpe: 'B',
    options: { parking: true, balcon: true, cave: true, ascenseur: false },
    statut: 'À vendre',
    dateAjout: '2026-03-17',
    image: '/img3.jpg',
    lat: 43.6108,
    lng: 3.8767,
  },
  {
    id: 10,
    titre: 'Local professionnel refait à neuf',
    type: 'Local',
    ville: 'Lyon',
    codePostal: '69007',
    prix: 355000,
    surface: 102,
    pieces: 4,
    chambres: 0,
    dpe: 'C',
    options: { parking: true, balcon: false, cave: false, ascenseur: true },
    statut: 'À vendre',
    dateAjout: '2026-03-05',
    image: '/img4.jpg',
    lat: 45.744,
    lng: 4.841,
  },
];

const DEFAULT_FILTRES = {
  type: '',
  ville: '',
  prixMin: 50000,
  prixMax: 900000,
  surfaceMin: 10,
  surfaceMax: 1000,
  pieces: '',
  chambres: '',
  dpe: '',
  options: { parking: false, balcon: false, cave: false, ascenseur: false },
  statut: '',
  tri: 'date',
};

function normaliseVille(value = '') {
  return value.trim().toLowerCase();
}

function parseFiltresFromSearchParams(searchParams) {
  const params = new URLSearchParams(searchParams?.toString() ?? '');
  return {
    ...DEFAULT_FILTRES,
    type: params.get('type') ?? '',
    ville: params.get('ville') ?? '',
    prixMin: Number(params.get('prixMin') ?? DEFAULT_FILTRES.prixMin),
    prixMax: Number(params.get('prixMax') ?? DEFAULT_FILTRES.prixMax),
    surfaceMin: Number(params.get('surfaceMin') ?? DEFAULT_FILTRES.surfaceMin),
    surfaceMax: Number(params.get('surfaceMax') ?? DEFAULT_FILTRES.surfaceMax),
    pieces: params.get('pieces') ?? '',
    chambres: params.get('chambres') ?? '',
    dpe: params.get('dpe') ?? '',
    options: {
      parking: params.get('parking') === '1',
      balcon: params.get('balcon') === '1',
      cave: params.get('cave') === '1',
      ascenseur: params.get('ascenseur') === '1',
    },
    statut: params.get('statut') ?? '',
    tri: params.get('tri') ?? 'date',
  };
}

export function useRecherche(searchParams) {
  const router = useRouter();
  const pathname = usePathname();
  const initialFiltres = useMemo(() => parseFiltresFromSearchParams(searchParams), [searchParams]);

  const [filtres, setFiltres] = useState(initialFiltres);
  const [isLoading, setIsLoading] = useState(false);
  const [page, setPage] = useState(1);

  const villesSuggestions = useMemo(() => {
    return Array.from(new Set(BIENS_MOCK.map((bien) => `${bien.ville} (${bien.codePostal})`)));
  }, []);

  const setFiltre = useCallback((cle, valeur) => {
    setPage(1);
    setFiltres((prev) => {
      if (cle.startsWith('options.')) {
        const optionCle = cle.replace('options.', '');
        return { ...prev, options: { ...prev.options, [optionCle]: valeur } };
      }
      return { ...prev, [cle]: valeur };
    });
  }, []);

  const resetFiltres = useCallback(() => {
    setPage(1);
    setFiltres(DEFAULT_FILTRES);
  }, []);

  const resultats = useMemo(() => {
    const villeQuery = normaliseVille(filtres.ville);

    let filtered = BIENS_MOCK.filter((bien) => {
      const typeOk = !filtres.type || bien.type.toLowerCase() === filtres.type.toLowerCase();
      const villeOk =
        !villeQuery ||
        normaliseVille(bien.ville).includes(villeQuery) ||
        bien.codePostal.includes(villeQuery.replace(/\s+/g, ''));
      const prixOk = bien.prix >= filtres.prixMin && bien.prix <= filtres.prixMax;
      const surfaceOk = bien.surface >= filtres.surfaceMin && bien.surface <= filtres.surfaceMax;
      const piecesOk =
        !filtres.pieces || (filtres.pieces === '5+' ? bien.pieces >= 5 : bien.pieces === Number(filtres.pieces));
      const chambresOk = !filtres.chambres || bien.chambres >= Number(filtres.chambres);
      const dpeOk = !filtres.dpe || bien.dpe === filtres.dpe;
      const statutOk = !filtres.statut || bien.statut === filtres.statut;
      const optionsOk = Object.entries(filtres.options).every(([key, enabled]) => !enabled || bien.options[key]);

      return typeOk && villeOk && prixOk && surfaceOk && piecesOk && chambresOk && dpeOk && optionsOk && statutOk;
    });

    filtered = [...filtered].sort((a, b) => {
      if (filtres.tri === 'prix-asc') return a.prix - b.prix;
      if (filtres.tri === 'prix-desc') return b.prix - a.prix;
      if (filtres.tri === 'surface-desc') return b.surface - a.surface;
      return new Date(b.dateAjout).getTime() - new Date(a.dateAjout).getTime();
    });

    return filtered;
  }, [filtres]);

  const perPage = 6;
  const resultatsVisibles = useMemo(() => resultats.slice(0, page * perPage), [resultats, page]);
  const hasMore = resultatsVisibles.length < resultats.length;

  useEffect(() => {
    const timeout = setTimeout(() => setIsLoading(false), 220);
    setIsLoading(true);
    return () => clearTimeout(timeout);
  }, [filtres]);

  useEffect(() => {
    const params = new URLSearchParams();
    const setIfValue = (key, value) => {
      if (value !== '' && value !== null && value !== undefined) params.set(key, String(value));
    };

    setIfValue('type', filtres.type.toLowerCase());
    setIfValue('ville', normaliseVille(filtres.ville));
    if (filtres.prixMin !== DEFAULT_FILTRES.prixMin) setIfValue('prixMin', filtres.prixMin);
    if (filtres.prixMax !== DEFAULT_FILTRES.prixMax) setIfValue('prixMax', filtres.prixMax);
    if (filtres.surfaceMin !== DEFAULT_FILTRES.surfaceMin) setIfValue('surfaceMin', filtres.surfaceMin);
    if (filtres.surfaceMax !== DEFAULT_FILTRES.surfaceMax) setIfValue('surfaceMax', filtres.surfaceMax);
    setIfValue('pieces', filtres.pieces);
    setIfValue('chambres', filtres.chambres);
    setIfValue('dpe', filtres.dpe);
    setIfValue('statut', filtres.statut);
    if (filtres.tri !== 'date') setIfValue('tri', filtres.tri);

    Object.entries(filtres.options).forEach(([key, value]) => {
      if (value) params.set(key, '1');
    });

    const queryString = params.toString();
    router.replace(queryString ? `${pathname}?${queryString}` : pathname, { scroll: false });
  }, [filtres, pathname, router]);

  return {
    filtres,
    resultats,
    resultatsVisibles,
    nombreResultats: resultats.length,
    isLoading,
    villesSuggestions,
    hasMore,
    chargerPlus: () => setPage((p) => p + 1),
    setFiltre,
    resetFiltres,
  };
}
