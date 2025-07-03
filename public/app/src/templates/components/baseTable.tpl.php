<?php

use crm\src\services\TemplateRenderer\_common\TableCellRenderer;

$columns = $columns ?? [];
$rows = $rows ?? [];
?>

<section class="component-wrapper-line">

    <section class="component component--full" id="test-base-table">
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

</section>

<!-- <script type="module">
  import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';
  
//   window.addEventListener('DOMContentLoaded', () => {
    ComponentFunctions.attachJsonRpcInputTrigger({
      triggerSelector: '#test-base-table .table-row button.btn-delete',
      containerSelector: '#add-user-form',
      method: 'user.delete',
      endpoint: '/api/users'
    });
//   });
</script>    -->