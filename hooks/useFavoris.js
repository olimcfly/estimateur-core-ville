'use client';

import { useCallback, useEffect, useMemo, useState } from 'react';

const STORAGE_KEY = 'favoris_biens';

function lireFavoris() {
  if (typeof window === 'undefined') return [];

  try {
    const brut = localStorage.getItem(STORAGE_KEY);
    if (!brut) return [];

    const parsed = JSON.parse(brut);
    return Array.isArray(parsed) ? parsed : [];
  } catch {
    return [];
  }
}

function sauverFavoris(favoris) {
  if (typeof window === 'undefined') return;

  localStorage.setItem(STORAGE_KEY, JSON.stringify(favoris));
  window.dispatchEvent(new CustomEvent('favoris:updated', { detail: favoris }));
}

export function useFavoris() {
  const [favoris, setFavoris] = useState([]);

  useEffect(() => {
    setFavoris(lireFavoris());
  }, []);

  useEffect(() => {
    const syncFavoris = (event) => {
      if (event?.detail && Array.isArray(event.detail)) {
        setFavoris(event.detail);
        return;
      }

      setFavoris(lireFavoris());
    };

    window.addEventListener('storage', syncFavoris);
    window.addEventListener('favoris:updated', syncFavoris);

    return () => {
      window.removeEventListener('storage', syncFavoris);
      window.removeEventListener('favoris:updated', syncFavoris);
    };
  }, []);

  const ajouterFavori = useCallback((bien) => {
    if (!bien?.id) return;

    setFavoris((precedents) => {
      if (precedents.some((item) => item.id === bien.id)) return precedents;

      const suivants = [bien, ...precedents];
      sauverFavoris(suivants);
      return suivants;
    });
  }, []);

  const supprimerFavori = useCallback((id) => {
    setFavoris((precedents) => {
      const suivants = precedents.filter((item) => item.id !== id);
      sauverFavoris(suivants);
      return suivants;
    });
  }, []);

  const estFavori = useCallback(
    (id) => favoris.some((item) => item.id === id),
    [favoris]
  );

  const viderFavoris = useCallback(() => {
    setFavoris([]);
    sauverFavoris([]);
  }, []);

  const compteur = useMemo(() => favoris.length, [favoris]);

  return {
    favoris,
    ajouterFavori,
    supprimerFavori,
    estFavori,
    viderFavoris,
    compteur,
  };
}

export default useFavoris;
