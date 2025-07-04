<?php

namespace crm\src\services\TableRenderer;

use crm\src\services\TableRenderer\TableRenderer;
use crm\src\services\TableRenderer\interfaces\ITableRenderResult;

/**
 * Результат генерации таблицы: данные + итоговый HTML.
 */
class TableRenderResult implements ITableRenderResult
{
    /**
     * @param array<int, string> $header
     * @param array<int, array<int, mixed>|string|int> $rows
     * @param array<string, string> $attributes
     * @param array<int, string> $classes
     * @param array<int, string> $classesWrapper
     * @param array<string, string> $attributesWrapper
     */
    public function __construct(
        private array $header,
        private array $rows,
        private array $attributes = [],
        private array $classes = [],
        private array $classesWrapper = [],
        private array $attributesWrapper = []
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function getHeader(): array
    {
        return $this->header;
    }

    /**
     * @return array<int, array<int, mixed>|string|int>
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    public function asHtml(): string
    {
        return TableRenderer::render(
            header: $this->header,
            rows: $this->rows,
            attributes: $this->attributes,
            classes: $this->classes,
            classesWrapper: $this->classesWrapper,
            attributesWrapper: $this->attributesWrapper
        );
    }
}
