import '../globals.css';
import { Noto_Sans_Arabic } from 'next/font/google';
import CookieBanner from '../../components/CookieBanner';
import LanguageSwitcher from '../../components/LanguageSwitcher';
import { DEFAULT_LOCALE, LOCALES, isRtlLocale, loadMessages, normalizeLocale } from '../../i18n/config';

const notoSansArabic = Noto_Sans_Arabic({
  subsets: ['arabic'],
  weight: ['400', '500', '700'],
  variable: '--font-noto-arabic',
});

export async function generateStaticParams() {
  return LOCALES.map((locale) => ({ locale }));
}

export async function generateMetadata({ params }) {
  const locale = normalizeLocale(params?.locale);
  return {
    title: locale === 'en' ? 'Real Estate Platform' : locale === 'ar' ? 'منصة العقارات' : 'Plateforme Immobilière',
    description: locale === 'en'
      ? 'Find, estimate, buy and sell your properties with confidence.'
      : locale === 'ar'
        ? 'ابحث وقدّر واشترِ وبِع العقارات بثقة.'
        : 'Trouvez, estimez, achetez et vendez vos biens avec confiance.',
    manifest: '/manifest.json',
    themeColor: '#0f172a',
  };
}

export default async function LocaleLayout({ children, params }) {
  const locale = normalizeLocale(params?.locale || DEFAULT_LOCALE);
  const dir = isRtlLocale(locale) ? 'rtl' : 'ltr';
  const messages = await loadMessages(locale);

  return (
    <html lang={locale} dir={dir} className={`${notoSansArabic.variable} scroll-smooth`} suppressHydrationWarning>
      <body className="bg-white text-slate-900 antialiased transition-colors duration-300 dark:bg-slate-950 dark:text-slate-50">
        <a href="#main-content" className="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-slate-900 focus:px-3 focus:py-2 focus:text-white">Aller au contenu principal</a>

        <header className="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur dark:border-slate-800 dark:bg-slate-950/95">
          <div className="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
            <p className="font-semibold">ImmoVille</p>
            <nav aria-label="Navigation principale" className="hidden items-center gap-4 md:flex">
              <a href={`/${locale}`} className="text-sm hover:underline">{messages.nav.accueil}</a>
              <a href={`/${locale}/estimation`} className="text-sm hover:underline">{messages.nav.estimation}</a>
              <a href={`/${locale}/contact`} className="text-sm hover:underline">{messages.nav.contact}</a>
            </nav>

            <div className={`flex items-center gap-2 ${dir === 'rtl' ? 'rtl:flex-row-reverse' : ''}`}>
              <button
                type="button"
                data-theme-toggle
                aria-label="Activer le mode sombre"
                className="rounded-lg border border-slate-300 px-3 py-2 text-sm hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
              >
                🌓
              </button>
              <LanguageSwitcher currentLocale={locale} />
            </div>
          </div>
        </header>

        <main id="main-content" className="min-h-[calc(100vh-64px)]">{children}</main>

        <CookieBanner />

        <script
          dangerouslySetInnerHTML={{
            __html: `
(function () {
  const key = 'theme-preference';
  const root = document.documentElement;
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const saved = localStorage.getItem(key);
  const dark = saved ? saved === 'dark' : prefersDark;
  root.classList.toggle('dark', dark);

  document.querySelector('[data-theme-toggle]')?.addEventListener('click', function () {
    const nextDark = !root.classList.contains('dark');
    root.classList.toggle('dark', nextDark);
    localStorage.setItem(key, nextDark ? 'dark' : 'light');
  });

  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(function () {});
  }
})();`,
          }}
        />
      </body>
    </html>
  );
}
