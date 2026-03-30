'use client';

import { useEffect, useMemo, useRef, useState } from 'react';

const formatPrice = (value) =>
  new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR',
    maximumFractionDigits: 0,
  }).format(value);

export default function LocaliteClient({ ville, villeSlug, cityData }) {
  const mapRef = useRef(null);
  const mapInstanceRef = useRef(null);

  const [typeBien, setTypeBien] = useState('Tous');
  const [budgetMin, setBudgetMin] = useState('');
  const [budgetMax, setBudgetMax] = useState('');
  const [surfaceMin, setSurfaceMin] = useState('');

  const biens = cityData?.biens || [];

  const typesDisponibles = useMemo(() => {
    const set = new Set(biens.map((bien) => bien.type));
    return ['Tous', ...set];
  }, [biens]);

  const biensFiltres = useMemo(() => {
    return biens.filter((bien) => {
      const typeOk = typeBien === 'Tous' || bien.type === typeBien;
      const minOk = !budgetMin || bien.prix >= Number(budgetMin);
      const maxOk = !budgetMax || bien.prix <= Number(budgetMax);
      const surfaceOk = !surfaceMin || bien.surface >= Number(surfaceMin);

      return typeOk && minOk && maxOk && surfaceOk;
    });
  }, [biens, typeBien, budgetMin, budgetMax, surfaceMin]);

  useEffect(() => {
    let ignore = false;

    const initMap = async () => {
      if (!mapRef.current || mapInstanceRef.current) {
        return;
      }

      const L = await import('leaflet');
      const leaflet = L.default || L;

      if (ignore) return;

      const map = leaflet.map(mapRef.current).setView(cityData.center, 12);

      leaflet
        .tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 19,
          attribution: '&copy; OpenStreetMap contributors',
        })
        .addTo(map);

      mapInstanceRef.current = { map, leaflet };
    };

    initMap();

    return () => {
      ignore = true;
      if (mapInstanceRef.current?.map) {
        mapInstanceRef.current.map.remove();
        mapInstanceRef.current = null;
      }
    };
  }, [cityData.center]);

  useEffect(() => {
    const instance = mapInstanceRef.current;
    if (!instance?.map || !instance?.leaflet) {
      return;
    }

    const { map, leaflet } = instance;

    if (instance.markers) {
      instance.markers.forEach((marker) => marker.remove());
    }

    instance.markers = biensFiltres.map((bien) => {
      const marker = leaflet.marker([bien.lat, bien.lng]).addTo(map);
      marker.bindPopup(
        `<strong>${bien.titre}</strong><br/>${formatPrice(bien.prix)} · ${bien.surface} m² · ${bien.pieces} pièces`
      );
      return marker;
    });

    if (biensFiltres.length) {
      const group = leaflet.featureGroup(instance.markers);
      map.fitBounds(group.getBounds().pad(0.2));
    } else {
      map.setView(cityData.center, 12);
    }
  }, [biensFiltres, cityData.center]);

  return (
    <main className="min-h-screen bg-slate-50 pb-14">
      <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossOrigin=""
      />

      <section className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <header className="mb-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
          <p className="text-sm font-medium uppercase tracking-wide text-indigo-600">Localité</p>
          <h1 className="mt-2 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
            Immobilier à {ville}
          </h1>
          <p className="mt-4 inline-flex items-center rounded-full bg-indigo-50 px-4 py-1 text-sm font-semibold text-indigo-700">
            Prix moyen : {cityData.prixM2 ? `${cityData.prixM2.toLocaleString('fr-FR')} €/m²` : 'N/A'}
          </p>
        </header>

        <div className="grid gap-6 lg:grid-cols-[300px,1fr]">
          <aside className="top-4 h-fit rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 lg:sticky">
            <h2 className="text-lg font-semibold text-slate-900">Filtres</h2>
            <div className="mt-4 space-y-4">
              <label className="block text-sm font-medium text-slate-700">
                Type de bien
                <select
                  className="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                  value={typeBien}
                  onChange={(e) => setTypeBien(e.target.value)}
                >
                  {typesDisponibles.map((type) => (
                    <option key={type} value={type}>
                      {type}
                    </option>
                  ))}
                </select>
              </label>

              <label className="block text-sm font-medium text-slate-700">
                Budget min (€)
                <input
                  type="number"
                  min="0"
                  value={budgetMin}
                  onChange={(e) => setBudgetMin(e.target.value)}
                  className="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                  placeholder="Ex : 250000"
                />
              </label>

              <label className="block text-sm font-medium text-slate-700">
                Budget max (€)
                <input
                  type="number"
                  min="0"
                  value={budgetMax}
                  onChange={(e) => setBudgetMax(e.target.value)}
                  className="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                  placeholder="Ex : 900000"
                />
              </label>

              <label className="block text-sm font-medium text-slate-700">
                Surface min (m²)
                <input
                  type="number"
                  min="0"
                  value={surfaceMin}
                  onChange={(e) => setSurfaceMin(e.target.value)}
                  className="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                  placeholder="Ex : 40"
                />
              </label>
            </div>
          </aside>

          <div className="space-y-6">
            <section className="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
              <div className="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h2 className="text-lg font-semibold text-slate-900">Carte interactive</h2>
                <p className="text-sm text-slate-500">/{`localite/${villeSlug}`}</p>
              </div>
              <div ref={mapRef} className="h-[360px] w-full" />
            </section>

            <section>
              <div className="mb-4 flex items-center justify-between">
                <h2 className="text-lg font-semibold text-slate-900">Biens disponibles</h2>
                <span className="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">
                  {biensFiltres.length} résultat(s)
                </span>
              </div>

              <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                {biensFiltres.map((bien) => (
                  <article
                    key={bien.id}
                    className="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-lg"
                  >
                    <img
                      src={bien.image}
                      alt={bien.titre}
                      className="h-44 w-full object-cover transition duration-300 group-hover:scale-105"
                      loading="lazy"
                    />
                    <div className="space-y-2 p-4">
                      <p className="text-sm font-medium text-indigo-600">{bien.type}</p>
                      <h3 className="text-base font-semibold text-slate-900">{bien.titre}</h3>
                      <p className="text-lg font-bold text-slate-900">{formatPrice(bien.prix)}</p>
                      <div className="flex gap-2 text-sm text-slate-600">
                        <span>{bien.surface} m²</span>
                        <span>•</span>
                        <span>{bien.pieces} pièces</span>
                      </div>
                    </div>
                  </article>
                ))}
              </div>

              {biensFiltres.length === 0 && (
                <p className="mt-4 rounded-xl bg-amber-50 p-4 text-sm text-amber-700">
                  Aucun bien ne correspond à vos filtres. Essayez d\'élargir votre budget ou votre surface minimale.
                </p>
              )}
            </section>

            <section className="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
              <h2 className="text-lg font-semibold text-slate-900">FAQ locale</h2>
              <div className="mt-4 space-y-3">
                {cityData.faq.map((entry) => (
                  <details key={entry.q} className="rounded-xl border border-slate-200 px-4 py-3">
                    <summary className="cursor-pointer list-none font-medium text-slate-900">
                      {entry.q}
                    </summary>
                    <p className="mt-2 text-sm leading-6 text-slate-600">{entry.a}</p>
                  </details>
                ))}
              </div>
            </section>
          </div>
        </div>
      </section>
    </main>
  );
}
