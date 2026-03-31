export default function GlobalLoading() {
  return (
    <main className="flex min-h-screen flex-col items-center justify-center gap-5 bg-slate-50 px-4">
      <div className="h-16 w-16 animate-pulse rounded-2xl bg-slate-900/80" />
      <div className="h-8 w-8 animate-spin rounded-full border-4 border-slate-300 border-t-slate-900" />
      <p className="text-sm font-medium text-slate-600">Chargement de votre expérience immobilière...</p>

      <section aria-hidden="true" className="mt-8 grid w-full max-w-5xl gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {Array.from({ length: 6 }).map((_, index) => (
          <article key={index} className="space-y-3 rounded-2xl border border-slate-200 bg-white p-4">
            <div className="h-36 animate-pulse rounded-xl bg-slate-200" />
            <div className="h-4 w-3/4 animate-pulse rounded bg-slate-200" />
            <div className="h-4 w-1/2 animate-pulse rounded bg-slate-200" />
            <div className="h-10 animate-pulse rounded-lg bg-slate-200" />
          </article>
        ))}
      </section>
    </main>
  );
}
