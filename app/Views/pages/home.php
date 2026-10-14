<section class="hero">
    <h1><?= e($title ?? 'Home') ?></h1>
    <p><?= e($message ?? '') ?></p>
</section>

<?= component('alert', [
    'type' => 'success',
    'text' => 'Routes, views, models, SQLite/MySQL, migrations, cache, and assets are wired up.',
]) ?>

