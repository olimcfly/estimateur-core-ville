import Link from 'next/link';
import BoutonFavori from '../components/BoutonFavori';

export const metadata = {
  title: 'Estimation Immobilière Gratuite Bordeaux',
  description: 'Estimez votre bien immobilier à Bordeaux en quelques minutes. Gratuit, rapide et fiable.',
};

const exempleBien = {
  id: 'demo-lyon-69001',
  titre: 'Appartement T3 - Lyon 1er',
  ville: 'Lyon',
  prix: '395 000 €',
  url: '/favoris',
  image: 'https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1200&q=80',
};

export default function HomePage() {
  return (
    <main className="mx-auto w-full max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
      <section className="rounded-3xl bg-gradient-to-br from-slate-900 to-slate-700 p-8 text-white shadow-xl sm:p-12">
        <p className="text-sm uppercase tracking-[0.18em] text-slate-300">Next.js App Router</p>
        <h1 className="mt-3 text-3xl font-bold sm:text-4xl">Estimez un bien immobilier par ville</h1>
        <p className="mt-4 max-w-2xl text-slate-200">
          Le socle front est prêt. Vous pouvez maintenant brancher vos données villes, API d\'estimation et tunnels de
          génération de leads.
        </p>
        <div className="mt-6 flex flex-wrap gap-3">
          <Link href="/favoris" className="rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-slate-900">
            Ouvrir mes favoris
          </Link>
          <a href="#preview" className="rounded-lg border border-slate-400 px-4 py-2.5 text-sm font-medium">
            Voir un aperçu
          </a>
        </div>
      </section>

      <section id="preview" className="mt-10 grid gap-6 lg:grid-cols-2">
        <article className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <div className="relative h-56 bg-slate-100">
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img src={exempleBien.image} alt={exempleBien.titre} className="h-full w-full object-cover" />
            <BoutonFavori bien={exempleBien} className="absolute right-4 top-4" />
          </div>
          <div className="space-y-2 p-5">
            <h2 className="text-xl font-semibold">{exempleBien.titre}</h2>
            <p className="text-sm text-slate-600">{exempleBien.ville}</p>
            <p className="text-base font-semibold text-slate-900">{exempleBien.prix}</p>
          </div>
        </article>

        <article className="rounded-2xl border border-dashed border-slate-300 bg-white p-6">
          <h2 className="text-lg font-semibold text-slate-900">Étapes suivantes</h2>
          <ul className="mt-4 list-disc space-y-2 pl-5 text-sm text-slate-700">
            <li>Connecter les données de villes depuis <code>data/villes</code>.</li>
            <li>Brancher les endpoints de calcul d\'estimation.</li>
            <li>Compléter la capture lead et les e-mails de suivi.</li>
          </ul>
        </article>
      </section>
    </main>
  );
}
