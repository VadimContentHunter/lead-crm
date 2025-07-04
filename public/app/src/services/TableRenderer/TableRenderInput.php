<?php

namespace crm\src\services\TableRenderer;

use crm\src\services\TableRenderer\interfaces\ITableRenderInput;

class TableRenderInput implements ITableRenderInput
{
    /**
     * @param array<int,string> $header
     * @param array<int,mixed> $rows
     * @param array<string,string> $attributes
     * @param array<int,string> $classes
     * @param array<int,string> $allowedColumns
     * @param array<string,string> $renameMap
     */
    public function __construct(
        private array $header,
        private array $rows,
        private array $attributes = [],
        private array $classes = [],
        private array $allowedColumns = [],
        private array $renameMap = []
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
     * @return array<int,mixed>
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * @return array<string, string>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return array<int, string>
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * @return array<int, string>
     */
    public function getAllowedColumns(): array
    {
        return $this->allowedColumns;
    }

    /**
     * @return array<string, string>
     */
    public function getRenameMap(): array
    {
        return $this->renameMap;
    }
}
