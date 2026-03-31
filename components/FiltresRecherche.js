'use client';

const TYPES_BIENS = ['Appartement', 'Maison', 'Terrain', 'Local'];
const PIECES = ['1', '2', '3', '4', '5+'];
const DPE_NOTES = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
const STATUTS = ['À vendre', 'Sous compromis'];

function Label({ children }) {
  return <label className="mb-1 block text-sm font-medium text-slate-700">{children}</label>;
}

function RangeField({ label, min, max, valueMin, valueMax, step = 10, onMinChange, onMaxChange }) {
  return (
    <div className="space-y-2">
      <Label>{label}</Label>
      <div className="grid grid-cols-2 gap-2 text-xs font-medium text-slate-600">
        <span className="rounded-lg bg-slate-100 px-2 py-1">Min : {valueMin}</span>
        <span className="rounded-lg bg-slate-100 px-2 py-1 text-right">Max : {valueMax}</span>
      </div>
      <div className="space-y-1">
        <input
          type="range"
          min={min}
          max={max}
          step={step}
          value={valueMin}
          onChange={(e) => onMinChange(Number(e.target.value))}
          className="h-2 w-full cursor-pointer appearance-none rounded-lg bg-slate-200"
        />
        <input
          type="range"
          min={min}
          max={max}
          step={step}
          value={valueMax}
          onChange={(e) => onMaxChange(Number(e.target.value))}
          className="h-2 w-full cursor-pointer appearance-none rounded-lg bg-slate-200"
        />
      </div>
    </div>
  );
}

export default function FiltresRecherche({ filtres, setFiltre, resetFiltres, villesSuggestions }) {
  return (
    <div className="space-y-5">
      <div className="flex items-center justify-between">
        <h2 className="text-lg font-semibold text-slate-900">Recherche avancée</h2>
        <button
          type="button"
          onClick={resetFiltres}
          className="text-xs font-semibold text-indigo-600 hover:text-indigo-500"
        >
          Reset
        </button>
      </div>

      <div>
        <Label>Type de bien</Label>
        <select
          value={filtres.type}
          onChange={(e) => setFiltre('type', e.target.value)}
          className="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
        >
          <option value="">Tous</option>
          {TYPES_BIENS.map((type) => (
            <option key={type} value={type}>
              {type}
            </option>
          ))}
        </select>
      </div>

      <div>
        <Label>Ville ou code postal</Label>
        <input
          list="villes-autocomplete"
          value={filtres.ville}
          onChange={(e) => setFiltre('ville', e.target.value)}
          placeholder="Ex : Lyon ou 69003"
          className="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
        />
        <datalist id="villes-autocomplete">
          {villesSuggestions.map((ville) => (
            <option key={ville} value={ville} />
          ))}
        </datalist>
      </div>

      <RangeField
        label="Prix (€)"
        min={50000}
        max={900000}
        step={5000}
        valueMin={filtres.prixMin}
        valueMax={filtres.prixMax}
        onMinChange={(value) => setFiltre('prixMin', Math.min(value, filtres.prixMax))}
        onMaxChange={(value) => setFiltre('prixMax', Math.max(value, filtres.prixMin))}
      />

      <RangeField
        label="Surface (m²)"
        min={10}
        max={1000}
        step={5}
        valueMin={filtres.surfaceMin}
        valueMax={filtres.surfaceMax}
        onMinChange={(value) => setFiltre('surfaceMin', Math.min(value, filtres.surfaceMax))}
        onMaxChange={(value) => setFiltre('surfaceMax', Math.max(value, filtres.surfaceMin))}
      />

      <div>
        <Label>Nombre de pièces</Label>
        <div className="grid grid-cols-5 gap-2">
          {PIECES.map((item) => (
            <button
              key={item}
              type="button"
              onClick={() => setFiltre('pieces', filtres.pieces === item ? '' : item)}
              className={`rounded-lg border px-2 py-1 text-xs font-medium ${
                filtres.pieces === item ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-slate-300'
              }`}
            >
              {item}
            </button>
          ))}
        </div>
      </div>

      <div>
        <Label>Nombre de chambres minimum</Label>
        <select
          value={filtres.chambres}
          onChange={(e) => setFiltre('chambres', e.target.value)}
          className="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
        >
          <option value="">Toutes</option>
          {[1, 2, 3, 4, 5].map((n) => (
            <option key={n} value={n}>
              {n}+
            </option>
          ))}
        </select>
      </div>

      <div>
        <Label>DPE</Label>
        <div className="grid grid-cols-7 gap-1">
          {DPE_NOTES.map((note) => (
            <button
              key={note}
              type="button"
              onClick={() => setFiltre('dpe', filtres.dpe === note ? '' : note)}
              className={`rounded-md border py-1 text-xs font-semibold ${
                filtres.dpe === note ? 'border-emerald-600 bg-emerald-50 text-emerald-700' : 'border-slate-300'
              }`}
            >
              {note}
            </button>
          ))}
        </div>
      </div>

      <div>
        <Label>Options</Label>
        <div className="space-y-2 text-sm text-slate-700">
          {['parking', 'balcon', 'cave', 'ascenseur'].map((opt) => (
            <label key={opt} className="flex items-center gap-2">
              <input
                type="checkbox"
                checked={Boolean(filtres.options[opt])}
                onChange={(e) => setFiltre(`options.${opt}`, e.target.checked)}
                className="h-4 w-4 rounded border-slate-300"
              />
              <span className="capitalize">{opt}</span>
            </label>
          ))}
        </div>
      </div>

      <div>
        <Label>Statut</Label>
        <select
          value={filtres.statut}
          onChange={(e) => setFiltre('statut', e.target.value)}
          className="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
        >
          <option value="">Tous</option>
          {STATUTS.map((statut) => (
            <option key={statut} value={statut}>
              {statut}
            </option>
          ))}
        </select>
      </div>
    </div>
  );
}
