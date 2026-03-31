'use client';

import { useEffect, useMemo, useState } from 'react';
import {
  CartesianGrid,
  Line,
  LineChart,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis,
} from 'recharts';

const formatCurrency = (value) =>
  new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR',
    maximumFractionDigits: 0,
  }).format(value || 0);

function AnimatedNumber({ value, suffix = '', duration = 700 }) {
  const [displayValue, setDisplayValue] = useState(0);

  useEffect(() => {
    let raf;
    const start = performance.now();
    const from = displayValue;

    const step = (time) => {
      const progress = Math.min(1, (time - start) / duration);
      const next = from + (value - from) * progress;
      setDisplayValue(next);
      if (progress < 1) raf = requestAnimationFrame(step);
    };

    raf = requestAnimationFrame(step);
    return () => cancelAnimationFrame(raf);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [value]);

  return (
    <span>
      {Math.round(displayValue).toLocaleString('fr-FR')}
      {suffix}
    </span>
  );
}

function SkeletonResult() {
  return (
    <div className="space-y-4 animate-pulse">
      <div className="h-5 w-1/3 rounded bg-slate-200" />
      <div className="h-10 w-3/4 rounded bg-slate-200" />
      <div className="grid gap-3 sm:grid-cols-3">
        {Array.from({ length: 3 }).map((_, i) => (
          <div key={i} className="h-24 rounded-xl bg-slate-200" />
        ))}
      </div>
      <div className="h-64 rounded-xl bg-slate-200" />
    </div>
  );
}

export default function ResultatEstimation({ estimation, isLoading }) {
  const trendData = useMemo(
    () =>
      (estimation?.historiquePrix || []).map((item) => ({
        ...item,
        prixM2: Number(item.prixM2.toFixed(0)),
      })),
    [estimation?.historiquePrix]
  );

  if (isLoading) {
    return (
      <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <SkeletonResult />
      </section>
    );
  }

  if (!estimation) return null;

  return (
    <section className="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <header className="space-y-2">
        <p className="text-sm font-medium uppercase tracking-wide text-indigo-600">Résultat de l'estimation</p>
        <h2 className="text-2xl font-bold text-slate-900 sm:text-3xl">
          Entre {formatCurrency(estimation.prixBas)} et {formatCurrency(estimation.prixHaut)}
        </h2>
        <p className="text-sm text-slate-600">Basé sur les données de marché locales et vos caractéristiques.</p>
      </header>

      <div className="grid gap-4 sm:grid-cols-3">
        <article className="rounded-xl bg-indigo-50 p-4">
          <p className="text-xs uppercase tracking-wide text-indigo-700">Prix estimé / m²</p>
          <p className="mt-2 text-2xl font-semibold text-indigo-900">
            <AnimatedNumber value={estimation.prixM2Estime} suffix=" €" />
          </p>
        </article>
        <article className="rounded-xl bg-emerald-50 p-4">
          <p className="text-xs uppercase tracking-wide text-emerald-700">Marché local</p>
          <p className="mt-2 text-sm font-semibold text-emerald-900">{estimation.comparaisonLocale}</p>
        </article>
        <article className="rounded-xl bg-amber-50 p-4">
          <p className="text-xs uppercase tracking-wide text-amber-700">Tendance quartier (12 mois)</p>
          <p className="mt-2 text-2xl font-semibold text-amber-900">
            <AnimatedNumber value={estimation.variationQuartier} suffix=" %" />
          </p>
        </article>
      </div>

      <div className="h-72 rounded-xl bg-slate-50 p-3">
        <ResponsiveContainer width="100%" height="100%">
          <LineChart data={trendData} margin={{ top: 8, right: 12, left: 0, bottom: 0 }}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="mois" />
            <YAxis />
            <Tooltip formatter={(value) => `${Number(value).toLocaleString('fr-FR')} €/m²`} />
            <Line type="monotone" dataKey="prixM2" stroke="#4f46e5" strokeWidth={3} dot={false} />
          </LineChart>
        </ResponsiveContainer>
      </div>

      <div className="flex flex-wrap gap-3">
        <a
          href="#contact-expert"
          className="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700"
        >
          Être contacté par un expert
        </a>
        <button
          type="button"
          onClick={() => window.print()}
          className="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
        >
          Export PDF du rapport
        </button>
      </div>
    </section>
  );
}
