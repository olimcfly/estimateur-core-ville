'use client';

import AdminLayout from '../../../components/admin/AdminLayout';

const messages = [
  { id: '#1402', sujet: 'Visite villa Nice', expediteur: 'Emma L.', statut: 'Nouveau', priorite: 'Urgent', assigne: 'Agent Hugo' },
  { id: '#1401', sujet: 'Demande estimation', expediteur: 'M. Durand', statut: 'En cours', priorite: 'Normal', assigne: 'Agent Sarah' },
  { id: '#1400', sujet: 'Question compromis', expediteur: 'Nina C.', statut: 'Traité', priorite: 'Normal', assigne: 'Agent Hugo' },
];

export default function AdminMessagesPage() {
  return (
    <AdminLayout title="Messages reçus" subtitle="Inbox centralisée avec filtres, assignation et notes internes">
      <section className="space-y-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div className="grid gap-3 md:grid-cols-4">
          <select className="rounded-lg border border-slate-300 px-3 py-2 text-sm"><option>Tous</option><option>Lu</option><option>Non lu</option><option>Archivé</option><option>Urgent</option></select>
          <select className="rounded-lg border border-slate-300 px-3 py-2 text-sm"><option>Nouveau</option><option>En cours</option><option>Traité</option></select>
          <select className="rounded-lg border border-slate-300 px-3 py-2 text-sm"><option>Assigner à...</option><option>Agent Hugo</option><option>Agent Sarah</option></select>
          <button className="rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm">Templates réponse rapide</button>
        </div>

        {messages.map((message) => (
          <article key={message.id} className="rounded-xl border border-slate-200 p-4">
            <div className="flex flex-wrap items-center justify-between gap-2">
              <h3 className="font-semibold">{message.id} · {message.sujet}</h3>
              <span className="rounded bg-slate-100 px-2 py-1 text-xs">{message.priorite}</span>
            </div>
            <p className="mt-1 text-sm text-slate-600">Expéditeur: {message.expediteur} · Statut: {message.statut} · Assigné: {message.assigne}</p>
            <div className="mt-3 flex flex-wrap gap-2">
              <button className="rounded border border-slate-300 px-2 py-1 text-xs">Répondre</button>
              <button className="rounded border border-slate-300 px-2 py-1 text-xs">Assigner</button>
              <button className="rounded border border-slate-300 px-2 py-1 text-xs">Archiver</button>
            </div>
            <textarea className="mt-3 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" rows={2} placeholder="Notes internes..." />
          </article>
        ))}
      </section>
    </AdminLayout>
  );
}
