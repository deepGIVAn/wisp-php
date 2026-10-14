# Views & components

Views are plain PHP under `app/Views/`. No template compiler — edits show up immediately.

## Render from a controller

```php
return $this->view('pages/home', [
    'title' => 'Welcome',
    'message' => 'Hello',
]);
```

That renders `app/Views/pages/home.php` inside `app/Views/layouts/main.php` by default.

Skip the layout:

```php
return $this->view('pages/home', $data, null);
```

## Layouts

Layouts receive `$content` (rendered page HTML) plus any data you pass. See [app/Views/layouts/main.php](../app/Views/layouts/main.php).

## Escape output

```php
<?= e($title) ?>
```

## Components

```php
<?= component('alert', [
    'type' => 'success',
    'text' => 'Saved',
]) ?>
```

Components live in `app/Views/components/` (e.g. `alert.php`).

## Assets

```php
<link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
```

`asset()` points at `public/assets/...` and appends `?v=` from file mtime (or `ASSET_VERSION` in `.env`) so browsers pick up updates even with long cache headers.

Next: [Caching](caching.md)
