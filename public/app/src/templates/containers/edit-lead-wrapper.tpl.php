<?php

    $controlPanel = $controlPanel ?? '';
    $components = $components ?? [];
?>

<section class="component-wrapper-line">
    <?= $controlPanel ?>
</section>

<section class="component-wrapper-line">
    <?php foreach ($components as $component) : ?>
        <?= $component ?>
    <?php endforeach; ?>
</section>
