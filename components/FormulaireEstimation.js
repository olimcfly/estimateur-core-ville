'use client';

import { useMemo, useState } from 'react';
import ResultatEstimation from './ResultatEstimation';

const STEPS = [
  'Type de bien',
  'Localisation',
  'Caractéristiques',
  'Options',
  'Résultat',
];

const TYPES_BIENS = [
  { key: 'Appartement', image: 'https://images.unsplash.com/photo-1460317442991-0ec209397118?auto=format&fit=crop&w=800&q=60' },
  { key: 'Maison', image: 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=800&q=60' },
  { key: 'Terrain', image: 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=800&q=60' },
  { key: 'Local', image: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=60' },
];

const PRIX_M2_PAR_VILLE = {
  Paris: 10500,
  Lyon: 5400,
  Marseille: 3900,
  Bordeaux: 5000,
  Nantes: 4300,
  Lille: 3700,
  Toulouse: 4200,
  Nice: 6100,
  Montpellier: 3900,
  Strasbourg: 3600,
};

const ETAT_COEF = {
  'À rénover': 0.85,
  'Bon état': 1,
  Excellent: 1.1,
};

const ETAGE_COEF = {
  0: 0.98,
  1: 1,
  2: 1.01,
  3: 1.02,
  4: 1.03,
};

const LOCALISATIONS_FICTIVES = [
  '12 rue de la République, Lyon',
  '48 avenue Victor Hugo, Paris',
  '5 quai des Chartrons, Bordeaux',
  '23 boulevard Longchamp, Marseille',
];

const defaultForm = {
  typeBien: '',
  adresse: '',
  codePostal: '',
  ville: '',
  quartier: '',
  surface: 65,
  pieces: 3,
  chambres: 2,
  etage: 1,
  anneeConstruction: 2005,
  etatGeneral: 'Bon état',
  parking: false,
  balcon: false,
  surfaceBalcon: 0,
  cave: false,
  ascenseur: false,
  vueDegagee: false,
  dpe: 'C',
};

const getVilleBasePrice = (ville) => PRIX_M2_PAR_VILLE[ville] ?? 3200;

const getHistorique = (prixM2Actuel) => {
  const mois = ['Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc', 'Jan', 'Fév', 'Mar', 'Avr'];
  return mois.map((m, i) => ({
    mois: m,
    prixM2: prixM2Actuel * (0.95 + i * 0.008),
  }));
};

export default function FormulaireEstimation() {
  const [step, setStep] = useState(0);
  const [form, setForm] = useState(defaultForm);
  const [errors, setErrors] = useState({});
  const [isLoading, setIsLoading] = useState(false);
  const [resultat, setResultat] = useState(null);

  const progression = ((step + 1) / STEPS.length) * 100;

  const suggestions = useMemo(() => {
    if (!form.adresse?.trim()) return LOCALISATIONS_FICTIVES;
    return LOCALISATIONS_FICTIVES.filter((option) =>
      option.toLowerCase().includes(form.adresse.toLowerCase())
    );
  }, [form.adresse]);

  const updateForm = (field, value) => {
    setForm((prev) => ({ ...prev, [field]: value }));
  };

  const validateStep = () => {
    const nextErrors = {};

    if (step === 0 && !form.typeBien) nextErrors.typeBien = 'Sélectionnez un type de bien.';

    if (step === 1) {
      if (!form.adresse.trim()) nextErrors.adresse = 'Adresse requise.';
      if (!form.ville.trim()) nextErrors.ville = 'Ville requise.';
      if (!form.codePostal.trim()) nextErrors.codePostal = 'Code postal requis.';
    }

    if (step === 2) {
      if (!form.surface || Number(form.surface) < 10) nextErrors.surface = 'Surface minimum 10 m².';
      if (!form.pieces) nextErrors.pieces = 'Nombre de pièces requis.';
      if (!form.chambres && Number(form.chambres) !== 0) nextErrors.chambres = 'Nombre de chambres requis.';
    }

    setErrors(nextErrors);
    return Object.keys(nextErrors).length === 0;
  };

  const computeEstimation = () => {
    const prixBaseM2 = getVilleBasePrice(form.ville);
    const coefEtat = ETAT_COEF[form.etatGeneral] ?? 1;
    const coefEtage = form.typeBien === 'Appartement' ? ETAGE_COEF[form.etage] ?? 1.04 : 1;

    let coefOptions = 1;
    if (form.parking) coefOptions += 0.05;
    if (form.balcon) coefOptions += 0.03;
    if (form.cave) coefOptions += 0.02;
    if (form.ascenseur) coefOptions += 0.02;
    if (form.vueDegagee) coefOptions += 0.03;

    const prixM2Estime = prixBaseM2 * coefEtat * coefEtage * coefOptions;
    const prixEstime = prixM2Estime * Number(form.surface);

    const variationQuartier = Number(((coefOptions - 1) * 100 + (coefEtat - 1) * 100).toFixed(1));

    return {
      prixBas: Math.round(prixEstime * 0.95),
      prixHaut: Math.round(prixEstime * 1.05),
      prixM2Estime: Math.round(prixM2Estime),
      comparaisonLocale:
        prixM2Estime > prixBaseM2
          ? `+${Math.round(((prixM2Estime - prixBaseM2) / prixBaseM2) * 100)}% au-dessus de la moyenne de ${form.ville}`
          : `${Math.round(((prixBaseM2 - prixM2Estime) / prixBaseM2) * 100)}% sous la moyenne de ${form.ville}`,
      variationQuartier,
      historiquePrix: getHistorique(prixM2Estime),
    };
  };

  const goNext = async () => {
    if (!validateStep()) return;

    if (step === STEPS.length - 2) {
      setIsLoading(true);
      await new Promise((resolve) => setTimeout(resolve, 950));
      setResultat(computeEstimation());
      setIsLoading(false);
    }

    setStep((prev) => Math.min(prev + 1, STEPS.length - 1));
  };

  const goPrev = () => {
    setStep((prev) => Math.max(prev - 1, 0));
  };

  return (
    <main className="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
      <header className="mb-6 space-y-3">
        <p className="text-sm font-semibold uppercase tracking-wide text-indigo-600">Estimation en ligne</p>
        <h1 className="text-3xl font-bold text-slate-900">Estimez votre bien en 5 étapes</h1>
        <div className="h-2 overflow-hidden rounded-full bg-slate-200">
          <div className="h-full rounded-full bg-indigo-600 transition-all duration-300" style={{ width: `${progression}%` }} />
        </div>
        <p className="text-sm text-slate-600">
          Étape {step + 1} / {STEPS.length} — {STEPS[step]}
        </p>
      </header>

      <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        {step === 0 && (
          <div className="space-y-4">
            <h2 className="text-xl font-semibold text-slate-900">Quel est le type de votre bien ?</h2>
            <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
              {TYPES_BIENS.map((type) => (
                <button
                  key={type.key}
                  type="button"
                  onClick={() => updateForm('typeBien', type.key)}
                  className={`overflow-hidden rounded-xl border text-left transition ${
                    form.typeBien === type.key
                      ? 'border-indigo-500 ring-2 ring-indigo-100'
                      : 'border-slate-200 hover:border-slate-300'
                  }`}
                >
                  <img src={type.image} alt={type.key} className="h-36 w-full object-cover" />
                  <p className="p-3 font-medium text-slate-900">{type.key}</p>
                </button>
              ))}
            </div>
            {errors.typeBien && <p className="text-sm text-red-600">{errors.typeBien}</p>}
          </div>
        )}

        {step === 1 && (
          <div className="grid gap-4 sm:grid-cols-2">
            <label className="block text-sm font-medium text-slate-700 sm:col-span-2">
              Adresse complète (autocomplete)
              <input
                value={form.adresse}
                onChange={(e) => updateForm('adresse', e.target.value)}
                className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"
                placeholder="Ex : 18 rue Nationale"
              />
              <div className="mt-2 rounded-lg border border-slate-200 bg-slate-50 p-2 text-xs text-slate-600">
                Suggestions (mock Google Maps) : {suggestions.join(' • ')}
              </div>
              {errors.adresse && <span className="text-red-600">{errors.adresse}</span>}
            </label>

            <label className="block text-sm font-medium text-slate-700">
              Code postal
              <input
                value={form.codePostal}
                onChange={(e) => updateForm('codePostal', e.target.value)}
                className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"
              />
              {errors.codePostal && <span className="text-red-600">{errors.codePostal}</span>}
            </label>

            <label className="block text-sm font-medium text-slate-700">
              Ville
              <input
                value={form.ville}
                onChange={(e) => updateForm('ville', e.target.value)}
                className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"
              />
              {errors.ville && <span className="text-red-600">{errors.ville}</span>}
            </label>

            <label className="block text-sm font-medium text-slate-700 sm:col-span-2">
              Quartier
              <input
                value={form.quartier}
                onChange={(e) => updateForm('quartier', e.target.value)}
                className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"
                placeholder="Ex : Chartrons"
              />
            </label>
          </div>
        )}

        {step === 2 && (
          <div className="grid gap-4 sm:grid-cols-2">
            {[
              ['surface', 'Surface habitable (m²)', 'number'],
              ['pieces', 'Nombre de pièces', 'number'],
              ['chambres', 'Nombre de chambres', 'number'],
              ['anneeConstruction', 'Année de construction', 'number'],
            ].map(([field, label, type]) => (
              <label key={field} className="block text-sm font-medium text-slate-700">
                {label}
                <input
                  type={type}
                  value={form[field]}
                  onChange={(e) => updateForm(field, Number(e.target.value))}
                  className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"
                />
                {errors[field] && <span className="text-red-600">{errors[field]}</span>}
              </label>
            ))}

            {form.typeBien === 'Appartement' && (
              <label className="block text-sm font-medium text-slate-700">
                Étage
                <input
                  type="number"
                  min={0}
                  max={8}
                  value={form.etage}
                  onChange={(e) => updateForm('etage', Number(e.target.value))}
                  className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"
                />
              </label>
            )}

            <label className="block text-sm font-medium text-slate-700">
              État général
              <select
                value={form.etatGeneral}
                onChange={(e) => updateForm('etatGeneral', e.target.value)}
                className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"
              >
                {Object.keys(ETAT_COEF).map((etat) => (
                  <option key={etat} value={etat}>
                    {etat}
                  </option>
                ))}
              </select>
            </label>
          </div>
        )}

        {step === 3 && (
          <div className="grid gap-4 sm:grid-cols-2">
            {[
              ['parking', 'Parking / Garage'],
              ['cave', 'Cave / Cellier'],
              ['ascenseur', 'Ascenseur'],
              ['vueDegagee', 'Vue dégagée'],
            ].map(([field, label]) => (
              <label key={field} className="flex items-center gap-2 rounded-lg border border-slate-200 p-3 text-sm text-slate-700">
                <input
                  type="checkbox"
                  checked={form[field]}
                  onChange={(e) => updateForm(field, e.target.checked)}
                  className="h-4 w-4 rounded"
                />
                {label}
              </label>
            ))}

            <label className="flex items-center gap-2 rounded-lg border border-slate-200 p-3 text-sm text-slate-700">
              <input
                type="checkbox"
                checked={form.balcon}
                onChange={(e) => updateForm('balcon', e.target.checked)}
                className="h-4 w-4 rounded"
              />
              Balcon / Terrasse
            </label>

            {form.balcon && (
              <label className="block text-sm font-medium text-slate-700">
                Surface balcon/terrasse (m²)
                <input
                  type="number"
                  min={0}
                  value={form.surfaceBalcon}
                  onChange={(e) => updateForm('surfaceBalcon', Number(e.target.value))}
                  className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"
                />
              </label>
            )}

            <label className="block text-sm font-medium text-slate-700 sm:col-span-2">
              DPE actuel
              <select
                value={form.dpe}
                onChange={(e) => updateForm('dpe', e.target.value)}
                className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"
              >
                {['A', 'B', 'C', 'D', 'E', 'F', 'G'].map((dpe) => (
                  <option key={dpe} value={dpe}>
                    {dpe}
                  </option>
                ))}
              </select>
            </label>
          </div>
        )}

        {step === 4 && <ResultatEstimation estimation={resultat} isLoading={isLoading} />}

        <footer className="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-5">
          <button
            type="button"
            onClick={goPrev}
            disabled={step === 0}
            className="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 disabled:opacity-40"
          >
            Précédent
          </button>

          <button
            type="button"
            onClick={goNext}
            disabled={step === STEPS.length - 1}
            className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-40"
          >
            {step === STEPS.length - 2 ? 'Calculer mon estimation' : 'Suivant'}
          </button>
        </footer>
      </section>

      <section id="contact-expert" className="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 className="text-xl font-semibold text-slate-900">Être contacté par un expert</h2>
        <form className="mt-4 grid gap-3 sm:grid-cols-2">
          <input className="rounded-lg border border-slate-300 px-3 py-2" placeholder="Nom" />
          <input className="rounded-lg border border-slate-300 px-3 py-2" placeholder="Téléphone" />
          <input className="rounded-lg border border-slate-300 px-3 py-2 sm:col-span-2" placeholder="Email" />
          <textarea
            className="rounded-lg border border-slate-300 px-3 py-2 sm:col-span-2"
            rows={4}
            placeholder="Précisez votre projet"
          />
          <button className="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white sm:col-span-2">
            Envoyer ma demande
          </button>
        </form>
      </section>
    </main>
  );
}
