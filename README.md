<p align="center">
  <img src="docs/assets/wisp-logo.jpg" alt="Wisp logo" width="160" />
</p>

<h1 align="center">Wisp</h1>

<p align="center">
  <strong>A barely-there PHP micro-framework</strong><br />
  Routes, views, models, cache — nothing you don’t need.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP 8.1+" />
  <img src="https://img.shields.io/badge/license-MIT-0f766e?style=flat-square" alt="MIT License" />
  <img src="https://img.shields.io/badge/composer-wisp%2Fframework-1c1917?style=flat-square&logo=composer&logoColor=white" alt="Composer" />
  <img src="https://img.shields.io/badge/database-SQLite%20%7C%20MySQL-0f766e?style=flat-square" alt="SQLite and MySQL" />
</p>

<p align="center">
  <img src="docs/assets/wisp-banner.jpg" alt="Wisp — barely-there PHP" width="100%" />
</p>

---

## Why Wisp?

Laravel is great — until you only need a router, a few controllers, and a database. Wisp boots in a handful of files, ships migrations and `.env`, and stays out of your way.

- **Tiny boot** — no service container circus, no compiled templates
- **Familiar shape** — routes → controllers → views / models
- **SQLite or MySQL** — PDO models + `php console migrate` (creates the DB if missing)
- **Components** — reusable view pieces without a templating language
- **Smart assets** — long browser cache + automatic `?v=` busting
- **Composer-ready** — manage libraries with `composer.phar` on the server

## Features

| Area | What you get |
|------|----------------|
| HTTP | Router (`GET`/`POST`/`PUT`/`DELETE`), path params, JSON responses |
| App | Controllers, views, layouts, components |
| Data | PDO models, SQLite / MySQL, migration CLI |
| Config | `.env` loader, `config/*.php` |
| Cache | File cache + asset `Cache-Control` |
| DX | Custom autoload fallback if `vendor/` isn’t installed yet |

## Quick start

```bash
git clone https://github.com/YOUR_USER/wisp-php.git
cd wisp-php
cp .env.example .env
php composer.phar install   # or: composer install
php console migrate
php -S localhost:8000 -t public public/router.php
```

Open **http://localhost:8000** — you’re in.

## Hello world

**Route** — `routes/web.php`

```php
use App\Controllers\HomeController;

$router->get('/', [HomeController::class, 'index']);
```

**Controller** — `app/Controllers/HomeController.php`

```php
public function index(Request $request): Response
{
    return $this->view('pages/home', [
        'title' => 'Welcome',
        'message' => 'Wisp is ready.',
    ]);
}
```

**View** — `app/Views/pages/home.php`

```php
<h1><?= e($title) ?></h1>
<p><?= e($message) ?></p>

<?= component('alert', [
    'type' => 'success',
    'text' => 'Barely-there and wired up.',
]) ?>
```

## Project layout

```text
wisp-php/
├── public/              # Web root (index.php, assets)
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Views/           # pages, layouts, components
│   └── Helpers/
├── core/                # Framework kernel
├── routes/web.php
├── config/
├── database/migrations/
├── storage/             # cache + sqlite
├── console              # CLI (migrate, make:migration, …)
└── docs/
```

## Documentation

| Guide | Topic |
|-------|--------|
| [Installation](docs/installation.md) | Requirements, Composer, Apache / Nginx |
| [Routing](docs/routing.md) | Routes, params, controllers |
| [Views](docs/views.md) | Layouts, components, `e()`, `asset()` |
| [Database](docs/database.md) | SQLite / MySQL, migrate commands, models |
| [Caching](docs/caching.md) | File cache + browser assets |
| [Deploy](docs/deploy.md) | Production checklist |
| [SERVER.md](SERVER.md) | Short server command cheat sheet |

## Composer (short)

```bash
php composer.phar install --no-dev --optimize-autoloader   # production
php composer.phar require vendor/package                  # add a library
php composer.phar dump-autoload -o                        # refresh classmap
```

Commit `composer.json` + `composer.lock`. Do not commit `vendor/`.

## Migrations (short)

```bash
php console migrate              # create DB if needed + migrate
php console migrate:status
php console make:migration create_posts_table
```

Full list → [docs/database.md](docs/database.md).

## License

Wisp is open source under the [MIT License](LICENSE).

---

<p align="center">
  <sub>Wisp — barely-there PHP. Ship small apps without the weight.</sub>
</p>
