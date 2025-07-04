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
     * @param array<int,string> $header            Заголовки колонок (первая строка tbody)
     * @param array<int,array<int, mixed>|string|int> $rows              Массив строк таблицы
     * @param array<string,string> $attributes        Атрибуты для <table>
     * @param array<int,string> $classes           CSS-классы таблицы
     * @param array<int,string> $classesWrapper    CSS-классы обертки таблицы
     * @param array<string,string> $attributesWrapper Атрибуты для обертки
     *
     * @return string
     */
    public static function render(
        array $header,
        array $rows,
        array $attributes = [],
        array $classes = [],
        array $classesWrapper = [],
        array $attributesWrapper = [],
    ): string {
        $attrHtml = self::buildHtmlAttributes($attributes);
        $attrHtmlWrapper = self::buildHtmlAttributes($attributesWrapper);
        $classHtml = empty($classes) ? '' : ' class="' . htmlspecialchars(implode(' ', $classes)) . '"';
        $classHtmlWrapper = empty($classesWrapper) ? '' : ' class="' . htmlspecialchars(implode(' ', $classesWrapper)) . '"';

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

            // Если $row не массив, оборачиваем в массив
            foreach (is_iterable($row) ? $row : [$row] as $cell) {
                $html .= '<td>' . TableCellRenderer::render($cell) . '</td>';
            }

            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        $html = "<div{$classHtmlWrapper}{$attrHtmlWrapper}>" . $html . "</div>";

        return $html;
    }

    /**
     * Преобразует массив атрибутов в строку.
     *
     * @param array<string,string> $attributes
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
