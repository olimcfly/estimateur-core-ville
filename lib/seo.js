const SITE_NAME = 'Estimation Immobilier Bordeaux';
const SITE_URL = process.env.NEXT_PUBLIC_SITE_URL || 'https://estimation-immobilier-bordeaux.fr';

export function slugify(text = '') {
  return String(text)
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');
}

export function truncateDescription(text = '', length = 155) {
  if (!text) return '';
  if (text.length <= length) return text;
  return `${text.slice(0, Math.max(0, length - 1)).trim()}…`;
}

export function generateOgImage(title, type = 'default') {
  const params = new URLSearchParams({
    title: truncateDescription(title, 80),
    type,
  });

  return `${SITE_URL}/api/og?${params.toString()}`;
}

export function generateBreadcrumb(items = []) {
  return {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: items.map((item, index) => ({
      '@type': 'ListItem',
      position: index + 1,
      name: item.name,
      item: item.url?.startsWith('http') ? item.url : `${SITE_URL}${item.url || ''}`,
    })),
  };
}

export function generateJsonLd(type, data = {}) {
  const base = {
    '@context': 'https://schema.org',
    '@type': type,
  };

  if (type === 'Article') {
    return {
      ...base,
      headline: data.title,
      description: data.description,
      datePublished: data.publishedAt,
      dateModified: data.updatedAt || data.publishedAt,
      image: data.image,
      author: {
        '@type': 'Person',
        name: data.author?.name,
      },
      mainEntityOfPage: data.url,
    };
  }

  if (type === 'Organization') {
    return {
      ...base,
      name: data.name || SITE_NAME,
      url: data.url || SITE_URL,
      logo: data.logo || `${SITE_URL}/logo.png`,
      sameAs: data.sameAs || [],
    };
  }

  return {
    ...base,
    ...data,
  };
}

export function generateMetadata(page = 'default', data = {}) {
  const pageMap = {
    home: {
      title: `Estimation Immobilière Gratuite ${data.city || 'Bordeaux'} | Évaluation Bien`,
      description: truncateDescription(
        data.description ||
          `Estimez votre bien immobilier à ${data.city || 'Bordeaux'} gratuitement. Outil d'estimation rapide et fiable pour vendre ou acheter.`,
        155
      ),
      keywords: ['estimation immobilière', data.city || 'Bordeaux', 'prix immobilier', 'évaluation bien'],
      type: 'website',
    },
    blog: {
      title: 'Blog Immobilier Bordeaux - Conseils & Actualités | Estimation Immobilier',
      description: truncateDescription(
        data.description ||
          'Analyses du marché immobilier bordelais, guides achat/vente, financement et conseils pour réussir votre projet.',
        155
      ),
      keywords: ['blog immobilier', 'conseils Bordeaux', 'marché immobilier', 'achat vente'],
      type: 'website',
    },
    article: {
      title: `${data.title || 'Article'} | Blog Estimation Immobilier Bordeaux`,
      description: truncateDescription(data.description || '', 155),
      keywords: data.keywords || ['blog immobilier', 'conseils Bordeaux'],
      type: 'article',
    },
    property: {
      title: `${data.type || 'Bien'} ${data.surface || '--'}m² ${data.city || ''} - ${data.price || '--'}€ | NomSite`,
      description: truncateDescription(data.description || '', 155),
      type: 'website',
      robots: data.isArchived ? { index: false, follow: false } : { index: true, follow: true },
    },
    default: {
      title: `${data.title || 'NomSite'} | ${SITE_NAME}`,
      description: truncateDescription(data.description || 'Plateforme immobilière locale : annonces, estimation et conseils.', 155),
      type: 'website',
    },
  };

  const selected = pageMap[page] || pageMap.default;
  const canonical = data.canonical || (data.slug ? `${SITE_URL}/${data.slug}` : undefined);
  const ogImage = data.image || generateOgImage(selected.title, page);

  return {
    metadataBase: new URL(SITE_URL),
    title: selected.title,
    description: selected.description,
    keywords: selected.keywords,
    alternates: canonical ? { canonical } : undefined,
    robots: selected.robots,
    openGraph: {
      title: selected.title,
      description: selected.description,
      type: selected.type,
      url: canonical,
      siteName: SITE_NAME,
      images: [
        {
          url: ogImage,
          width: 1200,
          height: 630,
          alt: selected.title,
        },
      ],
      ...(data.publishedAt
        ? {
            publishedTime: data.publishedAt,
          }
        : {}),
      ...(data.author
        ? {
            authors: [data.author],
          }
        : {}),
    },
    twitter: {
      card: 'summary_large_image',
      title: selected.title,
      description: selected.description,
      images: [ogImage],
    },
  };
}

export { SITE_NAME, SITE_URL };
