import { slugify } from './seo';

export const BLOG_CATEGORIES = [
  'Conseils achat',
  'Vente',
  'Investissement',
  'Actualités marché',
  'Guides',
  'Juridique',
  'Financement',
];

export const BLOG_ARTICLES = [
  {
    slug: 'acheter-2026-erreurs-a-eviter',
    title: 'Acheter en 2026 : 9 erreurs à éviter avant de signer',
    excerpt: 'Budget, négociation, prêt immobilier : les points critiques pour éviter les mauvaises surprises.',
    category: 'Conseils achat',
    cover: 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1400&q=80',
    blurDataURL:
      'data:image/jpeg;base64,/9j/2wBDABALDwwMDwwQEBAQEBYQFhYWFhYaGhoaGhohISEhISEhISEhISEhISEhISEhISEhISEhISEhISEhP/wAALCAAQABABAREA/8QAFQABAQAAAAAAAAAAAAAAAAAAAAf/xAAXAQEBAQEAAAAAAAAAAAAAAAABAAID/8QAFQEBAQAAAAAAAAAAAAAAAAAAAQL/xAAWEQEBAQAAAAAAAAAAAAAAAAABACH/2gAMAwEAAhEDEQA/AM+g3xkqR2v/2Q==',
    author: {
      name: 'Camille Bernard',
      avatar: 'https://i.pravatar.cc/80?img=32',
      role: 'Conseillère achat',
      bio: 'Camille accompagne les primo-accédants sur les stratégies de financement et de négociation.',
    },
    date: '2026-03-20',
    dateLabel: '20 mars 2026',
    readTime: 7,
    views: 11430,
    commentsCount: 42,
    tags: ['achat', 'prêt', 'négociation'],
    popularScore: 98,
    content: `
      <h2>Préparer son budget global</h2>
      <p>Ne limitez pas votre budget au prix affiché. Intégrez frais de notaire, travaux, mobilier et coût du crédit.</p>
      <h3>Simuler plusieurs scénarios</h3>
      <p>Créez 3 simulations : optimiste, réaliste, prudent. Vous éviterez les décisions impulsives.</p>
      <blockquote>Un budget bien calibré est votre meilleure protection contre les imprévus.</blockquote>
      <h2>Analyser le bien et son environnement</h2>
      <p>Vérifiez le règlement de copropriété, les diagnostics, les projets urbains et la qualité du voisinage.</p>
      <h2>Négocier avec méthode</h2>
      <p>Basez votre négociation sur des comparables locaux récents plutôt que sur une intuition.</p>
      <table><thead><tr><th>Point</th><th>Impact</th></tr></thead><tbody><tr><td>Travaux façade</td><td>Coût élevé à court terme</td></tr><tr><td>DPE faible</td><td>Travaux énergétiques à prévoir</td></tr></tbody></table>
    `,
  },
  {
    slug: 'marche-immobilier-lyon-t1-2026',
    title: 'Marché immobilier Lyon T1 2026 : prix, délais, tendances',
    excerpt: 'Notre décryptage local quartier par quartier pour acheter ou vendre au bon moment.',
    category: 'Actualités marché',
    cover: 'https://images.unsplash.com/photo-1460317442991-0ec209397118?auto=format&fit=crop&w=1400&q=80',
    author: { name: 'Paul Martin', avatar: 'https://i.pravatar.cc/80?img=22', role: 'Analyste marché', bio: 'Paul suit les dynamiques de prix et les volumes de transaction.' },
    date: '2026-03-18',
    dateLabel: '18 mars 2026',
    readTime: 6,
    views: 8670,
    commentsCount: 19,
    tags: ['lyon', 'prix', 'tendances'],
    popularScore: 95,
    content: '<h2>Un marché qui se stabilise</h2><p>Les délais moyens repartent légèrement à la hausse mais la demande reste active.</p>',
  },
  {
    slug: 'frais-notaire-guide-complet',
    title: 'Frais de notaire : guide complet pour bien anticiper',
    excerpt: 'Calcul, exonérations et astuces pour mieux piloter votre budget immobilier.',
    category: 'Guides',
    cover: 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=1400&q=80',
    author: { name: 'Inès Roche', avatar: 'https://i.pravatar.cc/80?img=45', role: 'Rédactrice juridique', bio: 'Inès vulgarise les sujets juridiques immobiliers.' },
    date: '2026-03-15',
    dateLabel: '15 mars 2026',
    readTime: 8,
    views: 5220,
    commentsCount: 12,
    tags: ['notaire', 'budget'],
    popularScore: 90,
    content: '<h2>Comment les calculer</h2><p>Les frais varient selon le type de bien, neuf ou ancien.</p>',
  },
  {
    slug: 'vendre-vite-sans-baisser-prix',
    title: 'Vendre vite sans baisser son prix : méthode en 5 étapes',
    excerpt: 'Home staging, diffusion multicanale et stratégie de prix pour vendre mieux.',
    category: 'Vente',
    cover: 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?auto=format&fit=crop&w=1400&q=80',
    author: { name: 'Camille Bernard', avatar: 'https://i.pravatar.cc/80?img=32', role: 'Conseillère achat', bio: 'Camille accompagne les primo-accédants sur les stratégies de financement et de négociation.' },
    date: '2026-03-10',
    dateLabel: '10 mars 2026',
    readTime: 5,
    views: 6789,
    commentsCount: 24,
    tags: ['vente', 'home-staging'],
    popularScore: 93,
    content: '<h2>Valoriser son bien</h2><p>Le home staging augmente la perception de valeur.</p>',
  },
  {
    slug: 'investissement-locatif-rentabilite',
    title: 'Investissement locatif : calculer la vraie rentabilité',
    excerpt: 'Cash-flow, vacance locative, fiscalité : un modèle simple à reproduire.',
    category: 'Investissement',
    cover: 'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=1400&q=80',
    author: { name: 'Paul Martin', avatar: 'https://i.pravatar.cc/80?img=22', role: 'Analyste marché', bio: 'Paul suit les dynamiques de prix et les volumes de transaction.' },
    date: '2026-03-08',
    dateLabel: '8 mars 2026',
    readTime: 9,
    views: 7400,
    commentsCount: 31,
    tags: ['investissement', 'rentabilité'],
    popularScore: 96,
    content: '<h2>Mesurer la rentabilité nette</h2><p>Incluez charges, fiscalité et vacance locative.</p>',
  },
  {
    slug: 'pret-immobilier-apport-2026',
    title: 'Prêt immobilier 2026 : faut-il encore un gros apport ?',
    excerpt: 'Les critères bancaires changent : comment maximiser vos chances de financement.',
    category: 'Financement',
    cover: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1400&q=80',
    author: { name: 'Inès Roche', avatar: 'https://i.pravatar.cc/80?img=45', role: 'Rédactrice juridique', bio: 'Inès vulgarise les sujets juridiques immobiliers.' },
    date: '2026-03-02',
    dateLabel: '2 mars 2026',
    readTime: 7,
    views: 4988,
    commentsCount: 16,
    tags: ['financement', 'banque'],
    popularScore: 88,
    content: '<h2>Le bon niveau d\'apport</h2><p>Un apport cohérent renforce votre dossier bancaire.</p>',
  },
];

export function getCategorySlug(category) {
  return slugify(category);
}

export function findCategoryBySlug(categorySlug) {
  return BLOG_CATEGORIES.find((category) => getCategorySlug(category) === categorySlug);
}
