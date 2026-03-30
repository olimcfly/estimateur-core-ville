'use client';

import { useMemo, useState } from 'react';
import dynamic from 'next/dynamic';
import Link from 'next/link';
import 'leaflet/dist/leaflet.css';

const MapContainer = dynamic(() => import('react-leaflet').then((mod) => mod.MapContainer), { ssr: false });
const TileLayer = dynamic(() => import('react-leaflet').then((mod) => mod.TileLayer), { ssr: false });
const Marker = dynamic(() => import('react-leaflet').then((mod) => mod.Marker), { ssr: false });
const Popup = dynamic(() => import('react-leaflet').then((mod) => mod.Popup), { ssr: false });

const STATUS_STYLES = {
  'À vendre': 'bg-emerald-100 text-emerald-700 ring-emerald-600/20',
  'Sous compromis': 'bg-amber-100 text-amber-700 ring-amber-600/20',
  Vendu: 'bg-rose-100 text-rose-700 ring-rose-600/20',
};

const HIGHLIGHT_ICONS = {
  Parking: '🅿️',
  Balcon: '🌤️',
  Cave: '📦',
  Ascenseur: '🛗',
  Terrasse: '☀️',
  Jardin: '🌳',
  Garage: '🚗',
  'Vue dégagée': '🌇',
  Meublé: '🛋️',
  'Métro proche': '🚇',
};

function formatPrice(value) {
  return new Intl.NumberFormat('fr-FR').format(value);
}

function SimilarCard({ item }) {
  return (
    <article className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <img src={item.photos?.[0] ?? '/img1.jpg'} alt={item.titre} className="h-44 w-full object-cover" />
      <div className="space-y-2 p-4">
        <h3 className="line-clamp-1 text-base font-semibold text-slate-900">{item.titre}</h3>
        <p className="text-sm text-slate-600">
          {item.surface} m² • {item.pieces} pièces • {item.ville}
        </p>
        <p className="text-lg font-bold text-slate-900">{formatPrice(item.prix)} €</p>
        <Link
          href={`/bien/${item.id}`}
          className="inline-flex rounded-lg bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-700"
        >
          Voir le bien
        </Link>
      </div>
    </article>
  );
}

export default function BienDetailClient({ bien, similarBiens, canonicalUrl }) {
  const [activePhoto, setActivePhoto] = useState(0);
  const [isFullscreen, setIsFullscreen] = useState(false);

  const heroPhoto = useMemo(() => bien.photos?.[activePhoto] ?? '/img1.jpg', [bien.photos, activePhoto]);

  return (
    <main className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <div className="grid grid-cols-1 gap-8 lg:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)]">
        <section className="space-y-8">
          <div className="space-y-4">
            <button
              type="button"
              onClick={() => setIsFullscreen(true)}
              className="group relative block w-full overflow-hidden rounded-2xl"
            >
              <img
                src={heroPhoto}
                alt={`${bien.titre} - photo ${activePhoto + 1}`}
                className="h-[260px] w-full object-cover sm:h-[360px] lg:h-[420px]"
              />
              <span className="absolute bottom-3 right-3 rounded-full bg-black/70 px-3 py-1 text-xs font-medium text-white">
                Plein écran
              </span>
            </button>

            <div className="grid grid-cols-4 gap-2 sm:grid-cols-6">
              {bien.photos.map((photo, index) => (
                <button
                  key={`${photo}-${index}`}
                  type="button"
                  onClick={() => setActivePhoto(index)}
                  className={`overflow-hidden rounded-xl ring-2 transition ${
                    index === activePhoto ? 'ring-slate-900' : 'ring-transparent hover:ring-slate-300'
                  }`}
                >
                  <img src={photo} alt={`Miniature ${index + 1}`} className="h-16 w-full object-cover sm:h-20" />
                </button>
              ))}
            </div>
          </div>

          <header className="space-y-3">
            <span
              className={`inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 ${STATUS_STYLES[bien.statut] ?? 'bg-slate-100 text-slate-700 ring-slate-500/20'}`}
            >
              {bien.statut}
            </span>
            <h1 className="text-2xl font-bold text-slate-900 sm:text-3xl">
              {bien.type} • {bien.ville} • {formatPrice(bien.prix)} €
            </h1>
            <p className="text-base text-slate-600">{bien.titre}</p>
          </header>

          <section className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
            {[
              ['Surface', `${bien.surface} m²`],
              ['Pièces', bien.pieces],
              ['Chambres', bien.chambres],
              ['Étage', bien.etage],
              ['DPE', bien.dpe],
            ].map(([label, value]) => (
              <div key={label} className="rounded-xl border border-slate-200 bg-white p-3">
                <p className="text-xs uppercase tracking-wide text-slate-500">{label}</p>
                <p className="mt-1 text-lg font-semibold text-slate-900">{value}</p>
              </div>
            ))}
          </section>

          <section className="space-y-3 rounded-2xl border border-slate-200 bg-white p-5">
            <h2 className="text-xl font-semibold text-slate-900">Description</h2>
            <p className="leading-relaxed text-slate-700">{bien.description}</p>
          </section>

          <section className="space-y-3 rounded-2xl border border-slate-200 bg-white p-5">
            <h2 className="text-xl font-semibold text-slate-900">Points forts</h2>
            <div className="grid grid-cols-2 gap-3 sm:grid-cols-3">
              {bien.pointsForts.map((point) => (
                <div key={point} className="flex items-center gap-2 rounded-xl bg-slate-50 p-3 text-sm font-medium text-slate-800">
                  <span>{HIGHLIGHT_ICONS[point] ?? '⭐'}</span>
                  <span>{point}</span>
                </div>
              ))}
            </div>
          </section>

          <section className="space-y-3 rounded-2xl border border-slate-200 bg-white p-5">
            <h2 className="text-xl font-semibold text-slate-900">Localisation approximative</h2>
            <div className="h-72 overflow-hidden rounded-xl">
              <MapContainer center={[bien.lat, bien.lng]} zoom={13} scrollWheelZoom className="h-full w-full">
                <TileLayer
                  attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                  url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                />
                <Marker position={[bien.lat, bien.lng]}>
                  <Popup>{bien.titre}</Popup>
                </Marker>
              </MapContainer>
            </div>
          </section>

          <section className="space-y-4">
            <h2 className="text-xl font-semibold text-slate-900">Biens similaires</h2>
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
              {similarBiens.map((item) => (
                <SimilarCard key={item.id} item={item} />
              ))}
            </div>
          </section>
        </section>

        <aside className="lg:sticky lg:top-6 lg:h-fit">
          <div className="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 className="text-lg font-semibold text-slate-900">Être contacté</h2>
            <form className="space-y-3">
              <input
                type="text"
                name="nom"
                placeholder="Nom"
                className="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none ring-slate-900 focus:ring"
              />
              <input
                type="email"
                name="email"
                placeholder="Email"
                className="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none ring-slate-900 focus:ring"
              />
              <input
                type="tel"
                name="telephone"
                placeholder="Téléphone"
                className="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none ring-slate-900 focus:ring"
              />
              <textarea
                name="message"
                rows={4}
                defaultValue={`Je suis intéressé par ce bien (${bien.type} à ${bien.ville} - ${formatPrice(bien.prix)} €).`}
                className="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none ring-slate-900 focus:ring"
              />
              <button
                type="submit"
                className="w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700"
              >
                Être contacté
              </button>
            </form>
            <Link href="/estimation" className="block text-center text-sm font-medium text-slate-700 underline hover:text-slate-900">
              Estimer mon bien
            </Link>
            <p className="text-xs text-slate-500">URL canonique: {canonicalUrl}</p>
          </div>
        </aside>
      </div>

      {isFullscreen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/95 p-4">
          <button
            type="button"
            onClick={() => setIsFullscreen(false)}
            className="absolute right-4 top-4 rounded-full bg-white/10 px-3 py-1 text-white hover:bg-white/20"
          >
            Fermer ✕
          </button>
          <button
            type="button"
            onClick={() => setActivePhoto((prev) => (prev === 0 ? bien.photos.length - 1 : prev - 1))}
            className="absolute left-3 rounded-full bg-white/10 px-3 py-2 text-2xl text-white hover:bg-white/20"
          >
            ‹
          </button>
          <img src={heroPhoto} alt="Photo en plein écran" className="max-h-[90vh] w-full max-w-5xl rounded-xl object-contain" />
          <button
            type="button"
            onClick={() => setActivePhoto((prev) => (prev + 1) % bien.photos.length)}
            className="absolute right-3 rounded-full bg-white/10 px-3 py-2 text-2xl text-white hover:bg-white/20"
          >
            ›
          </button>
        </div>
      )}
    </main>
  );
}
