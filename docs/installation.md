# Installation

## Requirements

- PHP **8.1+** (PDO, mbstring recommended)
- Composer (`composer` or `php composer.phar`)
- SQLite (default) or MySQL

## Get the project

```bash
git clone https://github.com/deepgivan/wisp-php.git
cd wisp-php
cp .env.example .env
php composer.phar install
```

If Composer is installed globally:

```bash
composer install
```

## Run locally

Document root must be `public/`:

```bash
php -S localhost:8000 -t public public/router.php
```

Open [http://localhost:8000](http://localhost:8000).

Create the database and tables:

```bash
php console migrate
```

## Apache

Point the vhost document root to `public/`. [public/.htaccess](../public/.htaccess) rewrites requests to `index.php` and sets long cache headers for static assets.

## Nginx (sketch)

```nginx
root /var/www/wisp-php/public;
index index.php;

location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    include fastcgi_params;
    fastcgi_pass unix:/run/php/php8.2-fpm.sock; # adjust
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}
```

## Autoload fallback

After `composer install`, Wisp uses `vendor/autoload.php`. If `vendor/` is missing, [bootstrap.php](../bootstrap.php) falls back to the built-in PSR-4 autoloader so the app can still boot.

Next: [Routing](routing.md) · [Database](database.md)
