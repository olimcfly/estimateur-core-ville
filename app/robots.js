const SITE_URL = process.env.NEXT_PUBLIC_SITE_URL || 'https://www.nomsite-immobilier.fr';

export default function robots() {
  return {
    rules: [
      {
        userAgent: '*',
        allow: '/',
        disallow: ['/admin', '/dashboard', '/api'],
        crawlDelay: 2,
      },
    ],
    sitemap: `${SITE_URL}/sitemap.xml`,
    host: SITE_URL,
  };
}
