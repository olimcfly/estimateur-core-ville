import { BLOG_ARTICLES } from '../lib/blogData';

const SITE_URL = process.env.NEXT_PUBLIC_SITE_URL || 'https://www.nomsite-immobilier.fr';

const STATIC_PAGES = ['/', '/blog', '/favoris'];

const MOCK_PROPERTIES = [
  { id: '1-appartement-lyon', updatedAt: '2026-03-25' },
  { id: '2-maison-lyon', updatedAt: '2026-03-22' },
  { id: '4-appartement-lyon', updatedAt: '2026-03-19' },
];

const CITY_PAGES = ['paris', 'lyon', 'marseille', 'toulouse'];

export default function sitemap() {
  const now = new Date();

  const staticEntries = STATIC_PAGES.map((path) => ({
    url: `${SITE_URL}${path}`,
    lastModified: now,
    changeFrequency: 'weekly',
    priority: 1.0,
  }));

  const propertyEntries = MOCK_PROPERTIES.map((property) => ({
    url: `${SITE_URL}/bien/${property.id}`,
    lastModified: new Date(property.updatedAt),
    changeFrequency: 'daily',
    priority: 0.8,
  }));

  const blogEntries = BLOG_ARTICLES.map((article) => ({
    url: `${SITE_URL}/blog/${article.slug}`,
    lastModified: new Date(article.date),
    changeFrequency: 'weekly',
    priority: 0.7,
  }));

  const cityEntries = CITY_PAGES.map((city) => ({
    url: `${SITE_URL}/localite/${city}`,
    lastModified: now,
    changeFrequency: 'weekly',
    priority: 0.6,
  }));

  return [...staticEntries, ...propertyEntries, ...blogEntries, ...cityEntries];
}
