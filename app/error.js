'use client';

import Link from 'next/link';

export default function GlobalError({ error, reset }) {
  return (
    <html>
      <body className="bg-slate-50 p-6 text-slate-900">
        <main className="mx-auto max-w-2xl rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
          <svg role="img" aria-label="Erreur serveur" viewBox="0 0 200 120" className="mx-auto h-28 w-40 text-red-400">
            <path d="M100 10l90 100H10z" fill="none" stroke="currentColor" strokeWidth="10" />
            <circle cx="100" cy="78" r="4" fill="currentColor" />
            <path d="M100 42v24" stroke="currentColor" strokeWidth="10" strokeLinecap="round" />
          </svg>

          <h1 className="mt-4 text-2xl font-bold">500 — Une erreur est survenue</h1>
          <p className="mt-2 text-slate-600">Notre équipe a été notifiée. Vous pouvez réessayer immédiatement.</p>

          <div className="mt-6 flex justify-center gap-3">
            <button type="button" onClick={() => reset()} className="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Réessayer</button>
            <Link href="/" className="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Retour accueil</Link>
          </div>

          <p className="mt-6 text-xs text-slate-400">Code technique: {error?.digest || 'ERR-500'}</p>
        </main>
      </body>
    </html>
  );
}
