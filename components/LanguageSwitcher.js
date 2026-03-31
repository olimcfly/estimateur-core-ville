'use client';

import { useMemo, useState, useTransition } from 'react';
import { usePathname, useRouter } from 'next/navigation';
import { LOCALES, localeFlags, localeNames, normalizeLocale, persistLocale, isRtlLocale } from '../i18n/config';

export default function LanguageSwitcher({ currentLocale = 'fr' }) {
  const [open, setOpen] = useState(false);
  const [isPending, startTransition] = useTransition();
  const pathname = usePathname();
  const router = useRouter();

  const activeLocale = useMemo(() => normalizeLocale(currentLocale), [currentLocale]);

  function getLocalizedPath(nextLocale) {
    const segments = pathname?.split('/').filter(Boolean) ?? [];
    if (segments.length > 0 && LOCALES.includes(segments[0])) {
      segments[0] = nextLocale;
      return `/${segments.join('/')}`;
    }

    return `/${nextLocale}${pathname === '/' ? '' : pathname}`;
  }

  function onSelectLocale(locale) {
    if (locale === activeLocale) {
      setOpen(false);
      return;
    }

    const href = getLocalizedPath(locale);
    persistLocale(locale);

    startTransition(() => {
      document.documentElement.lang = locale;
      document.documentElement.dir = isRtlLocale(locale) ? 'rtl' : 'ltr';
      router.push(href, { scroll: false });
    });

    setOpen(false);
  }

  return (
    <div className="relative inline-block text-left">
      <button
        type="button"
        aria-haspopup="menu"
        aria-expanded={open}
        aria-label="Changer de langue"
        onClick={() => setOpen((value) => !value)}
        className="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900"
      >
        <span>{localeFlags[activeLocale]}</span>
        <span>{localeNames[activeLocale]}</span>
        <span aria-hidden="true" className={`transition-transform ${open ? 'rotate-180' : ''}`}>▾</span>
      </button>

      {open && (
        <ul
          role="menu"
          className="absolute z-30 mt-2 w-48 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg"
        >
          {LOCALES.map((locale) => (
            <li key={locale} role="none">
              <button
                role="menuitem"
                type="button"
                disabled={isPending}
                onClick={() => onSelectLocale(locale)}
                className={`flex w-full items-center justify-between px-3 py-2 text-sm transition hover:bg-slate-50 ${locale === activeLocale ? 'bg-slate-100 font-semibold text-slate-900' : 'text-slate-700'}`}
              >
                <span className="flex items-center gap-2">
                  <span>{localeFlags[locale]}</span>
                  <span>{localeNames[locale]}</span>
                </span>
                {locale === activeLocale ? <span aria-hidden="true">✓</span> : null}
              </button>
            </li>
          ))}
        </ul>
      )}
    </div>
  );
}
