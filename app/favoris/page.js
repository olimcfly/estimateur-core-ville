'use client';

import Link from 'next/link';
import { useMemo } from 'react';
import BoutonFavori from '../../components/BoutonFavori';
import { useFavoris } from '../../hooks/useFavoris';

function CarteBien({ bien, onRetirer }) {
  return (
    <article className="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md">
      <div className="relative h-44 w-full bg-slate-100">
        {bien.image ? (
          // eslint-disable-next-line @next/next/no-img-element
          <img src={bien.image} alt={bien.titre} className="h-full w-full object-cover" />
        ) : (
          <div className="flex h-full items-center justify-center text-sm text-slate-500">Image indisponible</div>
        )}

        <BoutonFavori bien={bien} className="absolute right-3 top-3" taille="sm" />
      </div>

      <div className="space-y-3 p-4">
        <h2 className="line-clamp-2 text-base font-semibold text-slate-900">{bien.titre ?? 'Bien immobilier'}</h2>

        <div className="flex items-center justify-between text-sm text-slate-600">
          <span>{bien.ville ?? 'Ville inconnue'}</span>
          <span className="font-semibold text-slate-900">{bien.prix ?? 'Prix non renseigné'}</span>
        </div>

        <button
          type="button"
          onClick={() => onRetirer(bien.id)}
          className="w-full rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50"
        >
          Retirer des favoris
        </button>
      </div>
    </article>
  );
}

export default function FavorisPage() {
  const { favoris, supprimerFavori, viderFavoris, compteur } = useFavoris();

  const emailHref = useMemo(() => {
    if (!favoris.length) return 'mailto:';

    const sujet = `Mes ${favoris.length} biens favoris`;
    const lignes = favoris.map((bien, index) => {
      const titre = bien.titre ?? `Bien #${index + 1}`;
      const ville = bien.ville ? ` - ${bien.ville}` : '';
      const prix = bien.prix ? ` - ${bien.prix}` : '';
      const url = bien.url ? `\n${bien.url}` : '';

      return `• ${titre}${ville}${prix}${url}`;
    });

    const corps = `Bonjour,\n\nJe souhaite être contacté(e) pour ces biens :\n\n${lignes.join(
      '\n\n'
    )}\n\nMerci.`;

    return `mailto:?subject=${encodeURIComponent(sujet)}&body=${encodeURIComponent(corps)}`;
  }, [favoris]);

  return (
    <main className="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
      <header className="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-900 sm:text-3xl">Mes favoris</h1>
          <p className="mt-1 text-sm text-slate-600">Total : {compteur} biens sauvegardés</p>
        </div>

        <div className="flex flex-wrap gap-3">
          <a
            href={emailHref}
            className="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-700"
          >
            Être contacté pour ces biens
          </a>
          <button
            type="button"
            onClick={viderFavoris}
            disabled={!compteur}
            className="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
          >
            Supprimer tout
          </button>
        </div>
      </header>

      {!favoris.length ? (
        <section className="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
          <p className="text-base font-medium text-slate-700">Aucun bien sauvegardé</p>
          <Link
            href="/"
            className="mt-4 inline-flex rounded-lg bg-red-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-600"
          >
            Voir les biens disponibles
          </Link>
        </section>
      ) : (
        <section className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
          {favoris.map((bien) => (
            <CarteBien key={bien.id} bien={bien} onRetirer={supprimerFavori} />
          ))}
        </section>
      )}
    </main>
  );
}
