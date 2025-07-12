<?php

    $component = $component ?? '';
    $controlPanel = $controlPanel ?? '';
    $filterPanel = $filterPanel ?? '';
    $methodSend = $methodSend ?? '';
    $endpointSend = $endpointSend ?? '';
?>

<section class="component-wrapper-line">
    <section class="component-wrapper-table component">
        <?= $controlPanel ?>
        <?= $filterPanel ?>
        <?= $component ?>
    </section>
</section>
