'use client';

export default function GlobalError({ error, reset }) {
  return (
    <main className="mx-auto flex min-h-[60vh] w-full max-w-3xl flex-col items-start justify-center gap-4 px-4 py-12 sm:px-6 lg:px-8">
      <p className="text-sm font-semibold uppercase tracking-wide text-red-500">Erreur</p>
      <h2 className="text-2xl font-bold text-slate-900">Une erreur inattendue est survenue.</h2>
      <p className="text-sm text-slate-600">{error?.message ?? 'Veuillez réessayer dans quelques instants.'}</p>
      <button
        type="button"
        onClick={reset}
        className="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700"
      >
        Réessayer
      </button>
    </main>
  );
}
