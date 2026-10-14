<section class="hero">
    <h1><?= e($heading ?? '404') ?></h1>
    <p class="lead"><?= e($message ?? 'Page not found.') ?></p>
    <p><a href="<?= e(url('/')) ?>">Back home</a></p>
</section>
