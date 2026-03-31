"use client";

import { useState } from "react";

const initialAlerts = [
  { id: 1, ville: "Lyon", type: "Appartement", budget: "350000", surface: "60", frequence: "Quotidien", active: true, newAds: 4 },
  { id: 2, ville: "Bordeaux", type: "Maison", budget: "500000", surface: "100", frequence: "Hebdo", active: false, newAds: 1 },
];

export default function AlertesPage() {
  const [alerts, setAlerts] = useState(initialAlerts);
  const [form, setForm] = useState({ ville: "", type: "Appartement", budget: "", surface: "", frequence: "Immédiat" });

  const addAlert = (e) => {
    e.preventDefault();
    if (!form.ville || !form.budget) return;
    setAlerts((current) => [
      ...current,
      { id: Date.now(), ...form, active: true, newAds: 0 },
    ]);
    setForm({ ville: "", type: "Appartement", budget: "", surface: "", frequence: "Immédiat" });
  };

  return (
    <section className="space-y-6 p-4 sm:p-6">
      <h1 className="text-2xl font-bold text-slate-900">Mes alertes email</h1>

      <form onSubmit={addAlert} className="grid gap-3 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100 md:grid-cols-3">
        {[
          ["ville", "Ville"],
          ["budget", "Budget max (€)"],
          ["surface", "Surface min (m²)"],
        ].map(([key, label]) => (
          <label key={key} className="text-sm text-slate-600">
            {label}
            <input
              value={form[key]}
              onChange={(e) => setForm((f) => ({ ...f, [key]: e.target.value }))}
              className="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"
            />
          </label>
        ))}

        <label className="text-sm text-slate-600">
          Type de bien
          <select
            value={form.type}
            onChange={(e) => setForm((f) => ({ ...f, type: e.target.value }))}
            className="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"
          >
            <option>Appartement</option>
            <option>Maison</option>
            <option>Terrain</option>
          </select>
        </label>

        <label className="text-sm text-slate-600">
          Fréquence
          <select
            value={form.frequence}
            onChange={(e) => setForm((f) => ({ ...f, frequence: e.target.value }))}
            className="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"
          >
            <option>Immédiat</option>
            <option>Quotidien</option>
            <option>Hebdo</option>
          </select>
        </label>

        <div className="md:col-span-3">
          <button className="rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white">
            Créer une alerte email
          </button>
        </div>
      </form>

      <article className="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
        <h2 className="text-lg font-semibold">Alertes actives</h2>
        <ul className="mt-4 space-y-3">
          {alerts.map((alert) => (
            <li key={alert.id} className="rounded-xl border border-slate-200 p-4">
              <p className="font-medium text-slate-900">
                {alert.ville} • {alert.type} • {alert.budget}€ • {alert.surface || "-"}m²
              </p>
              <p className="mt-1 text-sm text-slate-500">
                Fréquence : {alert.frequence} — Vous avez {alert.newAds} nouvelles annonces
              </p>
              <div className="mt-3 flex gap-2 text-sm">
                <button
                  onClick={() =>
                    setAlerts((all) =>
                      all.map((a) => (a.id === alert.id ? { ...a, active: !a.active } : a))
                    )
                  }
                  className="rounded-lg bg-slate-100 px-3 py-1.5"
                >
                  {alert.active ? "Désactiver" : "Activer"}
                </button>
                <button
                  onClick={() => setAlerts((all) => all.filter((a) => a.id !== alert.id))}
                  className="rounded-lg bg-rose-100 px-3 py-1.5 text-rose-700"
                >
                  Supprimer
                </button>
              </div>
            </li>
          ))}
        </ul>
      </article>
    </section>
  );
}
