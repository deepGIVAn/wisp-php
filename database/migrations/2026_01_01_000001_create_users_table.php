<?php

use Core\Database;
use Core\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $driver = config('database.connection', 'sqlite');

        if ($driver === 'mysql') {
            Database::query("
                CREATE TABLE IF NOT EXISTS users (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL UNIQUE,
                    password VARCHAR(255) NULL,
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            return;
        }

        Database::query("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password TEXT NULL,
                created_at TEXT NULL,
                updated_at TEXT NULL
            )
        ");
    }

    public function down(): void
    {
        Database::query('DROP TABLE IF EXISTS users');
    }
};
