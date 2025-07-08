<?php

use crm\src\services\TableRenderer\TableCellRenderer;

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
                        $html .= '<tr>';

                        // Если $row не массив, оборачиваем в массив
                        foreach (is_iterable($row) ? $row : [$row] as $cell) {
                            $html .= '<td>' . TableCellRenderer::render($cell) . '</td>';
                        }

                        $html .= '</tr>';
                    } ?>
                </tbody>
            </table>
        </div>
    </section>
</section>
