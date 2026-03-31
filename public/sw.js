const STATIC_CACHE = 'immoville-static-v1';
const IMAGE_CACHE = 'immoville-images-v1';
const FONT_CACHE = 'immoville-fonts-v1';
const API_CACHE = 'immoville-api-v1';
const API_TTL_MS = 1000 * 60 * 5;
const IMAGE_TTL_MS = 1000 * 60 * 60 * 24 * 7;

const PRECACHE_URLS = ['/', '/offline.html', '/manifest.json', '/favicon.ico', '/favoris'];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(STATIC_CACHE).then((cache) => cache.addAll(PRECACHE_URLS)).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((key) => ![STATIC_CACHE, IMAGE_CACHE, FONT_CACHE, API_CACHE].includes(key))
          .map((key) => caches.delete(key))
      )
    ).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const request = event.request;
  const url = new URL(request.url);

  if (request.method !== 'GET') return;

  if (url.pathname.startsWith('/api/biens')) {
    event.respondWith(networkFirstWithTtl(request, API_CACHE, API_TTL_MS));
    return;
  }

  if (request.destination === 'image') {
    event.respondWith(cacheFirstWithTtl(request, IMAGE_CACHE, IMAGE_TTL_MS));
    return;
  }

  if (request.destination === 'font') {
    event.respondWith(cacheFirst(request, FONT_CACHE));
    return;
  }

  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .then((response) => {
          const copy = response.clone();
          caches.open(STATIC_CACHE).then((cache) => cache.put(request, copy));
          return response;
        })
        .catch(async () => (await caches.match(request)) || caches.match('/offline.html'))
    );
    return;
  }

  event.respondWith(cacheFirst(request, STATIC_CACHE));
});

async function cacheFirst(request, cacheName) {
  const cached = await caches.match(request);
  if (cached) return cached;
  const response = await fetch(request);
  const cache = await caches.open(cacheName);
  cache.put(request, response.clone());
  return response;
}

async function cacheFirstWithTtl(request, cacheName, ttl) {
  const cache = await caches.open(cacheName);
  const cached = await cache.match(request);

  if (cached) {
    const cachedAt = Number(cached.headers.get('sw-cache-time') || 0);
    if (Date.now() - cachedAt < ttl) {
      return cached;
    }
  }

  try {
    const response = await fetch(request);
    await cache.put(request, withTimestamp(response));
    return response;
  } catch {
    return cached || new Response('Offline', { status: 503 });
  }
}

async function networkFirstWithTtl(request, cacheName, ttl) {
  const cache = await caches.open(cacheName);

  try {
    const response = await fetch(request);
    await cache.put(request, withTimestamp(response));
    return response;
  } catch {
    const cached = await cache.match(request);
    if (!cached) return new Response('Offline', { status: 503 });

    const cachedAt = Number(cached.headers.get('sw-cache-time') || 0);
    if (Date.now() - cachedAt > ttl) {
      return new Response('Stale cache expired', { status: 504 });
    }

    return cached;
  }
}

async function withTimestamp(response) {
  const body = await response.blob();
  const headers = new Headers(response.headers);
  headers.set('sw-cache-time', String(Date.now()));
  return new Response(body, {
    status: response.status,
    statusText: response.statusText,
    headers,
  });
}

self.addEventListener('sync', (event) => {
  if (event.tag === 'sync-offline-forms') {
    event.waitUntil(replayQueuedForms());
  }
});

async function replayQueuedForms() {
  const clients = await self.clients.matchAll({ includeUncontrolled: true });
  clients.forEach((client) => client.postMessage({ type: 'OFFLINE_FORMS_SYNCED' }));
}

self.addEventListener('push', (event) => {
  const payload = event.data?.json() || {
    title: 'Nouvelle alerte immobilière',
    body: 'Des annonces correspondant à vos critères sont disponibles.',
    url: '/',
    topic: 'alertes personnalisées',
  };

  event.waitUntil(
    self.registration.showNotification(payload.title, {
      body: `${payload.body} (${payload.topic || 'nouvelles annonces'})`,
      icon: '/icons/icon-192.png',
      badge: '/icons/icon-72.png',
      data: { url: payload.url || '/' },
      tag: payload.topic || 'nouvelles-annonces',
    })
  );
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  const destination = event.notification.data?.url || '/';

  event.waitUntil(
    self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clients) => {
      for (const client of clients) {
        if ('focus' in client) {
          client.navigate(destination);
          return client.focus();
        }
      }
      return self.clients.openWindow(destination);
    })
  );
});

self.addEventListener('message', (event) => {
  if (event.data?.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }

  if (event.data?.type === 'PUSH_SUBSCRIBE') {
    event.waitUntil(handlePushSubscription(event.data.payload));
  }
});

async function handlePushSubscription(payload) {
  // Placeholder to wire with backend subscription endpoint.
  const clients = await self.clients.matchAll({ includeUncontrolled: true });
  clients.forEach((client) => client.postMessage({ type: 'PUSH_SUBSCRIBED', payload }));
}
