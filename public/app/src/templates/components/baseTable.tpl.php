<?php

use crm\src\services\TemplateRenderer\_common\TableCellRenderer;

?>

<section class="component component--full">
    <form class="base-table">
        <div class="table-header">
            <?php foreach ($columns as $col) : ?>
                <div class="table-cell"><?= htmlspecialchars($col) ?></div>
            <?php endforeach; ?>
        </div>

        <?php foreach ($rows as $row) : ?>
            <div class="table-row">
                <?php foreach ($row as $cell) : ?>
                    <div class="table-cell">
                        <?= TableCellRenderer::render($cell) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </form>
</section>
