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
     * Оборачивает таблицу в HTML-тег.
     */
    public static function renderWrappedTable(
        array $header,
        array $rows,
        array $attributes,
        array $classes,
        string $wrapperTag,
        array $wrapperAttributes = [],
        array $wrapperClasses = []
    ): string {
        $tableHtml = self::render($header, $rows, $attributes, $classes);

        $wrapperAttr = self::buildHtmlAttributes($wrapperAttributes);
        $wrapperClass = empty($wrapperClasses) ? '' : ' class="' . htmlspecialchars(implode(' ', $wrapperClasses)) . '"';

        return sprintf('<%1$s%2$s%3$s>%4$s</%1$s>', $wrapperTag, $wrapperClass, $wrapperAttr, $tableHtml);
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
