"use client";

import Link from "next/link";

export default function RegisterPage() {
  return (
    <main className="mx-auto flex min-h-screen max-w-md items-center px-4">
      <form className="w-full space-y-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
        <h1 className="text-2xl font-bold">Inscription</h1>
        <input className="w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Nom" />
        <input className="w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Email" />
        <input className="w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Téléphone" />
        <input className="w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Mot de passe" type="password" />
        <button className="w-full rounded-xl bg-blue-600 px-4 py-2 text-white">Créer mon compte</button>
        <p className="text-sm text-slate-600">
          Déjà inscrit ? <Link href="/login" className="text-blue-600">Se connecter</Link>
        </p>
      </form>
    </main>
  );
}
