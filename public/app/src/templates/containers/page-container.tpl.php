<?php

$overlay_content = $overlay_content ?? '';
$overlay_main_content = $overlay_main_content ?? '';
$main_menu = $main_menu ?? '';
$content_container = $content_container ?? '';

// Пример массива с элементами оверлея
$overlay_items = $overlay_items ?? [];

?>

<!-- Основная страница -->
<div class="page-container">
    <section class="overlay-main">
        <div class="overlay-wrapper">
            <div class="overlay-loader loader-main" id="overlay-loader-main" style="display: none;">
                <div class="icon-wrapper-loader">
                    <i class="fa-solid fa-spinner"></i>
                </div>
            </div>
        </div>
        <?= $overlay_main_content ?>
    </section>
    
    <section class="overlay-content">
        <div class="overlay-wrapper">
            <div class="overlay-loader" id="overlay-loader" style="display: none;">
                <div class="icon-wrapper-loader">
                    <i class="fa-solid fa-spinner"></i>
                </div>
            </div>

            <?php foreach ($overlay_items as $item) : ?>
                <?= $item ?>
            <?php endforeach; ?>

            <?= $overlay_content ?>
        </div>
    </section>

    <?= $main_menu ?>
    <?= $content_container ?>
</div>
