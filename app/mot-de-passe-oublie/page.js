"use client";

import Link from "next/link";

export default function ForgotPasswordPage() {
  return (
    <main className="mx-auto flex min-h-screen max-w-md items-center px-4">
      <form className="w-full space-y-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
        <h1 className="text-2xl font-bold">Mot de passe oublié</h1>
        <p className="text-sm text-slate-600">Entrez votre email pour recevoir un lien de réinitialisation.</p>
        <input className="w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Email" type="email" />
        <button className="w-full rounded-xl bg-blue-600 px-4 py-2 text-white">Envoyer le lien</button>
        <Link href="/login" className="block text-center text-sm text-blue-600">Retour à la connexion</Link>
      </form>
    </main>
  );
}
