'use client';

import AdminLayout from '../../../../components/admin/AdminLayout';
import FormulaireAjoutBien from '../../../../components/admin/FormulaireAjoutBien';

export default function AdminNouveauBienPage() {
  return (
    <AdminLayout
      title="Ajouter un bien"
      subtitle="Formulaire complet : informations, caractéristiques, médias, options et publication"
    >
      <FormulaireAjoutBien mode="create" />
    </AdminLayout>
  );
}
