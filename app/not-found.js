import Link from 'next/link';

const SUGGESTED = [
  { id: 's1', titre: 'Appartement familial à Lyon', prix: '420 000 €' },
  { id: 's2', titre: 'Maison avec jardin à Toulouse', prix: '510 000 €' },
  { id: 's3', titre: 'Studio investisseur à Bordeaux', prix: '189 000 €' },
];

export default function NotFound() {
  return (
    <main className="mx-auto max-w-5xl px-4 py-10 text-center sm:py-16">
      <svg role="img" aria-label="Maison introuvable" viewBox="0 0 240 180" className="mx-auto h-40 w-56 text-slate-400">
        <path d="M20 90L120 20l100 70" fill="none" stroke="currentColor" strokeWidth="10" strokeLinecap="round" />
        <rect x="55" y="85" width="130" height="70" rx="12" fill="none" stroke="currentColor" strokeWidth="10" />
        <circle cx="120" cy="120" r="16" fill="none" stroke="currentColor" strokeWidth="10" />
      </svg>

      <h1 className="mt-4 text-3xl font-bold text-slate-900">404 — Maison introuvable</h1>
      <p className="mt-2 text-slate-600">La page demandée s'est peut-être déplacée, ou n'existe plus.</p>

      <form className="mx-auto mt-6 flex max-w-xl gap-2">
        <input type="search" aria-label="Recherche rapide" placeholder="Rechercher un bien, une ville..." className="w-full rounded-xl border border-slate-300 px-4 py-2" />
        <button type="submit" className="rounded-xl bg-slate-900 px-4 py-2 text-white">Rechercher</button>
      </form>

      <div className="mt-6 flex flex-wrap justify-center gap-3">
        <Link href="/" className="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white">Retour accueil</Link>
        <Link href="/recherche" className="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium">Rechercher un bien</Link>
        <Link href="/contact" className="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium">Nous contacter</Link>
      </div>

      <section className="mt-10 text-left">
        <h2 className="text-lg font-semibold text-slate-900">Biens suggérés</h2>
        <div className="mt-3 grid gap-3 sm:grid-cols-3">
          {SUGGESTED.map((item) => (
            <article key={item.id} className="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
              <p className="font-medium text-slate-900">{item.titre}</p>
              <p className="mt-1 text-sm text-slate-600">{item.prix}</p>
            </article>
          ))}
        </div>
      </section>
    </main>
  );
}
