# Caching

## Application cache (file)

Files land in `storage/cache/`. Configure TTL in `.env`:

```env
CACHE_DRIVER=file
CACHE_TTL=3600
APP_DEBUG=true
```

```php
use Core\Cache;

$value = Cache::remember('home.stats', 600, function () {
    return expensiveQuery();
});

Cache::put('key', $data, 300);
Cache::get('key');
Cache::forget('key');
Cache::flush();
```

When `APP_DEBUG=true`, `Cache::remember()` always runs the callback so local work stays fresh.

## Asset browser caching

Static files under `public/assets/` get long `Cache-Control` (via `.htaccess` or [public/router.php](../public/router.php) for the built-in server).

`asset('css/app.css')` adds a version query string so deploying a changed file (or bumping `ASSET_VERSION`) busts the URL without fighting the long cache.

## Deploy tip

On production after a data-cache-heavy release:

```php
Cache::flush();
```

Or delete files under `storage/cache/` (keep the folder).

Next: [Deploy](deploy.md)
