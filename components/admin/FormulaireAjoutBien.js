'use client';

import { useMemo, useState } from 'react';

const TABS = [
  'Informations',
  'Caractéristiques',
  'Localisation',
  'Photos/Médias',
  'Options',
  'Publication',
];

const OPTIONS = [
  'Parking',
  'Garage',
  'Cave',
  'Balcon',
  'Terrasse',
  'Jardin',
  'Piscine',
  'Ascenseur',
  'Gardien',
  'Interphone',
  'Digicode',
  'Vue mer',
  'Vue montagne',
  'Vue dégagée',
  'Meublé',
  'Non meublé',
];

const inputClass = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm';

export default function FormulaireAjoutBien({ mode = 'create', initialValues = {} }) {
  const [activeTab, setActiveTab] = useState(0);
  const [toast, setToast] = useState('');
  const [data, setData] = useState({
    titre: '',
    type: 'Appartement',
    statut: 'Actif',
    description: '',
    prix: '',
    prixNegociable: false,
    reference: '',
    surfaceHabitable: '',
    surfaceTerrain: '',
    pieces: '',
    chambres: '',
    sdb: '',
    etage: '',
    etagesTotal: '',
    anneeConstruction: '',
    etat: 'Bon état',
    dpe: 'C',
    ges: 'C',
    chauffageType: '',
    chauffageEnergie: '',
    adresse: '',
    codePostal: '',
    ville: '',
    departement: '',
    lat: '',
    lng: '',
    quartier: '',
    secteur: '',
    visiteVirtuelle: '',
    videoYoutube: '',
    publicationStatus: 'Brouillon',
    dateMiseEnLigne: '',
    boost: false,
    partageSocial: false,
    ...initialValues,
  });

  const [selectedOptions, setSelectedOptions] = useState(initialValues.options ?? []);
  const [photos, setPhotos] = useState(initialValues.photos ?? []);

  const formTitle = useMemo(() => (mode === 'edit' ? 'Éditer le bien' : 'Ajouter un bien'), [mode]);

  const handleChange = (event) => {
    const { name, value, type, checked } = event.target;
    setData((prev) => ({ ...prev, [name]: type === 'checkbox' ? checked : value }));
  };

  const handleFiles = (filesList) => {
    const files = Array.from(filesList || []).slice(0, 20 - photos.length);
    const enriched = files.map((file, index) => ({
      id: `${file.name}-${Date.now()}-${index}`,
      name: file.name,
      size: `${Math.round(file.size / 1024)} KB`,
      principal: photos.length === 0 && index === 0,
    }));
    setPhotos((prev) => [...prev, ...enriched].slice(0, 20));
  };

  const toggleOption = (option) => {
    setSelectedOptions((prev) =>
      prev.includes(option) ? prev.filter((value) => value !== option) : [...prev, option]
    );
  };

  const setMainPhoto = (id) => {
    setPhotos((prev) => prev.map((photo) => ({ ...photo, principal: photo.id === id })));
  };

  return (
    <section className="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <h3 className="text-xl font-semibold">{formTitle}</h3>

      <div className="flex flex-wrap gap-2 border-b border-slate-200 pb-3">
        {TABS.map((tab, index) => (
          <button
            key={tab}
            type="button"
            onClick={() => setActiveTab(index)}
            className={`rounded-lg px-3 py-2 text-sm ${
              activeTab === index ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700'
            }`}
          >
            {tab}
          </button>
        ))}
      </div>

      {activeTab === 0 ? (
        <div className="grid gap-3 md:grid-cols-2">
          <input name="titre" value={data.titre} onChange={handleChange} placeholder="Titre de l'annonce" className={`${inputClass} md:col-span-2`} />
          <select name="type" value={data.type} onChange={handleChange} className={inputClass}>
            <option>Appartement</option><option>Maison</option><option>Terrain</option><option>Local</option>
          </select>
          <select name="statut" value={data.statut} onChange={handleChange} className={inputClass}>
            <option>Actif</option><option>Vendu</option><option>Archivé</option><option>Sous compromis</option>
          </select>
          <textarea name="description" value={data.description} onChange={handleChange} placeholder="Description (éditeur rich text simplifié)" rows={5} className={`${inputClass} md:col-span-2`} />
          <input name="prix" value={data.prix} onChange={handleChange} placeholder="Prix (€)" className={inputClass} />
          <input name="reference" value={data.reference} onChange={handleChange} placeholder="Référence interne" className={inputClass} />
          <label className="md:col-span-2 flex items-center gap-2 text-sm"><input type="checkbox" name="prixNegociable" checked={data.prixNegociable} onChange={handleChange} /> Prix négociable</label>
        </div>
      ) : null}

      {activeTab === 1 ? (
        <div className="grid gap-3 md:grid-cols-3">
          <input name="surfaceHabitable" value={data.surfaceHabitable} onChange={handleChange} placeholder="Surface habitable" className={inputClass} />
          <input name="surfaceTerrain" value={data.surfaceTerrain} onChange={handleChange} placeholder="Surface terrain" className={inputClass} />
          <input name="pieces" value={data.pieces} onChange={handleChange} placeholder="Pièces" className={inputClass} />
          <input name="chambres" value={data.chambres} onChange={handleChange} placeholder="Chambres" className={inputClass} />
          <input name="sdb" value={data.sdb} onChange={handleChange} placeholder="SDB" className={inputClass} />
          <input name="etage" value={data.etage} onChange={handleChange} placeholder="Étage" className={inputClass} />
          <input name="etagesTotal" value={data.etagesTotal} onChange={handleChange} placeholder="Nb étages" className={inputClass} />
          <input name="anneeConstruction" value={data.anneeConstruction} onChange={handleChange} placeholder="Année construction" className={inputClass} />
          <select name="etat" value={data.etat} onChange={handleChange} className={inputClass}><option>Neuf</option><option>Bon état</option><option>À rénover</option></select>
          <select name="dpe" value={data.dpe} onChange={handleChange} className={inputClass}><option>A</option><option>B</option><option>C</option><option>D</option><option>E</option><option>F</option><option>G</option></select>
          <select name="ges" value={data.ges} onChange={handleChange} className={inputClass}><option>A</option><option>B</option><option>C</option><option>D</option><option>E</option><option>F</option><option>G</option></select>
          <input name="chauffageType" value={data.chauffageType} onChange={handleChange} placeholder="Chauffage type" className={inputClass} />
          <input name="chauffageEnergie" value={data.chauffageEnergie} onChange={handleChange} placeholder="Chauffage énergie" className={inputClass} />
        </div>
      ) : null}

      {activeTab === 2 ? (
        <div className="grid gap-3 md:grid-cols-2">
          <input name="adresse" value={data.adresse} onChange={handleChange} placeholder="Adresse complète" className={`${inputClass} md:col-span-2`} />
          <input name="codePostal" value={data.codePostal} onChange={handleChange} placeholder="Code postal" className={inputClass} />
          <input name="ville" value={data.ville} onChange={handleChange} placeholder="Ville" className={inputClass} />
          <input name="departement" value={data.departement} onChange={handleChange} placeholder="Département" className={inputClass} />
          <input name="quartier" value={data.quartier} onChange={handleChange} placeholder="Quartier" className={inputClass} />
          <input name="secteur" value={data.secteur} onChange={handleChange} placeholder="Secteur" className={inputClass} />
          <input name="lat" value={data.lat} onChange={handleChange} placeholder="Latitude" className={inputClass} />
          <input name="lng" value={data.lng} onChange={handleChange} placeholder="Longitude" className={inputClass} />
          <div className="md:col-span-2 rounded-xl border border-dashed border-slate-300 p-5 text-sm text-slate-600">
            Aperçu carte Leaflet (placeholder) - Coordonnées: {data.lat || 'N/A'}, {data.lng || 'N/A'}
          </div>
        </div>
      ) : null}

      {activeTab === 3 ? (
        <div className="space-y-4">
          <label className="block rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-600">
            <input
              type="file"
              accept="image/png,image/jpeg,image/webp"
              multiple
              className="hidden"
              onChange={(event) => handleFiles(event.target.files)}
            />
            Drag & drop / cliquez pour upload (max 20 JPG/PNG/WebP, compression auto côté backend)
          </label>

          <div className="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
            {photos.map((photo) => (
              <div key={photo.id} className="rounded-lg border border-slate-200 p-3">
                <p className="truncate text-sm font-medium">{photo.name}</p>
                <p className="text-xs text-slate-500">{photo.size}</p>
                <div className="mt-2 flex gap-2">
                  <button type="button" onClick={() => setMainPhoto(photo.id)} className="rounded border border-amber-300 px-2 py-1 text-xs">⭐ Principale</button>
                  <button type="button" className="rounded border border-slate-300 px-2 py-1 text-xs">Recadrer</button>
                </div>
                {photo.principal ? <p className="mt-2 text-xs font-semibold text-amber-600">Photo principale</p> : null}
              </div>
            ))}
          </div>

          <input name="visiteVirtuelle" value={data.visiteVirtuelle} onChange={handleChange} placeholder="URL visite virtuelle Matterport" className={inputClass} />
          <input name="videoYoutube" value={data.videoYoutube} onChange={handleChange} placeholder="URL vidéo YouTube" className={inputClass} />
        </div>
      ) : null}

      {activeTab === 4 ? (
        <div className="grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
          {OPTIONS.map((option) => (
            <label key={option} className="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm">
              <input type="checkbox" checked={selectedOptions.includes(option)} onChange={() => toggleOption(option)} />
              {option}
            </label>
          ))}
        </div>
      ) : null}

      {activeTab === 5 ? (
        <div className="grid gap-3 md:grid-cols-2">
          <select name="publicationStatus" value={data.publicationStatus} onChange={handleChange} className={inputClass}>
            <option>Brouillon</option>
            <option>Publié</option>
            <option>Programmé</option>
            <option>Archivé</option>
          </select>
          <input type="datetime-local" name="dateMiseEnLigne" value={data.dateMiseEnLigne} onChange={handleChange} className={inputClass} />
          <label className="flex items-center gap-2 text-sm"><input type="checkbox" name="boost" checked={data.boost} onChange={handleChange} /> Mise en avant (boost)</label>
          <label className="flex items-center gap-2 text-sm"><input type="checkbox" name="partageSocial" checked={data.partageSocial} onChange={handleChange} /> Partage réseaux sociaux</label>
          <div className="md:col-span-2 rounded-xl border border-slate-200 p-4 text-sm text-slate-600">
            Prévisualisation annonce: {data.titre || 'Titre de l\'annonce'} · {data.ville || 'Ville'} · {data.prix || 'Prix'} €
          </div>
        </div>
      ) : null}

      <div className="flex flex-wrap justify-end gap-2 border-t border-slate-200 pt-4">
        <button type="button" className="rounded-lg border border-slate-300 px-4 py-2 text-sm">Brouillon</button>
        <button
          type="button"
          onClick={() => {
            setToast(mode === 'edit' ? 'Bien mis à jour.' : 'Bien ajouté avec succès.');
            setTimeout(() => setToast(''), 2500);
          }}
          className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white"
        >
          {mode === 'edit' ? 'Enregistrer' : 'Publier'}
        </button>
      </div>

      {toast ? <div className="fixed bottom-5 right-5 rounded-lg bg-slate-900 px-4 py-2 text-sm text-white">{toast}</div> : null}
    </section>
  );
}
