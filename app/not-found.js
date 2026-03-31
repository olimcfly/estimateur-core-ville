import Link from 'next/link';

export default function NotFound() {
  return (
    <main className="mx-auto flex min-h-[60vh] w-full max-w-3xl flex-col items-start justify-center gap-4 px-4 py-12 sm:px-6 lg:px-8">
      <p className="text-sm font-semibold uppercase tracking-wide text-slate-500">404</p>
      <h1 className="text-3xl font-bold text-slate-900">Page introuvable</h1>
      <p className="text-sm text-slate-600">La ressource demandée n\'existe pas ou a été déplacée.</p>
      <Link
        href="/"
        className="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700"
      >
        Retour à l\'accueil
      </Link>
    </main>
  );
}
