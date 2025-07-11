<?php

$overlay_content = $overlay_content ?? '';
$main_menu = $main_menu ?? '';
$content_container = $content_container ?? '';

// Пример массива с элементами оверлея
$overlay_items = $overlay_items ?? [];

?>

<!-- Основная страница -->
<div class="page-container">
    <section class="overlay-content">
        <?php foreach ($overlay_items as $item) : ?>
            <?= $item ?>
        <?php endforeach; ?>

        <?= $overlay_content ?>
    </section>

    <?= $main_menu ?>
    <?= $content_container ?>
</div>