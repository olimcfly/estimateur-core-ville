'use client';

import { Funnel, FunnelChart, LabelList, ResponsiveContainer, Tooltip } from 'recharts';
import AdminLayout from '../../../components/admin/AdminLayout';

const sources = [
  { source: 'Direct', sessions: 14200 },
  { source: 'Google', sessions: 29600 },
  { source: 'Réseaux', sessions: 8100 },
];

const pagesTop = ['/bien/142', '/estimation', '/localite/paris', '/contact'];

const funnelData = [
  { value: 1000, name: 'Visites' },
  { value: 280, name: 'Contacts' },
  { value: 96, name: 'Rendez-vous' },
  { value: 42, name: 'Offres' },
  { value: 19, name: 'Ventes' },
];

export default function AdminStatistiquesPage() {
  return (
    <AdminLayout title="Statistiques avancées" subtitle="Trafic, engagement, conversion et reporting">
      <section className="grid gap-4 lg:grid-cols-2">
        <article className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <h3 className="font-semibold">Trafic par source</h3>
          <ul className="mt-3 space-y-2 text-sm">
            {sources.map((item) => (
              <li key={item.source} className="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                <span>{item.source}</span>
                <span className="font-semibold">{item.sessions.toLocaleString('fr-FR')} sessions</span>
              </li>
            ))}
          </ul>
          <p className="mt-4 text-sm text-slate-600">Taux de rebond: 41% · Durée moyenne session: 3 min 42 s</p>
        </article>

        <article className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <h3 className="font-semibold">Pages les plus visitées</h3>
          <ul className="mt-3 list-disc space-y-1 pl-5 text-sm text-slate-700">
            {pagesTop.map((page) => (
              <li key={page}>{page}</li>
            ))}
          </ul>
          <p className="mt-4 text-sm text-slate-600">Top biens consultés actualisé en temps réel côté dashboard.</p>
        </article>

        <article className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm lg:col-span-2">
          <h3 className="font-semibold">Entonnoir de conversion</h3>
          <div className="mt-3 h-72">
            <ResponsiveContainer width="100%" height="100%">
              <FunnelChart>
                <Tooltip />
                <Funnel dataKey="value" data={funnelData} isAnimationActive>
                  <LabelList position="right" fill="#475569" stroke="none" dataKey="name" />
                </Funnel>
              </FunnelChart>
            </ResponsiveContainer>
          </div>
          <div className="mt-3 flex flex-wrap gap-2">
            <button className="rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm">Envoyer rapport hebdo par email</button>
            <button className="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm">Exporter PDF mensuel</button>
          </div>
        </article>
      </section>
    </AdminLayout>
  );
}
