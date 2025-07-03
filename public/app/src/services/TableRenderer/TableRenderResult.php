<?php

namespace crm\src\services\TableRenderer;

use crm\src\services\TableRenderer\TableRenderer;
use crm\src\services\TableRenderer\interfaces\ITableRenderResult;

/**
 * Результат генерации таблицы: данные + итоговый HTML.
 */
class TableRenderResult implements ITableRenderResult
{
    public function __construct(
        private array $header,
        private array $rows,
        private array $attributes = [],
        private array $classes = []
    ) {
    }

    public function getHeader(): array
    {
        return $this->header;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function asHtml(): string
    {
        return TableRenderer::render($this->header, $this->rows, $this->attributes, $this->classes);
    }
}
