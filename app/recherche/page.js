'use client';

import { useEffect, useMemo, useState } from 'react';
import dynamic from 'next/dynamic';
import 'leaflet/dist/leaflet.css';
import { useSearchParams } from 'next/navigation';
import FiltresRecherche from '../../components/FiltresRecherche';
import { useRecherche } from '../../hooks/useRecherche';

const MapContainer = dynamic(() => import('react-leaflet').then((mod) => mod.MapContainer), { ssr: false });
const TileLayer = dynamic(() => import('react-leaflet').then((mod) => mod.TileLayer), { ssr: false });
const Marker = dynamic(() => import('react-leaflet').then((mod) => mod.Marker), { ssr: false });
const Popup = dynamic(() => import('react-leaflet').then((mod) => mod.Popup), { ssr: false });

const SORT_OPTIONS = [
  { value: 'prix-asc', label: 'Prix croissant' },
  { value: 'prix-desc', label: 'Prix décroissant' },
  { value: 'surface-desc', label: 'Surface' },
  { value: 'date', label: 'Date' },
];

const formatPrice = (value) => `${new Intl.NumberFormat('fr-FR').format(value)} €`;

function BienCard({ bien, view }) {
  return (
    <article
      className={`overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm ${
        view === 'list' ? 'flex flex-col sm:flex-row' : ''
      }`}
    >
      <img
        src={bien.image}
        alt={bien.titre}
        className={view === 'list' ? 'h-40 w-full object-cover sm:h-auto sm:w-52' : 'h-44 w-full object-cover'}
      />
      <div className="flex-1 space-y-2 p-4">
        <p className="text-xs font-semibold uppercase tracking-wide text-indigo-600">{bien.type}</p>
        <h3 className="text-base font-semibold text-slate-900">{bien.titre}</h3>
        <p className="text-sm text-slate-600">
          {bien.ville} ({bien.codePostal}) • {bien.surface} m² • {bien.pieces} pièces • {bien.chambres} ch.
        </p>
        <p className="text-lg font-bold text-slate-900">{formatPrice(bien.prix)}</p>
        <div className="flex flex-wrap gap-2">
          <span className="rounded-full bg-slate-100 px-2 py-1 text-xs text-slate-700">DPE {bien.dpe}</span>
          <span className="rounded-full bg-amber-50 px-2 py-1 text-xs text-amber-700">{bien.statut}</span>
        </div>
      </div>
    </article>
  );
}

function SkeletonCard() {
  return <div className="h-60 animate-pulse rounded-2xl bg-slate-200" />;
}

export default function RecherchePage() {
  const searchParams = useSearchParams();
  const { filtres, setFiltre, resetFiltres, resultats, resultatsVisibles, nombreResultats, isLoading, villesSuggestions, hasMore, chargerPlus } =
    useRecherche(searchParams);

  const [vue, setVue] = useState('grid');
  const [drawerOpen, setDrawerOpen] = useState(false);

  const mapCenter = useMemo(() => {
    if (!resultats.length) return [46.6034, 1.8883];
    return [resultats[0].lat, resultats[0].lng];
  }, [resultats]);

  useEffect(() => {
    const typeLabel = filtres.type ? `${filtres.type}s` : 'Biens';
    const villeLabel = filtres.ville ? ` à ${filtres.ville}` : ' en France';
    const title = `${typeLabel} à vendre${villeLabel} - ${nombreResultats} résultats`;
    const description = `Découvrez ${nombreResultats} annonces${villeLabel}. Filtrez par prix, surface, DPE, options et statut.`;

    document.title = title;

    let meta = document.querySelector('meta[name="description"]');
    if (!meta) {
      meta = document.createElement('meta');
      meta.setAttribute('name', 'description');
      document.head.appendChild(meta);
    }
    meta.setAttribute('content', description);
  }, [filtres.type, filtres.ville, nombreResultats]);

  return (
    <main className="min-h-screen bg-slate-50 pb-10">
      <section className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <header className="mb-5 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
          <h1 className="text-2xl font-bold text-slate-900">Recherche immobilière</h1>
          <p className="mt-2 text-sm text-slate-600">{nombreResultats} biens correspondent à votre recherche.</p>
        </header>

        <div className="mb-4 flex items-center justify-between lg:hidden">
          <button
            type="button"
            onClick={() => setDrawerOpen(true)}
            className="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white"
          >
            Filtrer
          </button>
          <button
            type="button"
            onClick={resetFiltres}
            className="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium"
          >
            Reset filtres
          </button>
        </div>

        <div className="grid gap-6 lg:grid-cols-[280px,minmax(0,1fr)]">
          <aside className="hidden h-fit rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 lg:sticky lg:top-4 lg:block">
            <FiltresRecherche
              filtres={filtres}
              setFiltre={setFiltre}
              resetFiltres={resetFiltres}
              villesSuggestions={villesSuggestions}
            />
          </aside>

          <section className="space-y-4">
            <div className="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
              <div className="flex items-center gap-2">
                <span className="text-sm font-medium text-slate-700">Trier par</span>
                <select
                  className="rounded-lg border border-slate-300 px-3 py-2 text-sm"
                  value={filtres.tri}
                  onChange={(e) => setFiltre('tri', e.target.value)}
                >
                  {SORT_OPTIONS.map((option) => (
                    <option key={option.value} value={option.value}>
                      {option.label}
                    </option>
                  ))}
                </select>
              </div>

              <div className="flex items-center gap-2">
                <button
                  type="button"
                  onClick={() => setVue('grid')}
                  className={`rounded-lg px-3 py-2 text-sm ${vue === 'grid' ? 'bg-slate-900 text-white' : 'bg-slate-100'}`}
                >
                  Grid
                </button>
                <button
                  type="button"
                  onClick={() => setVue('list')}
                  className={`rounded-lg px-3 py-2 text-sm ${vue === 'list' ? 'bg-slate-900 text-white' : 'bg-slate-100'}`}
                >
                  Liste
                </button>
              </div>
            </div>

            <section className="h-72 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
              <MapContainer center={mapCenter} zoom={6} scrollWheelZoom className="h-full w-full">
                <TileLayer
                  attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                  url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                />
                {resultats.map((bien) => (
                  <Marker key={bien.id} position={[bien.lat, bien.lng]}>
                    <Popup>
                      <strong>{bien.titre}</strong>
                      <br />
                      {formatPrice(bien.prix)}
                    </Popup>
                  </Marker>
                ))}
              </MapContainer>
            </section>

            {isLoading ? (
              <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                {Array.from({ length: 6 }).map((_, i) => (
                  <SkeletonCard key={i} />
                ))}
              </div>
            ) : (
              <div className={vue === 'grid' ? 'grid gap-4 sm:grid-cols-2 xl:grid-cols-3' : 'space-y-4'}>
                {resultatsVisibles.map((bien) => (
                  <BienCard key={bien.id} bien={bien} view={vue} />
                ))}
              </div>
            )}

            {!isLoading && resultatsVisibles.length === 0 && (
              <p className="rounded-xl bg-amber-50 p-4 text-sm text-amber-700">
                Aucun bien trouvé avec ces filtres. Essayez d'élargir votre recherche.
              </p>
            )}

            {hasMore && (
              <div className="text-center">
                <button
                  type="button"
                  onClick={chargerPlus}
                  className="rounded-xl border border-slate-300 bg-white px-5 py-2 text-sm font-medium"
                >
                  Charger plus
                </button>
              </div>
            )}
          </section>
        </div>
      </section>

      {drawerOpen && (
        <div className="fixed inset-0 z-50 bg-black/40 lg:hidden">
          <div className="absolute left-0 top-0 h-full w-[88%] max-w-sm overflow-y-auto bg-white p-5">
            <div className="mb-4 flex items-center justify-between">
              <h2 className="text-lg font-semibold">Filtres</h2>
              <button type="button" onClick={() => setDrawerOpen(false)} className="text-sm font-medium text-slate-600">
                Fermer
              </button>
            </div>
            <FiltresRecherche
              filtres={filtres}
              setFiltre={setFiltre}
              resetFiltres={resetFiltres}
              villesSuggestions={villesSuggestions}
            />
          </div>
        </div>
      )}
    </main>
  );
}
