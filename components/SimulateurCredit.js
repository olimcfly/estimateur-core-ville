'use client';

import { useMemo, useState } from 'react';
import {
  Cell,
  Legend,
  Line,
  LineChart,
  Pie,
  PieChart,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis,
} from 'recharts';
import useSimulateurCredit from '../hooks/useSimulateurCredit';

const formatEuros = (value) =>
  new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(value || 0);


const calcMonthly = (capital, tauxAnnuel, annees) => {
  const n = annees * 12;
  const tm = tauxAnnuel / 12 / 100;
  if (tm === 0) return n ? capital / n : 0;
  return (capital * tm) / (1 - (1 + tm) ** -n);
};

const initialScenario = {
  prixBien: 350000,
  apport: 40000,
  dureeAnnees: 20,
  tauxInteret: 3.5,
  tauxAssurance: 0.35,
  typeTaux: 'fixe',
  salaireMensuel: 4500,
};

function InputRange({ label, min, max, step = 1000, value, onChange, helper }) {
  return (
    <label className="block space-y-1">
      <span className="text-sm font-medium text-slate-700">{label}</span>
      <input
        type="range"
        min={min}
        max={max}
        step={step}
        value={value}
        onChange={(e) => onChange(Number(e.target.value))}
        className="h-2 w-full cursor-pointer appearance-none rounded-lg bg-slate-200 accent-indigo-600"
      />
      <div className="flex items-center justify-between text-xs text-slate-500">
        <span>{formatEuros(min)}</span>
        <span>{helper}</span>
        <span>{formatEuros(max)}</span>
      </div>
    </label>
  );
}

function ResumeCard({ label, value, tone = 'slate' }) {
  const toneMap = {
    slate: 'bg-slate-50 text-slate-900',
    indigo: 'bg-indigo-50 text-indigo-900',
    emerald: 'bg-emerald-50 text-emerald-900',
    amber: 'bg-amber-50 text-amber-900',
  };

  return (
    <article className={`rounded-xl p-4 ${toneMap[tone] ?? toneMap.slate}`}>
      <p className="text-xs uppercase tracking-wide">{label}</p>
      <p className="mt-2 text-xl font-semibold">{value}</p>
    </article>
  );
}

function CompareBlock({ left, right }) {
  const diffMensualite = right.mensualite - left.mensualite;
  const diffCout = right.coutTotal - left.coutTotal;

  return (
    <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <h2 className="text-lg font-semibold text-slate-900">Comparateur scénarios A vs B</h2>
      <div className="mt-4 grid gap-4 md:grid-cols-3">
        <ResumeCard label="Mensualité A" value={formatEuros(left.mensualite)} tone="indigo" />
        <ResumeCard label="Mensualité B" value={formatEuros(right.mensualite)} tone="amber" />
        <ResumeCard
          label="Différence mensualité"
          value={`${diffMensualite > 0 ? '+' : ''}${formatEuros(diffMensualite)}`}
          tone={diffMensualite <= 0 ? 'emerald' : 'amber'}
        />
      </div>
      <p className="mt-3 text-sm text-slate-600">
        Différence de coût global :
        <span className={`ml-1 font-semibold ${diffCout <= 0 ? 'text-emerald-700' : 'text-amber-700'}`}>
          {diffCout > 0 ? '+' : ''}
          {formatEuros(diffCout)}
        </span>
      </p>
    </section>
  );
}

export default function SimulateurCredit() {
  const [scenarioA, setScenarioA] = useState(initialScenario);
  const [scenarioB, setScenarioB] = useState({ ...initialScenario, dureeAnnees: 25, apport: 60000, tauxInteret: 3.7 });
  const [page, setPage] = useState(1);

  const resultA = useSimulateurCredit(scenarioA);
  const resultB = useSimulateurCredit(scenarioB);

  const apportPercent = Math.round((scenarioA.apport / scenarioA.prixBien) * 100);

  const pieData = useMemo(
    () => [
      { name: 'Capital', value: resultA.montantEmprunte },
      { name: 'Intérêts', value: resultA.totalInterets },
      { name: 'Assurance', value: resultA.totalAssurance },
    ],
    [resultA]
  );

  const courbeResteDu = useMemo(
    () => resultA.tableauAmortissement.filter((line) => line.mois % 12 === 0 || line.mois === 1),
    [resultA.tableauAmortissement]
  );

  const comparatifDurees = useMemo(() => {
    const capital = Math.max(0, scenarioA.prixBien - scenarioA.apport);
    return [15, 20, 25].map((duree) => {
      const mensualiteCredit = calcMonthly(capital, scenarioA.tauxInteret, duree);
      const mensualiteAssurance = (capital * (scenarioA.tauxAssurance / 100)) / 12;
      const mensualite = mensualiteCredit + mensualiteAssurance;
      const coutTotal = mensualite * duree * 12;
      return {
        duree: `${duree} ans`,
        mensualite,
        coutTotal,
      };
    });
  }, [scenarioA]);

  const rowsPerPage = 12;
  const totalPages = Math.ceil(resultA.tableauAmortissement.length / rowsPerPage);
  const amortissementPage = resultA.tableauAmortissement.slice((page - 1) * rowsPerPage, page * rowsPerPage);

  const exportCsv = () => {
    const header = ['Mois', 'Capital', 'Intérêts', 'Assurance', 'Mensualité', 'Reste dû'];
    const lines = resultA.tableauAmortissement.map((line) =>
      [line.mois, line.capital, line.interets, line.assurance, line.mensualite, line.resteDu].join(';')
    );
    const csv = [header.join(';'), ...lines].join('\n');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'tableau-amortissement.csv';
    a.click();
    URL.revokeObjectURL(url);
  };

  return (
    <main className="mx-auto w-full max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
      <header>
        <p className="text-sm font-semibold uppercase tracking-wide text-indigo-600">Simulateur crédit immobilier</p>
        <h1 className="mt-1 text-3xl font-bold text-slate-900">Calculez votre capacité de financement</h1>
      </header>

      <section className="grid gap-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:grid-cols-2">
        <div className="space-y-4">
          <InputRange
            label="Prix du bien"
            min={50000}
            max={1200000}
            value={scenarioA.prixBien}
            onChange={(prixBien) => setScenarioA((prev) => ({ ...prev, prixBien }))}
            helper={formatEuros(scenarioA.prixBien)}
          />

          <InputRange
            label="Apport personnel"
            min={0}
            max={Math.max(50000, scenarioA.prixBien)}
            value={Math.min(scenarioA.apport, scenarioA.prixBien)}
            onChange={(apport) => setScenarioA((prev) => ({ ...prev, apport }))}
            helper={`${formatEuros(scenarioA.apport)} (${Number.isFinite(apportPercent) ? apportPercent : 0}%)`}
          />

          <label className="block text-sm font-medium text-slate-700">
            Durée du prêt
            <select
              className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"
              value={scenarioA.dureeAnnees}
              onChange={(e) => setScenarioA((prev) => ({ ...prev, dureeAnnees: Number(e.target.value) }))}
            >
              {[10, 15, 20, 25].map((y) => (
                <option key={y} value={y}>
                  {y} ans
                </option>
              ))}
            </select>
          </label>

          <div className="grid gap-3 sm:grid-cols-2">
            <label className="block text-sm font-medium text-slate-700">
              Taux d'intérêt (%)
              <input
                type="number"
                step="0.01"
                value={scenarioA.tauxInteret}
                onChange={(e) => setScenarioA((prev) => ({ ...prev, tauxInteret: Number(e.target.value) }))}
                className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"
              />
            </label>
            <label className="block text-sm font-medium text-slate-700">
              Assurance annuelle (%)
              <input
                type="number"
                step="0.01"
                value={scenarioA.tauxAssurance}
                onChange={(e) => setScenarioA((prev) => ({ ...prev, tauxAssurance: Number(e.target.value) }))}
                className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"
              />
            </label>
          </div>

          <div className="grid gap-3 sm:grid-cols-2">
            <label className="block text-sm font-medium text-slate-700">
              Type de taux
              <select
                value={scenarioA.typeTaux}
                onChange={(e) => setScenarioA((prev) => ({ ...prev, typeTaux: e.target.value }))}
                className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"
              >
                <option value="fixe">Taux fixe</option>
                <option value="variable">Taux variable</option>
              </select>
            </label>
            <label className="block text-sm font-medium text-slate-700">
              Salaire mensuel (€)
              <input
                type="number"
                value={scenarioA.salaireMensuel}
                onChange={(e) => setScenarioA((prev) => ({ ...prev, salaireMensuel: Number(e.target.value) }))}
                className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"
              />
            </label>
          </div>
        </div>

        <div className="grid gap-3 sm:grid-cols-2">
          <ResumeCard label="Mensualité totale" value={formatEuros(resultA.mensualite)} tone="indigo" />
          <ResumeCard label="Coût total du crédit" value={formatEuros(resultA.coutTotal)} tone="amber" />
          <ResumeCard label="Montant emprunté" value={formatEuros(resultA.montantEmprunte)} tone="slate" />
          <ResumeCard label="Total intérêts" value={formatEuros(resultA.totalInterets)} tone="slate" />
          <ResumeCard
            label="Taux d'endettement"
            value={`${resultA.tauxEndettement.toLocaleString('fr-FR')} %`}
            tone={resultA.tauxEndettement <= 35 ? 'emerald' : 'amber'}
          />
        </div>
      </section>

      <section className="grid gap-6 lg:grid-cols-3">
        <article className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm lg:col-span-1">
          <h2 className="text-base font-semibold text-slate-900">Répartition du coût</h2>
          <div className="mt-3 h-64">
            <ResponsiveContainer width="100%" height="100%">
              <PieChart>
                <Pie data={pieData} dataKey="value" nameKey="name" outerRadius={85} label>
                  {['#4f46e5', '#f59e0b', '#10b981'].map((color) => (
                    <Cell key={color} fill={color} />
                  ))}
                </Pie>
                <Legend />
                <Tooltip formatter={(value) => formatEuros(value)} />
              </PieChart>
            </ResponsiveContainer>
          </div>
        </article>

        <article className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm lg:col-span-2">
          <h2 className="text-base font-semibold text-slate-900">Évolution du capital restant dû</h2>
          <div className="mt-3 h-64">
            <ResponsiveContainer width="100%" height="100%">
              <LineChart data={courbeResteDu}>
                <XAxis dataKey="mois" />
                <YAxis />
                <Tooltip formatter={(value) => formatEuros(value)} />
                <Line type="monotone" dataKey="resteDu" stroke="#4f46e5" strokeWidth={2} dot={false} />
              </LineChart>
            </ResponsiveContainer>
          </div>
        </article>
      </section>

      <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div className="mb-4 flex flex-wrap items-center justify-between gap-3">
          <h2 className="text-lg font-semibold text-slate-900">Tableau d'amortissement</h2>
          <button
            onClick={exportCsv}
            type="button"
            className="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700"
          >
            Export Excel/CSV
          </button>
        </div>

        <div className="overflow-x-auto">
          <table className="min-w-full text-sm">
            <thead>
              <tr className="border-b border-slate-200 text-left text-xs uppercase tracking-wide text-slate-500">
                <th className="py-2">Mois</th>
                <th className="py-2">Capital</th>
                <th className="py-2">Intérêts</th>
                <th className="py-2">Assurance</th>
                <th className="py-2">Reste dû</th>
              </tr>
            </thead>
            <tbody>
              {amortissementPage.map((row) => (
                <tr key={row.mois} className="border-b border-slate-100">
                  <td className="py-2">{row.mois}</td>
                  <td className="py-2">{formatEuros(row.capital)}</td>
                  <td className="py-2">{formatEuros(row.interets)}</td>
                  <td className="py-2">{formatEuros(row.assurance)}</td>
                  <td className="py-2">{formatEuros(row.resteDu)}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        <div className="mt-4 flex items-center justify-between">
          <button
            type="button"
            onClick={() => setPage((p) => Math.max(1, p - 1))}
            className="rounded-lg border border-slate-300 px-3 py-2 text-sm"
          >
            Page précédente
          </button>
          <p className="text-sm text-slate-600">
            Page {page}/{totalPages}
          </p>
          <button
            type="button"
            onClick={() => setPage((p) => Math.min(totalPages, p + 1))}
            className="rounded-lg border border-slate-300 px-3 py-2 text-sm"
          >
            Page suivante
          </button>
        </div>
      </section>

      <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 className="text-lg font-semibold text-slate-900">Comparatif durées (15/20/25 ans)</h2>
        <div className="mt-4 grid gap-4 md:grid-cols-3">
          {comparatifDurees.map((line) => (
            <ResumeCard
              key={line.duree}
              label={line.duree}
              value={`${formatEuros(line.mensualite)} / mois`}
              tone={line.duree === `${scenarioA.dureeAnnees} ans` ? 'indigo' : 'slate'}
            />
          ))}
        </div>
      </section>


      <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"> 
        <h2 className="text-lg font-semibold text-slate-900">Scénario B (comparaison)</h2>
        <div className="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
          <label className="text-sm font-medium text-slate-700">
            Prix du bien (€)
            <input
              type="number"
              value={scenarioB.prixBien}
              onChange={(e) => setScenarioB((prev) => ({ ...prev, prixBien: Number(e.target.value) }))}
              className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"
            />
          </label>
          <label className="text-sm font-medium text-slate-700">
            Apport (€)
            <input
              type="number"
              value={scenarioB.apport}
              onChange={(e) => setScenarioB((prev) => ({ ...prev, apport: Number(e.target.value) }))}
              className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"
            />
          </label>
          <label className="text-sm font-medium text-slate-700">
            Durée
            <select
              value={scenarioB.dureeAnnees}
              onChange={(e) => setScenarioB((prev) => ({ ...prev, dureeAnnees: Number(e.target.value) }))}
              className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"
            >
              {[10, 15, 20, 25].map((y) => (
                <option key={y} value={y}>{y} ans</option>
              ))}
            </select>
          </label>
          <label className="text-sm font-medium text-slate-700">
            Taux (%)
            <input
              type="number"
              step="0.01"
              value={scenarioB.tauxInteret}
              onChange={(e) => setScenarioB((prev) => ({ ...prev, tauxInteret: Number(e.target.value) }))}
              className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"
            />
          </label>
        </div>
      </section>

      <CompareBlock left={resultA} right={resultB} />

    </main>
  );
}
