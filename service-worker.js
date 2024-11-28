const CACHE_NAME = 'travel-attractions-v1';
const ASSETS_TO_CACHE = [
    '/',
    '/index.html',
    '/manifest.json',
    '/offline.html',
    '/css/main.css',
    '/js/app.js',
    '/images/logo-192x192.png',
    '/images/logo-512x512.png'
];

// Install event - cache static assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('Opened cache');
                return cache.addAll(ASSETS_TO_CACHE);
            })
            .catch((error) => {
                console.error('Cache installation error:', error);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Fetch event - handle network requests
self.addEventListener('fetch', (event) => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') return;

    event.respondWith(
        caches.match(event.request)
            .then((response) => {
                // Cache hit - return response
                if (response) {
                    return response;
                }

                // IMPORTANT: Clone the request. A request is a stream and can only be consumed once
                const fetchRequest = event.request.clone();

                return fetch(fetchRequest)
                    .then((response) => {
                        // Check if we received a valid response
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }

                        // IMPORTANT: Clone the response. A response is a stream and can only be consumed once
                        const responseToCache = response.clone();

                        caches.open(CACHE_NAME)
                            .then((cache) => {
                                cache.put(event.request, responseToCache);
                            });

                        return response;
                    })
                    .catch(() => {
                        // If network fails and it's an HTML request, return offline page
                        if (event.request.headers.get('Accept').includes('text/html')) {
                            return caches.match('/offline.html');
                        }
                    });
            })
    );
});

// Push notification handler (optional)
self.addEventListener('push', (event) => {
    const title = 'Travel Attractions Update';
    const options = {
        body: event.data.text(),
        icon: '/images/logo-192x192.png',
        badge: '/images/logo-192x192.png'
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Background sync for offline actions (optional)
self.addEventListener('sync', (event) => {
    if (event.tag === 'upload-media') {
        event.waitUntil(uploadQueuedMedia());
    }
});

// Optional function for background sync
async function uploadQueuedMedia() {
    // Implement logic to upload queued media from IndexedDB or localStorage
    try {
        // Retrieve queued uploads
        // Attempt to upload
        // Remove successful uploads from queue
    } catch (error) {
        console.error('Background sync failed', error);
    }
}