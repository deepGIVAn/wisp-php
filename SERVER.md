# Wisp — server cheat sheet

## Composer

```bash
# First time / after git pull
php composer.phar install

# Production (recommended on live)
php composer.phar install --no-dev --optimize-autoloader

# Add a library
php composer.phar require vendor/package

# Remove a library
php composer.phar remove vendor/package

# Update everything
php composer.phar update

# Update one package
php composer.phar update vendor/package

# After you add new App/Core classes
php composer.phar dump-autoload -o
```

## Database (no DB yet on server is fine)

```bash
# Creates SQLite file OR MySQL database from .env, then runs migrations
php console migrate

# Only create empty DB (no tables yet)
php console db:create

# See which migrations ran / are pending
php console migrate:status

# Undo last batch
php console migrate:rollback

# Drop migrated tables and run all again (destructive)
php console migrate:fresh

# Create a new migration file
php console make:migration create_posts_table
```

See also: [docs/installation.md](docs/installation.md), [docs/database.md](docs/database.md), [docs/deploy.md](docs/deploy.md).
