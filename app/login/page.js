"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { useState } from "react";

export default function LoginPage() {
  const router = useRouter();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");

  const onSubmit = (e) => {
    e.preventDefault();
    if (!email || !password) return;
    document.cookie = "auth-token=demo-user-token; path=/; max-age=604800";
    router.push("/dashboard");
  };

  return (
    <main className="mx-auto flex min-h-screen max-w-md items-center px-4">
      <form onSubmit={onSubmit} className="w-full space-y-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
        <h1 className="text-2xl font-bold">Connexion</h1>
        <input className="w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Email" value={email} onChange={(e) => setEmail(e.target.value)} />
        <input className="w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Mot de passe" type="password" value={password} onChange={(e) => setPassword(e.target.value)} />
        <button className="w-full rounded-xl bg-blue-600 px-4 py-2 text-white">Se connecter</button>
        <button type="button" className="w-full rounded-xl border border-slate-200 px-4 py-2">Continuer avec Google</button>
        <div className="flex justify-between text-sm">
          <Link href="/mot-de-passe-oublie" className="text-blue-600">Mot de passe oublié ?</Link>
          <Link href="/register" className="text-blue-600">Créer un compte</Link>
        </div>
      </form>
    </main>
  );
}
