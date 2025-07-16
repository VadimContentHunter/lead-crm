<?php

    $component = $component ?? '';
    $controlPanel = $controlPanel ?? '';
    $filterPanel = $filterPanel ?? '';
?>

<section class="component-wrapper-line">
    <section class="component-wrapper-table component">
        <?= $controlPanel ?>
        <?= $filterPanel ?>
        <?= $component ?>
    </section>
</section>
