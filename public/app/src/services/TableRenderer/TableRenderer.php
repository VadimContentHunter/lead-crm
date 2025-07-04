<?php

namespace crm\src\services\TableRenderer;

use crm\src\services\TableRenderer\TableCellRenderer;

/**
 * Строит HTML таблицу с заголовками в первой строке тела.
 */
class TableRenderer
{
    /**
     * Генерирует HTML таблицы.
     *
     * @param array $header     Заголовки колонок (первая строка tbody)
     * @param array $rows       Массив строк таблицы
     * @param array $attributes Атрибуты для <table>
     * @param array $classes    CSS-классы таблицы
     *
     * @return string
     */
    public static function render(
        array $header,
        array $rows,
        array $attributes = [],
        array $classes = []
    ): string {
        $attrHtml = self::buildHtmlAttributes($attributes);
        $classHtml = empty($classes) ? '' : ' class="' . htmlspecialchars(implode(' ', $classes)) . '"';

        $html = "<table{$classHtml}{$attrHtml}><tbody>";

        // Первая строка — заголовки (как <td>)
        $html .= '<tr>';
        foreach ($header as $head) {
            $html .= '<td><strong>' . htmlspecialchars($head) . '</strong></td>';
        }
        $html .= '</tr>';

        // Остальные строки — данные
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . TableCellRenderer::render($cell) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        $html = '<div class="table-wrapper">' . $html . '</div>';

        return $html;
    }

    /**
     * Преобразует массив атрибутов в строку.
     */
    private static function buildHtmlAttributes(array $attributes): string
    {
        $html = '';

        foreach ($attributes as $key => $value) {
            $html .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
        }

        return $html;
    }
}
