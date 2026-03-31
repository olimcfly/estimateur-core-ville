import BlogListingClient from '../../components/blog/BlogListingClient';
import { generateMetadata as buildMetadata } from '../../lib/seo';

export const revalidate = 86400;

export function generateMetadata() {
  return buildMetadata('blog', {
    canonical: '/blog',
  });
}

export default function BlogPage() {
  return <BlogListingClient />;
}
