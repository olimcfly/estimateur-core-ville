import './globals.css';
import Link from 'next/link';

export const metadata = {
  title: 'Estimation Immobilière Bordeaux | Gratuit & Rapide',
  description: 'Estimez la valeur de votre bien immobilier à Bordeaux gratuitement. Outil d\'estimation rapide et fiable pour vendre ou acheter.',
  keywords: 'estimation immobilière, Bordeaux, prix immobilier, évaluation bien, estimation gratuite',
  authors: [{ name: 'Estimation Immobilier Bordeaux' }],
  openGraph: {
    title: 'Estimation Immobilière Bordeaux | Gratuit & Rapide',
    description: 'Estimez la valeur de votre bien immobilier à Bordeaux gratuitement. Outil d\'estimation rapide et fiable.',
    type: 'website',
    url: 'https://estimation-immobilier-bordeaux.fr',
    siteName: 'Estimation Immobilier Bordeaux',
    images: [
      {
        url: 'https://estimation-immobilier-bordeaux.fr/og-default.jpg',
        width: 1200,
        height: 630,
        alt: 'Estimation Immobilière Bordeaux',
      },
    ],
  },
  robots: {
    index: true,
    follow: true,
    googleBot: {
      index: true,
      follow: true,
      'max-snippet': -1,
      'max-image-preview': 'large',
      'max-video-preview': -1,
    },
  },
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
