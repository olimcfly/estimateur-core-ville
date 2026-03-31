'use client';

import {
  Bar,
  BarChart,
  CartesianGrid,
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
import AdminLayout from '../../components/admin/AdminLayout';

const kpis = [
  { label: 'Biens actifs', value: 184 },
  { label: 'Biens vendus', value: 96 },
  { label: 'Biens archivés', value: 23 },
  { label: 'Visites du jour', value: 1240 },
  { label: 'Visites semaine', value: 8420 },
  { label: 'Visites mois', value: 35280 },
  { label: 'Nouveaux utilisateurs', value: 48 },
  { label: 'Messages non traités', value: 17 },
  { label: 'Taux conversion', value: '12.6%' },
];

const visites30j = Array.from({ length: 30 }, (_, index) => ({
  jour: `J${index + 1}`,
  visites: 650 + Math.round(Math.random() * 600),
}));

const repartitionTypes = [
  { name: 'Appartement', value: 45 },
  { name: 'Maison', value: 28 },
  { name: 'Terrain', value: 12 },
  { name: 'Local', value: 15 },
];

const evolutionUsers = [
  { mois: 'Nov', users: 82 },
  { mois: 'Déc', users: 105 },
  { mois: 'Jan', users: 140 },
  { mois: 'Fév', users: 166 },
  { mois: 'Mar', users: 214 },
];

const ventesMensuelles = [
  { mois: 'Jan', ventes: 9 },
  { mois: 'Fév', ventes: 11 },
  { mois: 'Mar', ventes: 14 },
  { mois: 'Avr', ventes: 8 },
  { mois: 'Mai', ventes: 12 },
];

const topBiens = [
  { titre: 'Loft lumineux Lyon 6', vues: 2980 },
  { titre: 'Maison familiale Toulouse', vues: 2740 },
  { titre: 'Appartement T3 Nantes', vues: 2325 },
  { titre: 'Villa vue mer Nice', vues: 2258 },
  { titre: 'Studio centre Bordeaux', vues: 1980 },
];

const COLORS = ['#4f46e5', '#0ea5e9', '#f97316', '#10b981'];

const latestItems = {
  biens: ['Maison 5 pièces – Rennes', 'T2 meublé – Lille', 'Terrain 1200m² – Aix'],
  messages: ['Demande visite #1292', 'Question financement #1291', 'Relance vendeur #1290'],
  users: ['Camille R. (Agent)', 'Noah B. (Client)', 'Lina T. (Client)'],
  alertes: ['API CRM latence élevée', '3 tentatives de login refusées', 'Sauvegarde quotidienne terminée'],
};

function Card({ label, value }) {
  return (
    <article className="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
      <p className="text-xs uppercase tracking-wide text-slate-500">{label}</p>
      <p className="mt-2 text-2xl font-bold text-slate-900">{value}</p>
    </article>
  );
}

export default function AdminDashboardPage() {
  return (
    <AdminLayout title="Dashboard admin" subtitle="Pilotage temps réel de la plateforme immobilière">
      <section className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
        {kpis.map((item) => (
          <Card key={item.label} label={item.label} value={item.value} />
        ))}
      </section>

      <section className="mt-6 grid gap-4 xl:grid-cols-2">
        <article className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <h3 className="font-semibold">Courbe visites (30 derniers jours)</h3>
          <div className="mt-3 h-64">
            <ResponsiveContainer width="100%" height="100%">
              <LineChart data={visites30j}>
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="jour" />
                <YAxis />
                <Tooltip />
                <Line type="monotone" dataKey="visites" stroke="#4f46e5" strokeWidth={2} dot={false} />
              </LineChart>
            </ResponsiveContainer>
          </div>
        </article>

        <article className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <h3 className="font-semibold">Répartition biens par type</h3>
          <div className="mt-3 h-64">
            <ResponsiveContainer width="100%" height="100%">
              <PieChart>
                <Pie data={repartitionTypes} dataKey="value" nameKey="name" outerRadius={90}>
                  {repartitionTypes.map((entry, index) => (
                    <Cell key={entry.name} fill={COLORS[index % COLORS.length]} />
                  ))}
                </Pie>
                <Tooltip />
                <Legend />
              </PieChart>
            </ResponsiveContainer>
          </div>
        </article>

        <article className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <h3 className="font-semibold">Évolution inscriptions utilisateurs</h3>
          <div className="mt-3 h-64">
            <ResponsiveContainer width="100%" height="100%">
              <LineChart data={evolutionUsers}>
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="mois" />
                <YAxis />
                <Tooltip />
                <Line type="monotone" dataKey="users" stroke="#0ea5e9" strokeWidth={2} />
              </LineChart>
            </ResponsiveContainer>
          </div>
        </article>

        <article className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <h3 className="font-semibold">Biens vendus par mois</h3>
          <div className="mt-3 h-64">
            <ResponsiveContainer width="100%" height="100%">
              <BarChart data={ventesMensuelles}>
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="mois" />
                <YAxis />
                <Tooltip />
                <Bar dataKey="ventes" fill="#10b981" />
              </BarChart>
            </ResponsiveContainer>
          </div>
        </article>
      </section>

      <section className="mt-6 grid gap-4 lg:grid-cols-2">
        <article className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <h3 className="font-semibold">Biens les plus consultés (Top 5)</h3>
          <ul className="mt-3 space-y-2 text-sm">
            {topBiens.map((item) => (
              <li key={item.titre} className="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                <span>{item.titre}</span>
                <span className="font-semibold">{item.vues} vues</span>
              </li>
            ))}
          </ul>
        </article>

        <article className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <h3 className="font-semibold">Activité récente</h3>
          <div className="mt-3 grid gap-3 text-sm md:grid-cols-2">
            {Object.entries(latestItems).map(([section, values]) => (
              <div key={section}>
                <p className="mb-1 font-medium capitalize">{section}</p>
                <ul className="space-y-1 text-slate-600">
                  {values.map((value) => (
                    <li key={value}>• {value}</li>
                  ))}
                </ul>
              </div>
            ))}
          </div>
        </article>
      </section>
    </AdminLayout>
  );
}
