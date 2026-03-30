export default function Loading() {
  return (
    <main className="mx-auto flex min-h-[40vh] w-full max-w-4xl items-center justify-center px-4 py-10">
      <div className="inline-flex items-center gap-3 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 shadow-sm">
        <span className="h-2.5 w-2.5 animate-pulse rounded-full bg-slate-500" />
        Chargement de la page...
      </div>
    </main>
  );
}
