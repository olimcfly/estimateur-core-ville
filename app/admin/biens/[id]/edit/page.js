'use client';

import AdminLayout from '../../../../../components/admin/AdminLayout';
import FormulaireAjoutBien from '../../../../../components/admin/FormulaireAjoutBien';

const initialValues = {
  titre: 'Appartement T4 rénové - Centre ville',
  type: 'Appartement',
  statut: 'Actif',
  prix: '420000',
  ville: 'Bordeaux',
  codePostal: '33000',
  description: 'Bien lumineux avec balcon, proche tram et commerces.',
  reference: 'REF-2026-0042',
  options: ['Balcon', 'Ascenseur', 'Interphone'],
};

export default function AdminEditBienPage({ params }) {
  return (
    <AdminLayout
      title={`Édition du bien ${params?.id ?? ''}`}
      subtitle="Mettez à jour toutes les données de l'annonce"
    >
      <FormulaireAjoutBien mode="edit" initialValues={initialValues} />
    </AdminLayout>
  );
}
