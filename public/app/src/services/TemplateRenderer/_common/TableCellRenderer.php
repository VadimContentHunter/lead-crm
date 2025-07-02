<?php

namespace crm\src\services\TemplateRenderer\_common;

class TableCellRenderer
{
    /**
     * @param mixed[]|string $cell
     */
    public static function isInput(array|string $cell): bool
    {
        return is_array($cell) && isset($cell['type'], $cell['name']);
    }

    /**
     * @param mixed[]|string|int $cell
     */
    public static function render(array|string|int $cell): string
    {
        if (!self::isInput($cell)) {
            $cell = is_array($cell) ? $cell['type'] : $cell;
            return (string) $cell;
        }

        $cell = is_array($cell) ? $cell : ['type' => $cell];
        return match ($cell['type']) {
            'text', 'number', 'password' => self::renderInput($cell),
            'select' => self::renderSelect($cell),
            default => self::renderInput($cell),
        };
    }

    /**
     * @param mixed[] $cell
     */
    private static function renderInput(array $cell): string
    {
        $type = htmlspecialchars($cell['type']);
        $name = htmlspecialchars($cell['name']);
        $value = htmlspecialchars($cell['value'] ?? '');

        return "<input type=\"$type\" name=\"$name\" value=\"$value\">";
    }

    /**
     * @param mixed[] $cell
     */
    private static function renderSelect(array $cell): string
    {
        $name = htmlspecialchars($cell['name']);
        $options = $cell['options'] ?? [];
        $selected = $cell['value'] ?? '';

        $html = "<select name=\"$name\">";
        foreach ($options as $key => $label) {
            $key = htmlspecialchars($key);
            $label = htmlspecialchars($label);
            $isSelected = ((string) $key === (string) $selected) ? ' selected' : '';
            $html .= "<option value=\"$key\"$isSelected>$label</option>";
        }
        $html .= '</select>';

        return $html;
    }
}
