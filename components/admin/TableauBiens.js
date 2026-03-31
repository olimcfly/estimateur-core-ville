'use client';

import { useMemo, useState } from 'react';

const PAGE_SIZE = 20;

const currency = new Intl.NumberFormat('fr-FR', {
  style: 'currency',
  currency: 'EUR',
  maximumFractionDigits: 0,
});

const formatDate = (isoDate) => new Date(isoDate).toLocaleDateString('fr-FR');

export default function TableauBiens({ biens = [] }) {
  const [search, setSearch] = useState('');
  const [sortBy, setSortBy] = useState('dateAjout');
  const [sortDirection, setSortDirection] = useState('desc');
  const [typeFilter, setTypeFilter] = useState('Tous');
  const [statusFilter, setStatusFilter] = useState('Tous');
  const [cityFilter, setCityFilter] = useState('Toutes');
  const [priceFilter, setPriceFilter] = useState('Tous');
  const [page, setPage] = useState(1);
  const [selectedIds, setSelectedIds] = useState([]);
  const [toast, setToast] = useState('');
  const [confirmDeleteOpen, setConfirmDeleteOpen] = useState(false);

  const cities = useMemo(() => ['Toutes', ...new Set(biens.map((item) => item.ville))], [biens]);

  const filteredBiens = useMemo(() => {
    const normalizedSearch = search.trim().toLowerCase();

    return biens
      .filter((item) => {
        if (normalizedSearch) {
          const haystack = `${item.titre} ${item.ville} ${item.reference}`.toLowerCase();
          if (!haystack.includes(normalizedSearch)) return false;
        }

        if (typeFilter !== 'Tous' && item.type !== typeFilter) return false;
        if (statusFilter !== 'Tous' && item.statut !== statusFilter) return false;
        if (cityFilter !== 'Toutes' && item.ville !== cityFilter) return false;

        if (priceFilter === '<300k' && item.prix >= 300000) return false;
        if (priceFilter === '300k-700k' && (item.prix < 300000 || item.prix > 700000)) return false;
        if (priceFilter === '>700k' && item.prix <= 700000) return false;

        return true;
      })
      .sort((a, b) => {
        const order = sortDirection === 'asc' ? 1 : -1;
        if (sortBy === 'prix' || sortBy === 'vues') return (a[sortBy] - b[sortBy]) * order;
        return String(a[sortBy]).localeCompare(String(b[sortBy])) * order;
      });
  }, [biens, search, typeFilter, statusFilter, cityFilter, priceFilter, sortBy, sortDirection]);

  const totalPages = Math.max(1, Math.ceil(filteredBiens.length / PAGE_SIZE));
  const paginatedBiens = filteredBiens.slice((page - 1) * PAGE_SIZE, page * PAGE_SIZE);

  const toggleSort = (column) => {
    if (sortBy === column) {
      setSortDirection((prev) => (prev === 'asc' ? 'desc' : 'asc'));
      return;
    }
    setSortBy(column);
    setSortDirection('asc');
  };

  const toggleSelection = (id) => {
    setSelectedIds((prev) => (prev.includes(id) ? prev.filter((value) => value !== id) : [...prev, id]));
  };

  const selectAllCurrentPage = () => {
    const ids = paginatedBiens.map((item) => item.id);
    const allSelected = ids.every((id) => selectedIds.includes(id));

    if (allSelected) {
      setSelectedIds((prev) => prev.filter((id) => !ids.includes(id)));
      return;
    }

    setSelectedIds((prev) => [...new Set([...prev, ...ids])]);
  };

  const runBulkAction = (action) => {
    if (!selectedIds.length) return;
    if (action === 'Supprimer') {
      setConfirmDeleteOpen(true);
      return;
    }

    setToast(`${selectedIds.length} bien(s) mis à jour (${action}).`);
    setTimeout(() => setToast(''), 2500);
  };

  const exportCsv = () => {
    const header = ['Titre', 'Type', 'Ville', 'Prix', 'Statut', 'Vues', 'Date'];
    const rows = filteredBiens.map((item) => [
      item.titre,
      item.type,
      item.ville,
      item.prix,
      item.statut,
      item.vues,
      item.dateAjout,
    ]);

    const csv = [header, ...rows].map((line) => line.join(';')).join('\n');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'biens-export.csv';
    link.click();
    URL.revokeObjectURL(url);
  };

  return (
    <section className="space-y-4">
      <div className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div className="grid gap-3 md:grid-cols-2 lg:grid-cols-5">
          <input
            value={search}
            onChange={(event) => {
              setSearch(event.target.value);
              setPage(1);
            }}
            placeholder="Recherche en temps réel"
            className="rounded-lg border border-slate-300 px-3 py-2 text-sm"
          />

          <select value={typeFilter} onChange={(e) => setTypeFilter(e.target.value)} className="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option>Tous</option>
            <option>Appartement</option>
            <option>Maison</option>
            <option>Terrain</option>
            <option>Local</option>
          </select>

          <select value={statusFilter} onChange={(e) => setStatusFilter(e.target.value)} className="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option>Tous</option>
            <option>Actif</option>
            <option>Vendu</option>
            <option>Archivé</option>
            <option>Sous compromis</option>
          </select>

          <select value={cityFilter} onChange={(e) => setCityFilter(e.target.value)} className="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            {cities.map((city) => (
              <option key={city}>{city}</option>
            ))}
          </select>

          <select value={priceFilter} onChange={(e) => setPriceFilter(e.target.value)} className="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="Tous">Tous les prix</option>
            <option value="<300k">&lt; 300 000 €</option>
            <option value="300k-700k">300 000 - 700 000 €</option>
            <option value=">700k">&gt; 700 000 €</option>
          </select>
        </div>

        <div className="mt-3 flex flex-wrap gap-2">
          <button onClick={selectAllCurrentPage} type="button" className="rounded-lg border border-slate-300 px-3 py-2 text-sm">Sélectionner page</button>
          <button onClick={() => runBulkAction('Activer')} type="button" className="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm">Bulk Activer</button>
          <button onClick={() => runBulkAction('Désactiver')} type="button" className="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm">Bulk Désactiver</button>
          <button onClick={() => runBulkAction('Supprimer')} type="button" className="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm">Bulk Supprimer</button>
          <button onClick={exportCsv} type="button" className="rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm">Exporter CSV</button>
        </div>
      </div>

      <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div className="overflow-x-auto">
          <table className="min-w-full text-left text-sm">
            <thead className="bg-slate-50 text-xs uppercase text-slate-600">
              <tr>
                <th className="px-3 py-3">Sel.</th>
                <th className="px-3 py-3">Photo</th>
                <th onClick={() => toggleSort('titre')} className="cursor-pointer px-3 py-3">Titre</th>
                <th onClick={() => toggleSort('type')} className="cursor-pointer px-3 py-3">Type</th>
                <th onClick={() => toggleSort('ville')} className="cursor-pointer px-3 py-3">Ville</th>
                <th onClick={() => toggleSort('prix')} className="cursor-pointer px-3 py-3">Prix</th>
                <th onClick={() => toggleSort('statut')} className="cursor-pointer px-3 py-3">Statut</th>
                <th onClick={() => toggleSort('vues')} className="cursor-pointer px-3 py-3">Vues</th>
                <th onClick={() => toggleSort('dateAjout')} className="cursor-pointer px-3 py-3">Date</th>
                <th className="px-3 py-3">Actions</th>
              </tr>
            </thead>
            <tbody>
              {paginatedBiens.map((item) => (
                <tr key={item.id} className="border-t border-slate-100 hover:bg-slate-50">
                  <td className="px-3 py-3">
                    <input type="checkbox" checked={selectedIds.includes(item.id)} onChange={() => toggleSelection(item.id)} />
                  </td>
                  <td className="px-3 py-3">
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img src={item.photo} alt={item.titre} className="h-12 w-16 rounded object-cover" />
                  </td>
                  <td className="px-3 py-3 font-medium text-slate-900">{item.titre}</td>
                  <td className="px-3 py-3">{item.type}</td>
                  <td className="px-3 py-3">{item.ville}</td>
                  <td className="px-3 py-3">{currency.format(item.prix)}</td>
                  <td className="px-3 py-3">{item.statut}</td>
                  <td className="px-3 py-3">{item.vues}</td>
                  <td className="px-3 py-3">{formatDate(item.dateAjout)}</td>
                  <td className="px-3 py-3">
                    <div className="flex gap-2">
                      <button type="button" className="rounded border border-slate-300 px-2 py-1 text-xs">Voir</button>
                      <button type="button" className="rounded border border-slate-300 px-2 py-1 text-xs">Éditer</button>
                      <button type="button" className="rounded border border-rose-200 px-2 py-1 text-xs text-rose-600">Supprimer</button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        <div className="flex items-center justify-between border-t border-slate-200 px-4 py-3 text-sm">
          <p>
            {filteredBiens.length} résultat(s) · page {page}/{totalPages}
          </p>
          <div className="flex gap-2">
            <button
              type="button"
              disabled={page <= 1}
              onClick={() => setPage((prev) => Math.max(1, prev - 1))}
              className="rounded border border-slate-300 px-3 py-1 disabled:opacity-40"
            >
              Précédent
            </button>
            <button
              type="button"
              disabled={page >= totalPages}
              onClick={() => setPage((prev) => Math.min(totalPages, prev + 1))}
              className="rounded border border-slate-300 px-3 py-1 disabled:opacity-40"
            >
              Suivant
            </button>
          </div>
        </div>
      </div>

      {toast ? (
        <div className="fixed bottom-5 right-5 rounded-lg bg-slate-900 px-4 py-2 text-sm text-white shadow-lg">{toast}</div>
      ) : null}

      {confirmDeleteOpen ? (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4">
          <div className="w-full max-w-md rounded-xl bg-white p-5">
            <h3 className="text-lg font-semibold">Confirmer la suppression</h3>
            <p className="mt-2 text-sm text-slate-600">Supprimer {selectedIds.length} bien(s) sélectionné(s) ?</p>
            <div className="mt-4 flex justify-end gap-2">
              <button type="button" onClick={() => setConfirmDeleteOpen(false)} className="rounded border border-slate-300 px-3 py-2 text-sm">
                Annuler
              </button>
              <button
                type="button"
                onClick={() => {
                  setConfirmDeleteOpen(false);
                  setSelectedIds([]);
                  setToast('Suppression effectuée.');
                  setTimeout(() => setToast(''), 2500);
                }}
                className="rounded bg-rose-600 px-3 py-2 text-sm text-white"
              >
                Confirmer
              </button>
            </div>
          </div>
        </div>
      ) : null}
    </section>
  );
}
