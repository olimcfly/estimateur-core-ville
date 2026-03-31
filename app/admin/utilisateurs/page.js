'use client';

import AdminLayout from '../../../components/admin/AdminLayout';

const users = [
  { id: 1, avatar: '👩‍💼', nom: 'Claire Dubois', email: 'claire@demo.fr', role: 'Admin', inscrit: '2025-11-03', statut: 'Actif', favoris: 14, alertes: 3 },
  { id: 2, avatar: '🧑‍💼', nom: 'Yanis Martin', email: 'yanis@demo.fr', role: 'Agent', inscrit: '2026-01-12', statut: 'Actif', favoris: 2, alertes: 1 },
  { id: 3, avatar: '👨', nom: 'Lucas Petit', email: 'lucas@demo.fr', role: 'Client', inscrit: '2026-02-20', statut: 'Suspendu', favoris: 9, alertes: 5 },
];

export default function AdminUtilisateursPage() {
  return (
    <AdminLayout title="Gestion des utilisateurs" subtitle="Rôles, statut, historique et fiches utilisateurs">
      <section className="space-y-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div className="grid gap-3 md:grid-cols-3">
          <select className="rounded-lg border border-slate-300 px-3 py-2 text-sm"><option>Tous rôles</option><option>Admin</option><option>Agent</option><option>Client</option></select>
          <select className="rounded-lg border border-slate-300 px-3 py-2 text-sm"><option>Tous statuts</option><option>Actif</option><option>Suspendu</option></select>
          <input type="date" className="rounded-lg border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <div className="overflow-x-auto">
          <table className="min-w-full text-left text-sm">
            <thead className="bg-slate-50 text-xs uppercase text-slate-600">
              <tr>
                <th className="px-3 py-2">Avatar</th><th className="px-3 py-2">Nom</th><th className="px-3 py-2">Email</th><th className="px-3 py-2">Rôle</th><th className="px-3 py-2">Inscrit le</th><th className="px-3 py-2">Statut</th><th className="px-3 py-2">Favoris / Alertes</th><th className="px-3 py-2">Actions</th>
              </tr>
            </thead>
            <tbody>
              {users.map((user) => (
                <tr key={user.id} className="border-t border-slate-100">
                  <td className="px-3 py-3 text-lg">{user.avatar}</td>
                  <td className="px-3 py-3 font-medium">{user.nom}</td>
                  <td className="px-3 py-3">{user.email}</td>
                  <td className="px-3 py-3">{user.role}</td>
                  <td className="px-3 py-3">{user.inscrit}</td>
                  <td className="px-3 py-3">{user.statut}</td>
                  <td className="px-3 py-3">{user.favoris} / {user.alertes}</td>
                  <td className="px-3 py-3">
                    <div className="flex flex-wrap gap-2">
                      <button className="rounded border border-slate-300 px-2 py-1 text-xs">Voir</button>
                      <button className="rounded border border-slate-300 px-2 py-1 text-xs">Éditer</button>
                      <button className="rounded border border-amber-200 px-2 py-1 text-xs">Suspendre</button>
                      <button className="rounded border border-rose-200 px-2 py-1 text-xs text-rose-600">Supprimer</button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        <article className="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
          <p className="font-medium text-slate-900">Fiche utilisateur (aperçu)</p>
          <p className="mt-1">Informations personnelles, historique d'activité, messages échangés, biens favoris, alertes actives, changement de rôle et suspension.</p>
        </article>
      </section>
    </AdminLayout>
  );
}
