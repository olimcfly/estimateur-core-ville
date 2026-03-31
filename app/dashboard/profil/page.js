"use client";

import { useState } from "react";

export default function ProfilPage() {
  const [form, setForm] = useState({
    nom: "Sofia Martin",
    email: "sofia.martin@email.com",
    telephone: "06 12 34 56 78",
  });

  const [searchPrefs] = useState([
    "Achat appartement - Lyon - Budget 350 000 €",
    "Maison avec jardin - Bordeaux - Min 90m²",
  ]);

  const [history] = useState([
    "26/03 : Appartement T3 Lyon 6e",
    "25/03 : Maison pierre Rennes",
    "23/03 : Studio Nantes centre",
  ]);

  return (
    <section className="space-y-6 p-4 sm:p-6">
      <h1 className="text-2xl font-bold text-slate-900">Mon profil</h1>

      <article className="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
        <h2 className="text-lg font-semibold">Informations personnelles</h2>
        <div className="mt-4 grid gap-4 sm:grid-cols-2">
          {[
            ["nom", "Nom complet"],
            ["email", "Email"],
            ["telephone", "Téléphone"],
          ].map(([key, label]) => (
            <label key={key} className="text-sm text-slate-600">
              {label}
              <input
                type="text"
                value={form[key]}
                onChange={(e) => setForm((f) => ({ ...f, [key]: e.target.value }))}
                className="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"
              />
            </label>
          ))}
        </div>

        <label className="mt-4 block text-sm text-slate-600">
          Photo de profil
          <input type="file" accept="image/*" className="mt-1 block w-full text-sm" />
        </label>

        <button className="mt-4 rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white">
          Enregistrer les modifications
        </button>
      </article>

      <article className="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
        <h2 className="text-lg font-semibold">Préférences de recherche sauvegardées</h2>
        <ul className="mt-3 space-y-2 text-sm">
          {searchPrefs.map((pref) => (
            <li key={pref} className="rounded-lg bg-slate-50 p-3">{pref}</li>
          ))}
        </ul>
      </article>

      <article className="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
        <h2 className="text-lg font-semibold">Historique des consultations</h2>
        <ul className="mt-3 space-y-2 text-sm">
          {history.map((item) => (
            <li key={item} className="rounded-lg bg-slate-50 p-3">{item}</li>
          ))}
        </ul>
      </article>

      <button className="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-medium text-rose-700">
        Supprimer mon compte
      </button>
    </section>
  );
}
