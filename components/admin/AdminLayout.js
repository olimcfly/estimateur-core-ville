'use client';

import Link from 'next/link';
import { useMemo, useState } from 'react';

const NAV_ITEMS = [
  { href: '/admin', label: 'Dashboard' },
  { href: '/admin/biens', label: 'Biens' },
  { href: '/admin/biens/nouveau', label: 'Ajouter un bien' },
  { href: '/admin/utilisateurs', label: 'Utilisateurs' },
  { href: '/admin/messages', label: 'Messages' },
  { href: '/admin/statistiques', label: 'Statistiques' },
];

export default function AdminLayout({ title, subtitle, actions, children }) {
  const [darkMode, setDarkMode] = useState(false);
  const [globalSearch, setGlobalSearch] = useState('');

  const wrapperClasses = useMemo(
    () =>
      darkMode
        ? 'min-h-screen bg-slate-950 text-slate-100'
        : 'min-h-screen bg-slate-100 text-slate-900',
    [darkMode]
  );

  const sidebarClasses = darkMode
    ? 'h-screen w-[260px] shrink-0 border-r border-slate-800 bg-slate-900 p-5'
    : 'h-screen w-[260px] shrink-0 border-r border-slate-200 bg-white p-5';

  const cardClasses = darkMode
    ? 'rounded-2xl border border-slate-800 bg-slate-900 shadow-sm'
    : 'rounded-2xl border border-slate-200 bg-white shadow-sm';

  return (
    <div className={wrapperClasses}>
      <div className="flex">
        <aside className={`${sidebarClasses} sticky top-0`}>
          <div className="mb-8">
            <p className="text-xs uppercase tracking-[0.2em] text-indigo-400">Admin panel</p>
            <h1 className="mt-2 text-xl font-bold">ImmoPilot</h1>
          </div>

          <nav className="space-y-2">
            {NAV_ITEMS.map((item) => (
              <Link
                key={item.href}
                href={item.href}
                className={`block rounded-lg px-3 py-2 text-sm font-medium transition ${
                  darkMode ? 'hover:bg-slate-800' : 'hover:bg-slate-100'
                }`}
              >
                {item.label}
              </Link>
            ))}
          </nav>
        </aside>

        <div className="flex min-w-0 flex-1 flex-col">
          <header
            className={`sticky top-0 z-20 border-b px-6 py-4 backdrop-blur ${
              darkMode ? 'border-slate-800 bg-slate-950/90' : 'border-slate-200 bg-white/90'
            }`}
          >
            <div className="flex flex-wrap items-center justify-between gap-3">
              <div className="min-w-[220px] flex-1">
                <div className="relative">
                  <input
                    type="search"
                    value={globalSearch}
                    onChange={(event) => setGlobalSearch(event.target.value)}
                    placeholder="Recherche globale (biens, utilisateurs, messages...)"
                    className={`w-full rounded-xl border px-4 py-2 text-sm outline-none transition ${
                      darkMode
                        ? 'border-slate-700 bg-slate-900 text-slate-100 placeholder:text-slate-400 focus:border-indigo-500'
                        : 'border-slate-300 bg-white text-slate-900 placeholder:text-slate-500 focus:border-indigo-500'
                    }`}
                  />
                </div>
              </div>

              <button
                type="button"
                onClick={() => setDarkMode((prev) => !prev)}
                className={`rounded-xl border px-3 py-2 text-sm font-medium ${
                  darkMode
                    ? 'border-slate-700 bg-slate-900 hover:bg-slate-800'
                    : 'border-slate-300 bg-white hover:bg-slate-50'
                }`}
              >
                {darkMode ? '☀️ Mode clair' : '🌙 Mode sombre'}
              </button>
            </div>
          </header>

          <main className="p-6">
            <section className={`${cardClasses} mb-6 p-5`}>
              <div className="flex flex-wrap items-center justify-between gap-4">
                <div>
                  <h2 className="text-2xl font-bold">{title}</h2>
                  {subtitle ? <p className="mt-1 text-sm opacity-80">{subtitle}</p> : null}
                </div>
                {actions ? <div className="flex flex-wrap gap-2">{actions}</div> : null}
              </div>
            </section>

            {children}
          </main>
        </div>
      </div>
    </div>
  );
}
