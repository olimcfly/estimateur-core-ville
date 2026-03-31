'use client';

import { useEffect, useState } from 'react';
import { useFavoris } from '../hooks/useFavoris';

function Coeur({ actif }) {
  return (
    <svg
      viewBox="0 0 24 24"
      className={`h-5 w-5 transition-all duration-300 ${
        actif ? 'fill-red-500 text-red-500 scale-110' : 'fill-transparent text-slate-500'
      }`}
      aria-hidden="true"
    >
      <path
        stroke="currentColor"
        strokeWidth="1.8"
        d="M12 21s-6.716-4.43-9.193-8.063c-1.684-2.47-1.145-5.905 1.158-7.639 2.178-1.64 5.353-1.353 7.243.654l.792.84.792-.84c1.89-2.007 5.065-2.294 7.243-.654 2.303 1.734 2.842 5.169 1.158 7.639C18.716 16.57 12 21 12 21z"
      />
    </svg>
  );
}

export default function BoutonFavori({ bien, className = '', taille = 'md' }) {
  const { ajouterFavori, supprimerFavori, estFavori } = useFavoris();
  const [pulse, setPulse] = useState(false);
  const actif = estFavori(bien?.id);

  useEffect(() => {
    if (!pulse) return;

    const timeout = setTimeout(() => setPulse(false), 240);
    return () => clearTimeout(timeout);
  }, [pulse]);

  const handleClick = () => {
    if (!bien?.id) return;

    setPulse(true);

    if (actif) {
      supprimerFavori(bien.id);
      return;
    }

    ajouterFavori(bien);
  };

  const tailleClasses = {
    sm: 'h-8 w-8',
    md: 'h-10 w-10',
    lg: 'h-12 w-12',
  };

  return (
    <button
      type="button"
      onClick={handleClick}
      title={actif ? 'Retirer des favoris' : 'Ajouter aux favoris'}
      aria-label={actif ? 'Retirer des favoris' : 'Ajouter aux favoris'}
      className={`
        ${tailleClasses[taille] ?? tailleClasses.md}
        inline-flex items-center justify-center rounded-full border border-slate-200 bg-white/95 backdrop-blur
        shadow-sm transition-all duration-300 hover:scale-105 hover:border-red-200 hover:shadow
        focus:outline-none focus:ring-2 focus:ring-red-300
        ${pulse ? 'animate-pulse' : ''}
        ${className}
      `}
    >
      <Coeur actif={actif} />
    </button>
  );
}
