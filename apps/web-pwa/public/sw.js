/* eslint-disable no-undef */
const CACHE_NAME = 'haida-hub-static-v1';
const STATIC_ASSETS = ['/', '/manifest.json', '/icon.svg'];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS);
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key)));
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  const { request } = event;
  if (request.method !== 'GET') {
    return;
  }

  event.respondWith(
    caches.match(request).then((cached) => {
      if (cached) {
        return cached;
      }
      return fetch(request).then((response) => {
        const copy = response.clone();
        if (request.url.startsWith(self.location.origin)) {
          caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
        }
        return response;
      });
    })
  );
});

try {
  importScripts('https://storage.googleapis.com/workbox-cdn/releases/6.5.4/workbox-sw.js');
  if (self.workbox && workbox.backgroundSync) {
    const queue = new workbox.backgroundSync.Queue('app-sync-queue', {
      maxRetentionTime: 24 * 60
    });

    self.addEventListener('fetch', (event) => {
      const { request } = event;
      if (request.method === 'POST' && request.url.includes('/api/v1/app/sync/push')) {
        event.respondWith(
          fetch(request.clone()).catch(() => {
            return queue.pushRequest({ request });
          })
        );
      }
    });

    self.addEventListener('sync', (event) => {
      if (event.tag === 'app-sync') {
        event.waitUntil(queue.replayRequests());
      }
    });
  }
} catch (error) {
  // Workbox unavailable; fallback to in-app online sync.
}
