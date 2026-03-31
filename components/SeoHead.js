export default function SeoHead({ jsonLd }) {
  if (!jsonLd) return null;

  return <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }} />;
}
