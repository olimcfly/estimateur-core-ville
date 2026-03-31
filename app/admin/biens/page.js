'use client';

import Link from 'next/link';
import AdminLayout from '../../../components/admin/AdminLayout';
import TableauBiens from '../../../components/admin/TableauBiens';

const types = ['Appartement', 'Maison', 'Terrain', 'Local'];
const statuts = ['Actif', 'Vendu', 'Archivé', 'Sous compromis'];
const villes = ['Paris', 'Lyon', 'Marseille', 'Bordeaux', 'Nantes', 'Lille'];

const biens = Array.from({ length: 36 }, (_, index) => ({
  id: `bien-${index + 1}`,
  photo: `https://picsum.photos/seed/bien-${index + 1}/400/260`,
  titre: `Bien premium #${index + 1}`,
  type: types[index % types.length],
  ville: villes[index % villes.length],
  prix: 180000 + index * 18000,
  statut: statuts[index % statuts.length],
  vues: 120 + index * 17,
  dateAjout: `2026-03-${String((index % 28) + 1).padStart(2, '0')}`,
  reference: `REF-${1000 + index}`,
}));

export default function AdminBiensPage() {
  return (
    <AdminLayout
      title="Gestion des biens"
      subtitle="Recherche, tri, filtres, actions groupées et export CSV"
      actions={
        <Link href="/admin/biens/nouveau" className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white">
          + Nouveau bien
        </Link>
      }
    >
      <TableauBiens biens={biens} />
    </AdminLayout>
  );
}
