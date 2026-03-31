const stats = [
  { label: "Favoris", value: 18, color: "bg-blue-100 text-blue-700" },
  { label: "Alertes actives", value: 6, color: "bg-emerald-100 text-emerald-700" },
  { label: "Messages non lus", value: 3, color: "bg-rose-100 text-rose-700" },
];

const viewedProperties = [
  "T3 lumineux - Lyon 6e",
  "Maison 4 chambres - Bordeaux Caudéran",
  "Studio rénové - Nantes centre",
];

const recommendations = [
  "Appartement T2 avec balcon à Toulouse (245 000 €)",
  "Maison familiale proche gare à Rennes (420 000 €)",
  "Loft moderne à Montpellier centre (365 000 €)",
];

export default function DashboardPage() {
  return (
    <section className="space-y-6 p-4 sm:p-6">
      <header>
        <h1 className="text-2xl font-bold text-slate-900">Mon dashboard</h1>
        <p className="text-sm text-slate-600">Bienvenue, voici votre activité récente.</p>
      </header>

      <div className="grid gap-4 sm:grid-cols-3">
        {stats.map((stat) => (
          <article key={stat.label} className="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
            <p className="text-sm text-slate-500">{stat.label}</p>
            <p className={`mt-2 inline-flex rounded-full px-3 py-1 text-xl font-bold ${stat.color}`}>
              {stat.value}
            </p>
          </article>
        ))}
      </div>

      <div className="grid gap-4 xl:grid-cols-2">
        <article className="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
          <h2 className="text-lg font-semibold text-slate-900">Derniers biens consultés</h2>
          <ul className="mt-3 space-y-2 text-sm text-slate-700">
            {viewedProperties.map((item) => (
              <li key={item} className="rounded-lg bg-slate-50 p-3">{item}</li>
            ))}
          </ul>
        </article>

        <article className="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
          <h2 className="text-lg font-semibold text-slate-900">Recommandations personnalisées</h2>
          <ul className="mt-3 space-y-2 text-sm text-slate-700">
            {recommendations.map((item) => (
              <li key={item} className="rounded-lg bg-slate-50 p-3">{item}</li>
            ))}
          </ul>
        </article>
      </div>

      <article className="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
        <h2 className="text-lg font-semibold text-slate-900">Statut de dossier en cours</h2>
        <div className="mt-4">
          <div className="mb-2 flex justify-between text-sm text-slate-600">
            <span>Demande de financement</span>
            <span>70%</span>
          </div>
          <div className="h-2 w-full rounded-full bg-slate-100">
            <div className="h-2 w-[70%] rounded-full bg-blue-600" />
          </div>
          <p className="mt-3 text-sm text-slate-500">
            Prochaine étape : envoyer le justificatif de revenus.
          </p>
        </div>
      </article>
    </section>
  );
}
