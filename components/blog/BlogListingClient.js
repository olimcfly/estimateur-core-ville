'use client';

import { useMemo, useState } from 'react';
import Image from 'next/image';
import Link from 'next/link';
import ArticleCard from './ArticleCard';
import { BLOG_ARTICLES, BLOG_CATEGORIES, getCategorySlug } from '../../lib/blogData';

const PAGE_SIZE = 6;

export default function BlogListingClient() {
  const [query, setQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('Toutes');
  const [sortBy, setSortBy] = useState('recent');
  const [page, setPage] = useState(1);

  const filtered = useMemo(() => {
    const normalizedQuery = query.trim().toLowerCase();

    let items = BLOG_ARTICLES.filter((article) => {
      const inCategory = selectedCategory === 'Toutes' || article.category === selectedCategory;
      const inSearch =
        !normalizedQuery ||
        article.title.toLowerCase().includes(normalizedQuery) ||
        article.excerpt.toLowerCase().includes(normalizedQuery) ||
        article.tags.some((tag) => tag.includes(normalizedQuery));

      return inCategory && inSearch;
    });

    if (sortBy === 'popular') items = items.sort((a, b) => b.popularScore - a.popularScore);
    if (sortBy === 'commented') items = items.sort((a, b) => b.commentsCount - a.commentsCount);
    if (sortBy === 'recent') items = items.sort((a, b) => new Date(b.date) - new Date(a.date));

    return items;
  }, [query, selectedCategory, sortBy]);

  const featured = filtered[0] || BLOG_ARTICLES[0];
  const recent = filtered.slice(1, page * PAGE_SIZE + 1);

  const categoriesCount = BLOG_CATEGORIES.map((category) => ({
    category,
    count: BLOG_ARTICLES.filter((article) => article.category === category).length,
  }));

  const allTags = Array.from(new Set(BLOG_ARTICLES.flatMap((article) => article.tags)));

  return (
    <main className="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
      <header className="mb-8 flex flex-wrap gap-3 rounded-2xl border border-slate-200 bg-white p-4">
        <input
          type="search"
          value={query}
          onChange={(event) => {
            setQuery(event.target.value);
            setPage(1);
          }}
          placeholder="Rechercher un article..."
          className="min-w-[240px] flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm"
        />

        <select
          value={selectedCategory}
          onChange={(event) => {
            setSelectedCategory(event.target.value);
            setPage(1);
          }}
          className="rounded-lg border border-slate-300 px-3 py-2 text-sm"
        >
          <option>Toutes</option>
          {BLOG_CATEGORIES.map((category) => (
            <option key={category} value={category}>
              {category}
            </option>
          ))}
        </select>

        <select
          value={sortBy}
          onChange={(event) => setSortBy(event.target.value)}
          className="rounded-lg border border-slate-300 px-3 py-2 text-sm"
        >
          <option value="recent">Récent</option>
          <option value="popular">Populaire</option>
          <option value="commented">Commentés</option>
        </select>
      </header>

      <div className="grid gap-8 xl:grid-cols-[minmax(0,1fr)_320px]">
        <div className="space-y-8">
          <section className="overflow-hidden rounded-3xl border border-slate-200 bg-white">
            <div className="relative aspect-[16/7] w-full">
              <Image
                src={featured.cover}
                alt={featured.title}
                fill
                priority
                sizes="(max-width: 1200px) 100vw, 70vw"
                className="object-cover"
              />
            </div>
            <div className="p-6">
              <p className="text-sm font-semibold text-blue-700">Article mis en avant</p>
              <h1 className="mt-2 text-3xl font-bold text-slate-900">{featured.title}</h1>
              <p className="mt-3 text-slate-600">{featured.excerpt}</p>
              <Link href={`/blog/${featured.slug}`} className="mt-5 inline-flex text-sm font-semibold text-blue-700">
                Lire l'article →
              </Link>
            </div>
          </section>

          <section className="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            {recent.map((article) => (
              <ArticleCard key={article.slug} article={article} />
            ))}
          </section>

          {recent.length < filtered.length - 1 ? (
            <button
              type="button"
              onClick={() => setPage((current) => current + 1)}
              className="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700"
            >
              Load more
            </button>
          ) : null}
        </div>

        <aside className="space-y-5">
          <section className="rounded-2xl border border-slate-200 bg-white p-4">
            <h2 className="text-sm font-semibold text-slate-900">Articles populaires</h2>
            <ul className="mt-3 space-y-3 text-sm">
              {[...BLOG_ARTICLES]
                .sort((a, b) => b.views - a.views)
                .slice(0, 4)
                .map((article) => (
                  <li key={article.slug}>
                    <Link href={`/blog/${article.slug}`} className="text-slate-700 hover:text-blue-700">
                      {article.title}
                    </Link>
                  </li>
                ))}
            </ul>
          </section>

          <section className="rounded-2xl border border-slate-200 bg-white p-4">
            <h2 className="text-sm font-semibold text-slate-900">Catégories</h2>
            <ul className="mt-3 space-y-2 text-sm">
              {categoriesCount.map((item) => (
                <li key={item.category} className="flex items-center justify-between">
                  <Link href={`/blog/categorie/${getCategorySlug(item.category)}`}>{item.category}</Link>
                  <span className="rounded-full bg-slate-100 px-2 py-0.5 text-xs">{item.count}</span>
                </li>
              ))}
            </ul>
          </section>

          <section className="rounded-2xl border border-slate-200 bg-white p-4">
            <h2 className="text-sm font-semibold text-slate-900">Newsletter</h2>
            <p className="mt-2 text-sm text-slate-600">1 email/semaine sur le marché immobilier local.</p>
            <input type="email" placeholder="Votre email" className="mt-3 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
            <button type="button" className="mt-2 w-full rounded-lg bg-slate-900 px-3 py-2 text-sm font-medium text-white">
              S'inscrire
            </button>
          </section>

          <section className="rounded-2xl border border-slate-200 bg-white p-4">
            <h2 className="text-sm font-semibold text-slate-900">Tags</h2>
            <div className="mt-3 flex flex-wrap gap-2">
              {allTags.map((tag) => (
                <span key={tag} className="rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-700">
                  #{tag}
                </span>
              ))}
            </div>
          </section>
        </aside>
      </div>
    </main>
  );
}
