import { NextResponse } from 'next/server';
import Negotiator from 'negotiator';
import { match } from '@formatjs/intl-localematcher';
import { COOKIE_KEY, DEFAULT_LOCALE, LOCALES } from './i18n/config';

function getLocaleFromAcceptLanguage(request) {
  const negotiatorHeaders = Object.fromEntries(request.headers.entries());
  const languages = new Negotiator({ headers: negotiatorHeaders }).languages();
  return match(languages, LOCALES, DEFAULT_LOCALE);
}

function getLocaleFromCookie(request) {
  const locale = request.cookies.get(COOKIE_KEY)?.value;
  if (locale && LOCALES.includes(locale)) {
    return locale;
  }

  return null;
}

function getLocaleFromPath(pathname) {
  const maybeLocale = pathname.split('/').filter(Boolean)[0];
  return LOCALES.includes(maybeLocale) ? maybeLocale : null;
}

export function middleware(request) {
  const { pathname } = request.nextUrl;

  if (
    pathname.startsWith('/_next') ||
    pathname.startsWith('/api') ||
    pathname.includes('.')
  ) {
    return NextResponse.next();
  }

  const localeInPath = getLocaleFromPath(pathname);
  if (localeInPath) {
    return NextResponse.next();
  }

  const locale = getLocaleFromCookie(request) || getLocaleFromAcceptLanguage(request);
  const redirectUrl = new URL(`/${locale}${pathname}`, request.url);
  const response = NextResponse.redirect(redirectUrl);

  response.cookies.set(COOKIE_KEY, locale, {
    path: '/',
    maxAge: 60 * 60 * 24 * 365,
    sameSite: 'lax',
  });

  return response;
}

export const config = {
  matcher: ['/((?!_next|.*\\..*).*)'],
};
