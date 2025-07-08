<?php

use crm\src\services\TableRenderer\TableCellRenderer;

$filterPanel ??= '';
$header ??= [];
$rows ??= [];
?>


<section class="component-wrapper-line">
    <section class="component component-table">
        <?= $filterPanel ?>
        <div table-r-id="user-table-1">
            <table class="base-table" id="user-table-1" data-module="users">
                <tbody>
                    <tr>
                    <?php foreach ($header as $head) {
                        echo'<td><strong>' . htmlspecialchars($head) . '</strong></td>';
                    } ?>
                    </tr>

                    <?php  foreach ($rows as $row) {
                        echo '<tr>';

                        // Если $row не массив, оборачиваем в массив
                        foreach (is_iterable($row) ? $row : [$row] as $cell) {
                            echo '<td>' . TableCellRenderer::render($cell) . '</td>';
                        }

                        echo '</tr>';
                    } ?>
                </tbody>
            </table>
        </div>
    </section>
</section>
