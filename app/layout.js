import './globals.css';
import Link from 'next/link';

export const metadata = {
  title: 'Estimation Immobilier',
  description: 'Plateforme d\'estimation immobilière par ville',
};

export default function RootLayout({ children }) {
  return (
    <html lang="fr">
      <body>
        <header className="border-b border-slate-200 bg-white">
          <nav className="mx-auto flex w-full max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <Link href="/" className="text-lg font-semibold text-slate-900">
              Estimation Immobilier
            </Link>
            <Link
              href="/favoris"
              className="rounded-md border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
            >
              Mes favoris
            </Link>
          </nav>
        </header>
        {children}
      </body>
    </html>
  );
}
