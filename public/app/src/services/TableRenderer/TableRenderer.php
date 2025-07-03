<?php

namespace crm\src\services\TableRenderer;

use crm\src\services\TableRenderer\TableCellRenderer;

/**
 * Строит HTML таблицу и обёртки вокруг неё.
 */
class TableRenderer
{
    /**
     * Генерирует HTML таблицы.
     */
    public static function render(
        array $header,
        array $rows,
        array $attributes = [],
        array $classes = []
    ): string {
        $attrHtml = self::buildHtmlAttributes($attributes);
        $classHtml = empty($classes) ? '' : ' class="' . htmlspecialchars(implode(' ', $classes)) . '"';

        $html = "<table{$classHtml}{$attrHtml}>";
        $html .= "<thead><tr>";

        foreach ($header as $head) {
            $html .= '<th>' . htmlspecialchars($head) . '</th>';
        }

        $html .= '</tr></thead><tbody>';

        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . TableCellRenderer::render($cell) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

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
