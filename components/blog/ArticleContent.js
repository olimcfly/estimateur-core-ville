import Image from 'next/image';

export default function ArticleContent({ content = '', images = [] }) {
  return (
    <div className="prose prose-slate max-w-none prose-headings:scroll-mt-28 prose-h1:text-4xl prose-h2:mt-10 prose-h2:border-b prose-h2:border-slate-200 prose-h2:pb-3 prose-h3:mt-8 prose-blockquote:border-l-4 prose-blockquote:border-blue-400 prose-blockquote:bg-blue-50 prose-blockquote:py-3 prose-blockquote:pl-4 prose-code:rounded prose-code:bg-slate-100 prose-code:px-1 prose-code:py-0.5">
      {images?.length > 0 && (
        <figure className="not-prose mb-8 overflow-hidden rounded-2xl border border-slate-200 bg-white">
          <div className="relative aspect-[16/8] w-full">
            <Image
              src={images[0].src}
              alt={images[0].alt}
              fill
              priority
              sizes="100vw"
              className="object-cover"
            />
          </div>
          {images[0].caption ? (
            <figcaption className="px-4 py-2 text-sm text-slate-500">{images[0].caption}</figcaption>
          ) : null}
        </figure>
      )}

      <div dangerouslySetInnerHTML={{ __html: content }} />

      <div className="not-prose mt-8 rounded-2xl border border-amber-300 bg-amber-50 p-5 text-sm text-amber-900">
        <p className="font-semibold">💡 Conseil pro</p>
        <p className="mt-1">Comparez toujours au moins 3 estimations avant de fixer votre prix de vente.</p>
      </div>
    </div>
  );
}
