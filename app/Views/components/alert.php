<?php
$type = $type ?? 'info';
$text = $text ?? '';
?>
<div class="alert alert-<?= e($type) ?>" role="status">
    <?= e($text) ?>
</div>
