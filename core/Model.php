<?php

namespace Core;

abstract class Model
{
    protected static string $table;
    protected static string $primaryKey = 'id';

    public static function all(): array
    {
        $table = static::table();
        return Database::query("SELECT * FROM {$table}")->fetchAll();
    }

    public static function find(int|string $id): ?array
    {
        $table = static::table();
        $pk = static::$primaryKey;
        $row = Database::query("SELECT * FROM {$table} WHERE {$pk} = ?", [$id])->fetch();
        return $row ?: null;
    }

    public static function where(string $column, mixed $value): array
    {
        $table = static::table();
        return Database::query("SELECT * FROM {$table} WHERE {$column} = ?", [$value])->fetchAll();
    }

    public static function create(array $data): int|string
    {
        $table = static::table();
        $columns = array_keys($data);
        $placeholders = array_map(fn ($c) => ':' . $c, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        Database::query($sql, $data);
        return Database::connection()->lastInsertId();
    }

    public static function update(int|string $id, array $data): bool
    {
        $table = static::table();
        $pk = static::$primaryKey;
        $sets = [];
        foreach (array_keys($data) as $column) {
            $sets[] = "{$column} = :{$column}";
        }

        $data[$pk] = $id;
        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s = :%s',
            $table,
            implode(', ', $sets),
            $pk,
            $pk
        );

        return Database::query($sql, $data)->rowCount() > 0;
    }

    public static function delete(int|string $id): bool
    {
        $table = static::table();
        $pk = static::$primaryKey;
        return Database::query("DELETE FROM {$table} WHERE {$pk} = ?", [$id])->rowCount() > 0;
    }

    protected static function table(): string
    {
        if (!empty(static::$table)) {
            return static::$table;
        }

        $class = (new \ReflectionClass(static::class))->getShortName();
        return strtolower($class) . 's';
    }
}
