<?php

namespace crm\src\services\TableRenderer;

use crm\src\services\TableRenderer\interfaces\ITableRenderInput;

class TableRenderInput implements ITableRenderInput
{
    public function __construct(
        private array $header,
        private array $rows,
        private array $attributes = [],
        private array $classes = [],
        private array $allowedColumns = [],
        private array $renameMap = []
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

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getClasses(): array
    {
        return $this->classes;
    }

    public function getAllowedColumns(): array
    {
        return $this->allowedColumns;
    }

    public function getRenameMap(): array
    {
        return $this->renameMap;
    }
}
