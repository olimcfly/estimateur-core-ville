'use client';

import Image from 'next/image';
import Link from 'next/link';

const CATEGORY_COLORS = {
  'Conseils achat': 'bg-blue-100 text-blue-700',
  Vente: 'bg-emerald-100 text-emerald-700',
  Investissement: 'bg-purple-100 text-purple-700',
  'Actualités marché': 'bg-orange-100 text-orange-700',
  Guides: 'bg-cyan-100 text-cyan-700',
  Juridique: 'bg-red-100 text-red-700',
  Financement: 'bg-amber-100 text-amber-700',
};

export default function ArticleCard({ article }) {
  const badgeColor = CATEGORY_COLORS[article.category] || 'bg-slate-100 text-slate-700';
  const articleUrl = `/blog/${article.slug}`;

  const onShare = async () => {
    const url = typeof window !== 'undefined' ? `${window.location.origin}${articleUrl}` : articleUrl;

    if (navigator.share) {
      await navigator.share({ title: article.title, url });
      return;
    }

    await navigator.clipboard.writeText(url);
  };

  return (
    <article className="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
      <Link href={articleUrl} className="relative block aspect-[16/10] overflow-hidden bg-slate-100">
        <Image
          src={article.cover}
          alt={article.title}
          fill
          sizes="(max-width: 768px) 100vw, (max-width: 1280px) 33vw, 28vw"
          className="object-cover transition duration-500 group-hover:scale-105"
          loading="lazy"
          placeholder={article.blurDataURL ? 'blur' : 'empty'}
          blurDataURL={article.blurDataURL}
        />
      </Link>

      <div className="flex flex-1 flex-col p-5">
        <span className={`mb-3 inline-flex w-fit rounded-full px-2.5 py-1 text-xs font-semibold ${badgeColor}`}>
          {article.category}
        </span>

        <Link href={articleUrl}>
          <h3 className="line-clamp-2 text-lg font-semibold text-slate-900">{article.title}</h3>
        </Link>

        <p className="mt-2 line-clamp-3 text-sm text-slate-600">{article.excerpt}</p>

        <div className="mt-4 flex items-center gap-3 text-xs text-slate-500">
          <Image
            src={article.author.avatar}
            alt={article.author.name}
            width={28}
            height={28}
            className="rounded-full"
          />
          <span>{article.author.name}</span>
          <span>•</span>
          <time dateTime={article.date}>{article.dateLabel}</time>
          <span>• {article.readTime} min</span>
        </div>

        <div className="mt-4 flex items-center justify-between border-t border-slate-100 pt-4 text-xs text-slate-500">
          <span>{article.views.toLocaleString('fr-FR')} vues • {article.commentsCount} commentaires</span>
          <button
            type="button"
            onClick={onShare}
            className="rounded-full border border-slate-200 px-3 py-1.5 font-medium text-slate-700 transition hover:bg-slate-100"
          >
            Partager
          </button>
        </div>
      </div>
    </article>
  );
}
