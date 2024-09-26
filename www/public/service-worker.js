const CACHE_NAME = 'my-pwa-cache-v1';
const urlsToCache = [
  '/public/',
  '/public/index.php',
  '/public/css/style.css',
  '/public/css/font-awesome.css',
  '/public/css/font-awesome.min.css',
  '/public/css/font-circle-video.css',
  '/public/js/custom.js',
  '/public/js/jquery.min.js',
  '/public/images/account.jpg',
  '/public/images/add-article.jpg',
  '/public/images/favicon.ico',
  '/public/images/logo.png',
  '/public/bootstrap/css/font-awesome.css',
  '/public/bootstrap/js/bootstrap.min.js',
  '/public/fonts/circle-video.eot',
  '/public/fonts/circle-video.svg',
  '/public/fonts/circle-video.ttf',
  '/public/fonts/circle-video.woff',
  '/public/fonts/circle-video.woff2',
  '/public/fonts/fontawesome-webfont.eot',
  '/public/fonts/fontawesome-webfont.svg',
  '/public/fonts/fontawesome-webfont.ttf',
  '/public/fonts/fontawesome-webfont.woff',
  '/public/fonts/fontawesome-webfont.woff2',
  '/public/fonts/FontAwesome.otf',
  '/public/index.php',
  '/public/manifest.json',
  '/public/service-worker.js',
];

// Install event - Cache necessary files
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        return cache.addAll(urlsToCache);
      })
  );
});

// Fetch event - Serve cached content when offline
self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request)
      .then((response) => {
        return response || fetch(event.request);
      })
  );
});

// Activate event - Clean up old caches
self.addEventListener('activate', (event) => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then((keyList) =>
      Promise.all(keyList.map((key) => {
        if (!cacheWhitelist.includes(key)) {
          return caches.delete(key);
        }
      }))
    )
  );
});
