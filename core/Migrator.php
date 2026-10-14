<?php

namespace Core;

use PDO;

class Migrator
{
    private string $path;

    public function __construct(?string $path = null)
    {
        $this->path = $path ?? base_path('database/migrations');
    }

    public function ensureDatabase(): void
    {
        $config = config('database');
        $driver = $config['connection'] ?? 'sqlite';

        if ($driver === 'sqlite') {
            $database = $config['sqlite']['database'];
            $dir = dirname($database);
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            // Connecting creates the empty SQLite file
            Database::connection();
            $this->writeln('SQLite database ready: ' . $database);
            return;
        }

        $mysql = $config['mysql'];
        $dsn = sprintf(
            'mysql:host=%s;port=%s;charset=%s',
            $mysql['host'],
            $mysql['port'],
            $mysql['charset']
        );

        $pdo = new PDO($dsn, $mysql['username'], $mysql['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        $name = str_replace('`', '``', $mysql['database']);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $this->writeln("MySQL database ready: {$mysql['database']}");

        // Reset so next connection uses the new database
        Database::reset();
        Database::connection();
    }

    public function migrate(): void
    {
        $this->ensureDatabase();
        $this->ensureMigrationsTable();

        $pending = $this->pendingMigrations();
        if ($pending === []) {
            $this->writeln('Nothing to migrate.');
            return;
        }

        $batch = $this->nextBatch();

        foreach ($pending as $file) {
            $name = basename($file, '.php');
            $migration = $this->resolve($file);
            $migration->up();

            Database::query(
                'INSERT INTO migrations (migration, batch) VALUES (?, ?)',
                [$name, $batch]
            );

            $this->writeln("Migrated: {$name}");
        }

        $this->writeln('Done.');
    }

    public function rollback(): void
    {
        $this->ensureDatabase();
        $this->ensureMigrationsTable();

        $batch = $this->lastBatch();
        if ($batch === null) {
            $this->writeln('Nothing to rollback.');
            return;
        }

        $rows = Database::query(
            'SELECT migration FROM migrations WHERE batch = ? ORDER BY id DESC',
            [$batch]
        )->fetchAll();

        foreach ($rows as $row) {
            $name = $row['migration'];
            $file = $this->path . DIRECTORY_SEPARATOR . $name . '.php';

            if (!is_file($file)) {
                throw new \RuntimeException("Migration file missing: {$name}");
            }

            $migration = $this->resolve($file);
            $migration->down();

            Database::query('DELETE FROM migrations WHERE migration = ?', [$name]);
            $this->writeln("Rolled back: {$name}");
        }

        $this->writeln('Done.');
    }

    public function fresh(): void
    {
        $this->ensureDatabase();
        $this->ensureMigrationsTable();

        $ran = Database::query(
            'SELECT migration FROM migrations ORDER BY id DESC'
        )->fetchAll();

        foreach ($ran as $row) {
            $name = $row['migration'];
            $file = $this->path . DIRECTORY_SEPARATOR . $name . '.php';
            if (is_file($file)) {
                $this->resolve($file)->down();
                $this->writeln("Dropped via: {$name}");
            }
        }

        Database::query('DELETE FROM migrations');
        $this->migrate();
    }

    public function status(): void
    {
        $this->ensureDatabase();
        $this->ensureMigrationsTable();

        $ran = [];
        foreach (Database::query('SELECT migration, batch FROM migrations')->fetchAll() as $row) {
            $ran[$row['migration']] = $row['batch'];
        }

        $files = $this->migrationFiles();
        if ($files === []) {
            $this->writeln('No migration files found.');
            return;
        }

        $this->writeln(str_pad('Migration', 50) . 'Batch / Status');
        $this->writeln(str_repeat('-', 70));

        foreach ($files as $file) {
            $name = basename($file, '.php');
            if (isset($ran[$name])) {
                $this->writeln(str_pad($name, 50) . 'Ran (batch ' . $ran[$name] . ')');
            } else {
                $this->writeln(str_pad($name, 50) . 'Pending');
            }
        }
    }

    public function make(string $name): string
    {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', trim($name)) ?? 'migration');
        $slug = trim($slug, '_');
        if ($slug === '') {
            $slug = 'migration';
        }

        if (!is_dir($this->path)) {
            mkdir($this->path, 0775, true);
        }

        $filename = date('Y_m_d_His') . '_' . $slug . '.php';
        $full = $this->path . DIRECTORY_SEPARATOR . $filename;

        $stub = <<<'PHP'
<?php

use Core\Database;
use Core\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Database::query("
            CREATE TABLE IF NOT EXISTS example (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                created_at DATETIME NULL
            )
        ");
    }

    public function down(): void
    {
        Database::query('DROP TABLE IF EXISTS example');
    }
};

PHP;

        // MySQL-friendly note in stub comments via dual-compatible SQL where possible
        file_put_contents($full, $stub);
        $this->writeln("Created: database/migrations/{$filename}");
        $this->writeln('Edit up()/down() — for MySQL use AUTO_INCREMENT instead of AUTOINCREMENT.');

        return $full;
    }

    private function ensureMigrationsTable(): void
    {
        $driver = config('database.connection', 'sqlite');

        if ($driver === 'mysql') {
            Database::query("
                CREATE TABLE IF NOT EXISTS migrations (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    migration VARCHAR(255) NOT NULL,
                    batch INT NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            return;
        }

        Database::query("
            CREATE TABLE IF NOT EXISTS migrations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                migration TEXT NOT NULL,
                batch INTEGER NOT NULL
            )
        ");
    }

    /** @return list<string> */
    private function migrationFiles(): array
    {
        if (!is_dir($this->path)) {
            return [];
        }

        $files = glob($this->path . DIRECTORY_SEPARATOR . '*.php') ?: [];
        sort($files);
        return $files;
    }

    /** @return list<string> */
    private function pendingMigrations(): array
    {
        $ran = [];
        foreach (Database::query('SELECT migration FROM migrations')->fetchAll() as $row) {
            $ran[$row['migration']] = true;
        }

        $pending = [];
        foreach ($this->migrationFiles() as $file) {
            $name = basename($file, '.php');
            if (!isset($ran[$name])) {
                $pending[] = $file;
            }
        }

        return $pending;
    }

    private function nextBatch(): int
    {
        $row = Database::query('SELECT MAX(batch) AS m FROM migrations')->fetch();
        return ((int) ($row['m'] ?? 0)) + 1;
    }

    private function lastBatch(): ?int
    {
        $row = Database::query('SELECT MAX(batch) AS m FROM migrations')->fetch();
        $batch = $row['m'] ?? null;
        return $batch === null ? null : (int) $batch;
    }

    private function resolve(string $file): Migration
    {
        $migration = require $file;
        if (!$migration instanceof Migration) {
            throw new \RuntimeException("Migration must return a Migration instance: {$file}");
        }
        return $migration;
    }

    private function writeln(string $message): void
    {
        echo $message . PHP_EOL;
    }
}
