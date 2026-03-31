import Link from 'next/link';
import ArticleCard from '../../../../components/blog/ArticleCard';
import { BLOG_ARTICLES, findCategoryBySlug } from '../../../../lib/blogData';
import { generateMetadata as buildMetadata } from '../../../../lib/seo';

export const revalidate = 86400;

export function generateMetadata({ params }) {
  const categoryName = findCategoryBySlug(params.slug) || 'Catégorie';

  return buildMetadata('blog', {
    canonical: `/blog/categorie/${params.slug}`,
    description: `Retrouvez tous nos articles de la catégorie ${categoryName} : guides pratiques, analyses et conseils experts.`,
  });
}

export default function BlogCategoryPage({ params }) {
  const categoryName = findCategoryBySlug(params.slug);
  const articles = BLOG_ARTICLES.filter((article) => article.category === categoryName);

  return (
    <main className="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
      <nav className="mb-4 text-sm text-slate-500">
        <Link href="/">Accueil</Link> &gt; <Link href="/blog">Blog</Link> &gt; {categoryName || 'Catégorie'}
      </nav>

      <h1 className="text-3xl font-bold text-slate-900">Catégorie : {categoryName || 'Introuvable'}</h1>
      <p className="mt-2 text-slate-600">{articles.length} article(s) dans cette catégorie.</p>

      {articles.length ? (
        <section className="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
          {articles.map((article) => (
            <ArticleCard key={article.slug} article={article} />
          ))}
        </section>
      ) : (
        <div className="mt-8 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-slate-600">
          Aucun article disponible pour cette catégorie.
        </div>
      )}
    </main>
  );
}
