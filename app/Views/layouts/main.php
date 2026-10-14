<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(($title ?? 'App') . ' — ' . config('app.name')) ?></title>
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body>
    <header class="site-header">
        <a class="brand" href="/"><?= e(config('app.name')) ?></a>
        <nav>
            <a href="/">Home</a>
            <a href="/about">About</a>
        </nav>
    </header>

    <main class="site-main">
        <?= $content ?? '' ?>
    </main>

    <footer class="site-footer">
        <small>&copy; <?= date('Y') ?> <?= e(config('app.name')) ?></small>
    </footer>

    <script src="<?= e(asset('js/app.js')) ?>" defer></script>
</body>
</html>
