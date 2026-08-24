/* ----------------------------------------------------------------------------
 * Timecrack - Time Tracking Application
 *
 * @package     Timecrack
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://github.com/alextselegidis/timecrack
 * ---------------------------------------------------------------------------- */

/**
 * The cache is named after the "v" query parameter of the registration URL, which carries the
 * application version. A new release therefore installs a new service worker and drops the
 * caches of the previous one, without any manual bookkeeping in this file.
 */
const CACHE_NAME = 'timecrack-' + (new URL(self.location.href).searchParams.get('v') || 'dev');

const SCOPE = new URL('./', self.location.href);

const OFFLINE_URL = new URL('offline.html', SCOPE).href;

const PRECACHE_URLS = [
    OFFLINE_URL,
    'vendor/bootstrap/bootstrap.min.css',
    'vendor/bootstrap/bootstrap.bundle.min.js',
    'vendor/bootstrap-icons/bootstrap-icons.min.css',
    'vendor/bootstrap-icons/fonts/bootstrap-icons.woff2',
    'vendor/pace-js/pace-theme-default.min.css',
    'vendor/pace-js/pace-theme-flat-top.tmpl.css',
    'vendor/pace-js/pace.min.js',
    'styles/timecrack.css',
    'scripts/timecrack.js',
    'images/logo.png',
    'images/icon-192.png',
].map((path) => new URL(path, SCOPE).href);

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches
            .open(CACHE_NAME)
            // Individual requests are added one by one, so a single missing file does not
            // abort the whole installation.
            .then((cache) => Promise.all(PRECACHE_URLS.map((url) => cache.add(url).catch(() => null))))
            .then(() => self.skipWaiting()),
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches
            .keys()
            .then((names) => Promise.all(names.filter((name) => name !== CACHE_NAME).map((name) => caches.delete(name))))
            .then(() => self.clients.claim()),
    );
});

self.addEventListener('fetch', (event) => {
    const request = event.request;

    if (request.method !== 'GET' || !request.url.startsWith(SCOPE.href)) {
        return;
    }

    // Pages always come from the network, they depend on the session and must never be shared
    // between users. Only the offline placeholder is served from the cache.
    if (request.mode === 'navigate') {
        event.respondWith(fetch(request).catch(() => caches.match(OFFLINE_URL)));

        return;
    }

    // Static assets are versioned through their query string, so a cache hit is always current.
    event.respondWith(
        caches.match(request).then(
            (cached) =>
                cached ||
                fetch(request).then((response) => {
                    if (response.ok && response.type === 'basic') {
                        const copy = response.clone();

                        caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
                    }

                    return response;
                }),
        ),
    );
});
