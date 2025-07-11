<?php

$overlay_content = $overlay_content ?? '';
$main_menu = $main_menu ?? '';
$content_container = $content_container ?? '';
?>

<!-- Основная страница -->
<div class="page-container">
    <section class="overlay-content">
        <?= $overlay_content ?>
    </section>

    <?= $main_menu ?>
    <?= $content_container ?>
</div>