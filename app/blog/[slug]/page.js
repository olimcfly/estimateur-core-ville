import Link from 'next/link';
import Image from 'next/image';
import dynamic from 'next/dynamic';
import ArticleContent from '../../../components/blog/ArticleContent';
import SeoHead from '../../../components/SeoHead';
import { BLOG_ARTICLES } from '../../../lib/blogData';
import {
  SITE_URL,
  generateBreadcrumb,
  generateJsonLd,
  generateMetadata as buildMetadata,
  truncateDescription,
} from '../../../lib/seo';

const TableOfContents = dynamic(() => import('../../../components/blog/TableOfContents'), { ssr: false });
const CommentSection = dynamic(() => import('../../../components/blog/CommentSection'), { ssr: false });

export const revalidate = 86400;

function getArticle(slug) {
  return BLOG_ARTICLES.find((article) => article.slug === slug) || BLOG_ARTICLES[0];
}

export function generateMetadata({ params }) {
  const article = getArticle(params.slug);
  return buildMetadata('article', {
    title: article.title,
    description: truncateDescription(article.excerpt, 155),
    image: article.cover,
    publishedAt: article.date,
    author: article.author.name,
    canonical: `/blog/${article.slug}`,
  });
}

export default function BlogArticlePage({ params }) {
  const article = getArticle(params.slug);
  const similar = BLOG_ARTICLES.filter((item) => item.slug !== article.slug).slice(0, 3);

  const breadcrumbItems = [
    { name: 'Accueil', url: '/' },
    { name: 'Blog', url: '/blog' },
    { name: article.category, url: `/blog/categorie/${article.category.toLowerCase().replace(/\s+/g, '-')}` },
    { name: article.title, url: `/blog/${article.slug}` },
  ];

  const jsonLd = generateJsonLd('Article', {
    title: article.title,
    description: article.excerpt,
    publishedAt: article.date,
    updatedAt: article.date,
    image: article.cover,
    author: article.author,
    url: `${SITE_URL}/blog/${article.slug}`,
  });

  const breadcrumbJsonLd = generateBreadcrumb(breadcrumbItems);

  const comments = [
    {
      id: 'c1',
      author: 'Sophie',
      dateLabel: 'Il y a 2 jours',
      message: 'Excellent guide, très concret pour préparer son budget.',
      replies: [{ id: 'r1', author: 'Camille Bernard', message: 'Merci Sophie ! Ravi que cela vous aide.' }],
    },
    {
      id: 'c2',
      author: 'Nicolas',
      dateLabel: 'Il y a 1 jour',
      message: 'Une checklist PDF serait top pour la visite de biens.',
      replies: [],
    },
  ];

  return (
    <main className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
      <SeoHead jsonLd={jsonLd} />
      <SeoHead jsonLd={breadcrumbJsonLd} />

      <nav className="mb-5 text-sm text-slate-500">
        {breadcrumbItems.map((item, index) => (
          <span key={item.url}>
            <Link href={item.url} className="hover:text-blue-700">
              {item.name}
            </Link>
            {index < breadcrumbItems.length - 1 ? ' > ' : ''}
          </span>
        ))}
      </nav>

      <div className="relative mb-6 aspect-[16/7] overflow-hidden rounded-3xl">
        <Image src={article.cover} alt={article.title} fill priority sizes="100vw" className="object-cover" />
      </div>

      <header className="mb-8">
        <h1 className="text-3xl font-bold text-slate-900 md:text-4xl">{article.title}</h1>
        <p className="mt-3 text-sm text-slate-600">
          {article.author.name} • <time dateTime={article.date}>{article.dateLabel}</time> • {article.readTime} min •{' '}
          {article.views.toLocaleString('fr-FR')} vues
        </p>

        <div className="mt-4 flex flex-wrap gap-2">
          {['Facebook', 'Twitter', 'LinkedIn'].map((network) => (
            <button key={network} type="button" className="rounded-full border border-slate-300 px-3 py-1 text-xs font-medium">
              {network}
            </button>
          ))}
          <button
            type="button"
            className="rounded-full border border-slate-300 px-3 py-1 text-xs font-medium"
            onClick={() => navigator.clipboard.writeText(`${SITE_URL}/blog/${article.slug}`)}
          >
            Copier lien
          </button>
        </div>
      </header>

      <div className="grid gap-8 lg:grid-cols-[minmax(0,1fr)_280px]">
        <article className="article-content min-w-0">
          <ArticleContent
            content={article.content}
            images={[{ src: article.cover, alt: article.title, caption: 'Crédit photo : banque d’images' }]}
          />

          <div className="mt-6 flex flex-wrap gap-2">
            {article.tags.map((tag) => (
              <span key={tag} className="rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-700">
                #{tag}
              </span>
            ))}
          </div>

          <section className="mt-8 rounded-2xl border border-slate-200 bg-white p-5">
            <div className="flex items-center gap-3">
              <Image src={article.author.avatar} alt={article.author.name} width={56} height={56} className="rounded-full" />
              <div>
                <p className="font-semibold text-slate-900">{article.author.name}</p>
                <p className="text-sm text-slate-500">{article.author.role}</p>
              </div>
            </div>
            <p className="mt-3 text-sm text-slate-700">{article.author.bio}</p>
          </section>

          <section className="mt-8">
            <h2 className="mb-4 text-xl font-semibold text-slate-900">Articles similaires</h2>
            <div className="grid gap-4 md:grid-cols-3">
              {similar.map((item) => (
                <Link key={item.slug} href={`/blog/${item.slug}`} className="rounded-xl border border-slate-200 bg-white p-4">
                  <p className="text-xs text-blue-600">{item.category}</p>
                  <p className="mt-1 text-sm font-semibold text-slate-900">{item.title}</p>
                </Link>
              ))}
            </div>
          </section>

          <div className="mt-8">
            <CommentSection initialComments={comments} />
          </div>
        </article>

        <div>
          <TableOfContents containerSelector=".article-content" />
        </div>
      </div>
    </main>
  );
}
