export const LOCALES = ['fr', 'en', 'ar'];
export const DEFAULT_LOCALE = 'fr';
export const RTL_LOCALES = ['ar'];

export const localeNames = {
  fr: 'Français',
  en: 'English',
  ar: 'العربية',
};

export const localeFlags = {
  fr: '🇫🇷',
  en: '🇬🇧',
  ar: '🇸🇦',
};

export const STORAGE_KEY = 'immo.locale';
export const COOKIE_KEY = 'preferred_locale';

export function isValidLocale(locale) {
  return LOCALES.includes(locale);
}

export function isRtlLocale(locale = DEFAULT_LOCALE) {
  return RTL_LOCALES.includes(locale);
}

export function normalizeLocale(input) {
  if (!input) return DEFAULT_LOCALE;

  const candidate = input.toLowerCase().split('-')[0];
  return isValidLocale(candidate) ? candidate : DEFAULT_LOCALE;
}

export function getLocaleFromPathname(pathname = '') {
  const firstSegment = pathname.split('/').filter(Boolean)[0];
  return isValidLocale(firstSegment) ? firstSegment : null;
}

export function detectBrowserLocale() {
  if (typeof window === 'undefined') {
    return DEFAULT_LOCALE;
  }

  const persisted = window.localStorage?.getItem(STORAGE_KEY);
  if (persisted && isValidLocale(persisted)) {
    return persisted;
  }

  const languages = Array.isArray(navigator.languages) && navigator.languages.length
    ? navigator.languages
    : [navigator.language];

  for (const lang of languages) {
    const normalized = normalizeLocale(lang);
    if (isValidLocale(normalized)) {
      return normalized;
    }
  }

  return DEFAULT_LOCALE;
}

export function persistLocale(locale) {
  if (typeof document === 'undefined' || typeof window === 'undefined') {
    return;
  }

  const normalized = normalizeLocale(locale);
  window.localStorage?.setItem(STORAGE_KEY, normalized);
  document.cookie = `${COOKIE_KEY}=${normalized}; path=/; max-age=${60 * 60 * 24 * 365}; samesite=lax`;
}

export async function loadMessages(locale) {
  const safeLocale = normalizeLocale(locale);

  try {
    const messages = (await import(`./locales/${safeLocale}.json`)).default;

    if (safeLocale === DEFAULT_LOCALE) {
      return messages;
    }

    const fallback = (await import(`./locales/${DEFAULT_LOCALE}.json`)).default;
    return deepMerge(fallback, messages);
  } catch {
    const fallback = (await import(`./locales/${DEFAULT_LOCALE}.json`)).default;
    return fallback;
  }
}

function deepMerge(base, override) {
  if (Array.isArray(base)) return override ?? base;
  if (typeof base !== 'object' || base === null) {
    return override ?? base;
  }

  const merged = { ...base };

  for (const [key, value] of Object.entries(override ?? {})) {
    const baseValue = merged[key];
    merged[key] = typeof value === 'object' && value !== null
      ? deepMerge(baseValue, value)
      : value;
  }

  return merged;
}
