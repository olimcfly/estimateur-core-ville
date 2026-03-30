import { NextResponse } from 'next/server';

const accessLog = [];
const rateLimitStore = new Map();
const WINDOW_MS = 60 * 1000;
const MAX_REQUESTS_PER_WINDOW = 60;

const getClientIp = (request) =>
  request.headers.get('x-forwarded-for')?.split(',')[0]?.trim() ||
  request.ip ||
  'unknown';

export function middleware(request) {
  const { pathname } = request.nextUrl;

  if (!pathname.startsWith('/admin')) {
    return NextResponse.next();
  }

  const ip = getClientIp(request);
  const now = Date.now();
  const previousHits = rateLimitStore.get(ip) || [];
  const recentHits = previousHits.filter((timestamp) => now - timestamp < WINDOW_MS);
  recentHits.push(now);
  rateLimitStore.set(ip, recentHits);

  accessLog.push({ ip, pathname, at: new Date(now).toISOString() });
  if (accessLog.length > 1000) accessLog.shift();

  if (recentHits.length > MAX_REQUESTS_PER_WINDOW) {
    return NextResponse.json({ message: 'Trop de requêtes. Réessayez plus tard.' }, { status: 429 });
  }

  const role = request.cookies.get('user_role')?.value || request.headers.get('x-user-role');

  if (role !== 'ADMIN') {
    const redirectUrl = new URL('/?unauthorized=admin', request.url);
    return NextResponse.redirect(redirectUrl);
  }

  const response = NextResponse.next();
  response.headers.set('x-admin-access-log-size', String(accessLog.length));
  return response;
}

export const config = {
  matcher: ['/admin/:path*'],
};
