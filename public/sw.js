const CACHE_NAME = 'pos-app-v' + 'APP_VERSION_PLACEHOLDER';
const RUNTIME_CACHE = 'pos-runtime-v' + 'APP_VERSION_PLACEHOLDER';

// Install event - skip pre-caching, let runtime handle it
self.addEventListener('install', (event) => {
  console.log('[Service Worker] Installing...', CACHE_NAME);
  // Skip waiting to activate immediately
  self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  console.log('[Service Worker] Activating...');
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          // Delete old caches that don't match current version
          if (cacheName !== CACHE_NAME && cacheName !== RUNTIME_CACHE) {
            console.log('[Service Worker] Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  return self.clients.claim();
});

// Fetch event - cache CSS/JS files automatically
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Only cache GET requests
  if (request.method !== 'GET') {
    return;
  }

  // Skip cross-origin requests
  if (url.origin !== location.origin) {
    return;
  }

  // Auto-cache CSS and JS files
  if (url.pathname.match(/\.(css|js)$/)) {
    event.respondWith(
      caches.match(request).then((response) => {
        // Return cached version if available
        if (response) {
          return response;
        }

        // Fetch from network
        return fetch(request).then((response) => {
          // Don't cache if not a valid response
          if (!response || response.status !== 200 || response.type !== 'basic') {
            return response;
          }

          // Clone the response for caching
          const responseToCache = response.clone();

          // Cache in runtime cache
          caches.open(RUNTIME_CACHE).then((cache) => {
            cache.put(request, responseToCache);
          });

          return response;
        }).catch((err) => {
          console.error('[Service Worker] Fetch failed:', err);
          return response;
        });
      })
    );
  }
  // For other requests, try network first, fallback to cache
  else {
    event.respondWith(
      fetch(request)
        .then((response) => {
          // Cache successful responses
          if (response && response.status === 200) {
            const responseToCache = response.clone();
            caches.open(RUNTIME_CACHE).then((cache) => {
              cache.put(request, responseToCache);
            });
          }
          return response;
        })
        .catch(() => {
          // Fallback to cache if network fails
          return caches.match(request);
        })
    );
  }
});
