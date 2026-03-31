'use client';

import Link from 'next/link';
import { useEffect, useMemo, useState } from 'react';

const STORAGE_KEY = 'cookie-consent-v1';
const MAX_AGE_MS = 1000 * 60 * 60 * 24 * 30 * 13;

const defaultConsent = {
  essentials: true,
  analytics: false,
  marketing: false,
  preferences: false,
  updatedAt: Date.now(),
};

function isConsentValid(consent) {
  return consent?.updatedAt && Date.now() - consent.updatedAt < MAX_AGE_MS;
}

function applyAnalytics(consent) {
  if (typeof window === 'undefined') return;
  window.__analyticsDisabled = !consent.analytics;
}

export default function CookieBanner() {
  const [isVisible, setIsVisible] = useState(false);
  const [showModal, setShowModal] = useState(false);
  const [consent, setConsent] = useState(defaultConsent);

  useEffect(() => {
    try {
      const parsed = JSON.parse(window.localStorage.getItem(STORAGE_KEY) || 'null');
      if (parsed && isConsentValid(parsed)) {
        setConsent(parsed);
        applyAnalytics(parsed);
        setIsVisible(false);
        return;
      }
    } catch {
      // ignore invalid localStorage payload
    }

    setIsVisible(true);
  }, []);

  const toggleKeys = useMemo(() => ['analytics', 'marketing', 'preferences'], []);

  function persist(nextConsent) {
    const payload = { ...nextConsent, essentials: true, updatedAt: Date.now() };
    setConsent(payload);
    window.localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
    document.cookie = `cookie_consent=${encodeURIComponent(JSON.stringify(payload))}; path=/; max-age=${60 * 60 * 24 * 30 * 13}; samesite=lax`;
    applyAnalytics(payload);
    setIsVisible(false);
    setShowModal(false);
  }

  if (!isVisible) return null;

  return (
    <>
      <aside
        role="dialog"
        aria-label="Bannière de consentement cookies"
        className="fixed inset-x-4 bottom-4 z-40 rounded-2xl border border-slate-200 bg-white p-4 shadow-xl sm:inset-x-auto sm:right-6 sm:w-[540px]"
      >
        <p className="text-sm leading-relaxed text-slate-700">
          Nous utilisons des cookies pour améliorer votre expérience, mesurer l'audience (GA4) et personnaliser le contenu.
          Vous pouvez accepter, refuser ou personnaliser vos choix.
        </p>
        <div className="mt-3 flex flex-wrap gap-2">
          <button type="button" onClick={() => persist({ ...defaultConsent, analytics: true, marketing: true, preferences: true })} className="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white transition hover:bg-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900">Tout accepter</button>
          <button type="button" onClick={() => setShowModal(true)} className="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900">Personnaliser</button>
          <button type="button" onClick={() => persist({ ...defaultConsent })} className="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900">Refuser</button>
          <Link href="/confidentialite" className="ml-auto text-sm text-slate-600 underline hover:text-slate-900">Politique de confidentialité</Link>
        </div>
      </aside>

      {showModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
          <section role="dialog" aria-modal="true" aria-label="Personnalisation des cookies" className="w-full max-w-lg rounded-2xl bg-white p-5 shadow-xl">
            <h2 className="text-lg font-semibold text-slate-900">Personnaliser les cookies</h2>
            <p className="mt-1 text-sm text-slate-600">Les cookies essentiels sont toujours actifs.</p>

            <div className="mt-4 space-y-3">
              <div className="flex items-center justify-between rounded-xl border border-slate-200 p-3">
                <div>
                  <p className="font-medium text-slate-900">Essentiels</p>
                  <p className="text-xs text-slate-600">Toujours actifs</p>
                </div>
                <span className="text-xs font-semibold text-emerald-700">Actif</span>
              </div>

              {toggleKeys.map((key) => (
                <label key={key} className="flex cursor-pointer items-center justify-between rounded-xl border border-slate-200 p-3">
                  <div>
                    <p className="font-medium capitalize text-slate-900">{key}</p>
                    <p className="text-xs text-slate-600">
                      {key === 'analytics' ? 'Mesure d\'audience et performance.' : key === 'marketing' ? 'Campagnes publicitaires et retargeting.' : 'Mémorise vos préférences de navigation.'}
                    </p>
                  </div>
                  <input
                    type="checkbox"
                    checked={consent[key]}
                    onChange={(event) => setConsent((prev) => ({ ...prev, [key]: event.target.checked }))}
                    className="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                  />
                </label>
              ))}
            </div>

            <div className="mt-5 flex justify-end gap-2">
              <button type="button" onClick={() => setShowModal(false)} className="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700">Annuler</button>
              <button type="button" onClick={() => persist(consent)} className="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white">Confirmer</button>
            </div>
          </section>
        </div>
      )}
    </>
  );
}
