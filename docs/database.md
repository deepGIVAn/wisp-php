# Database & migrations

Wisp uses PDO. Configure the driver in `.env`.

## SQLite (default)

```env
DB_CONNECTION=sqlite
DB_DATABASE=storage/database/app.sqlite
```

`php console migrate` creates the file if it does not exist.

## MySQL

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=wisp
DB_USER=root
DB_PASS=
```

On a fresh server with **no database yet**, `php console migrate` runs `CREATE DATABASE IF NOT EXISTS` then applies migrations.

## Console commands

| Command | What it does |
|---------|----------------|
| `php console migrate` | Create DB if missing + run pending migrations |
| `php console db:create` | Only create empty DB |
| `php console migrate:status` | List ran / pending |
| `php console migrate:rollback` | Undo last batch |
| `php console migrate:fresh` | Drop migrated tables & re-run (destructive) |
| `php console make:migration name` | New migration stub |

## Writing a migration

```bash
php console make:migration create_posts_table
```

Edit `up()` / `down()` in `database/migrations/`. Ship separate SQL for SQLite vs MySQL when types differ (see the included `users` migration).

## Models

```php
namespace App\Models;

use Core\Model;

class User extends Model
{
    protected static string $table = 'users';
}
```

```php
User::all();
User::find(1);
User::where('email', 'ada@example.com');
User::create(['name' => 'Ada', 'email' => 'ada@example.com']);
User::update(1, ['name' => 'Augusta']);
User::delete(1);
```

Raw queries: `Core\Database::query($sql, $params)`.

Next: [Caching](caching.md) · [Deploy](deploy.md)
