# Deploy

## Checklist

1. Point the web server document root to `public/`
2. Copy `.env.example` → `.env` and set production values
3. Set `APP_DEBUG=false`
4. Set `APP_URL` to your public URL
5. Optionally bump `ASSET_VERSION`
6. Install dependencies and migrate:

```bash
php composer.phar install --no-dev --optimize-autoloader
php console migrate
```

7. Ensure `storage/cache` and `storage/database` are writable by PHP

## Composer on the server

Commit `composer.json` and `composer.lock`. Do **not** commit `vendor/`.

```bash
php composer.phar install --no-dev --optimize-autoloader
php composer.phar dump-autoload -o
```

Full command list: [SERVER.md](../SERVER.md).

## After code updates

- PHP views/controllers: no compile step — deploy files and you’re done
- CSS/JS/images: `asset()` versioning handles browser cache
- Cached app data: `Cache::flush()` if you used `Cache::remember` in production

## Security

- Keep `.env` out of the web root (never inside `public/`)
- Do not expose `storage/`, `vendor/`, or `core/` as document roots
