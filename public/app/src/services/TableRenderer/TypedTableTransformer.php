<?php

namespace crm\src\services\TableRenderer;

use crm\src\services\TableRenderer\interfaces\ITableTransformer;
use crm\src\services\TableRenderer\interfaces\ITypeTransform;

class TypedTableTransformer implements ITableTransformer
{
    /**
     * @param ITypeTransform[] $typeTransformers
     */
    public function __construct(
        protected array $typeTransformers = []
    ) {
        $this->typeTransformers = $typeTransformers;
    }

    public function addTypeTransformer(ITypeTransform $transformer): void
    {
        $this->typeTransformers[] = $transformer;
    }

    public function transform(array $header, array $rows): array
    {
        $result = [];

        foreach ($rows as $row) {
            $transformedRow = [];
            $row_id = $row['id'] ?? $row['uid'] ?? 0;

            foreach ($header as $columnName) {
                $value = $row[$columnName] ?? null;
                $transformedRow[] = $this->transformCell($columnName, $value, $row_id);
            }

            $result[] = $transformedRow;
        }

        return $result;
    }

    public function filterAndRename(array $header, array $rows, array $allowedColumns, array $renameMap): array
    {
        $filteredHeader = [];
        foreach ($allowedColumns as $column) {
            if (in_array($column, $header, true)) {
                $filteredHeader[] = $renameMap[$column] ?? $column;
            }
        }

        $filteredRows = [];
        foreach ($rows as $row) {
            $filteredRow = [];
            $row_id = $row['id'] ?? $row['uid'] ?? 0;
            foreach ($allowedColumns as $column) {
                $value = $row[$column] ?? null;
                $filteredRow[] = $this->transformCell($column, $value, $row_id);
            }
            $filteredRows[] = $filteredRow;
        }

        return [
            'header' => $filteredHeader,
            'rows'   => $filteredRows,
        ];
    }

    protected function transformCell(string $column, mixed $value, ?int $row_id = null): mixed
    {
        if ($value === null) {
            return '';
        }

        foreach ($this->typeTransformers as $transformer) {
            if (in_array($column, $transformer->getColumnNames(), true)) {
                return $transformer->transform($column, $value, $row_id);
            }
        }

        return $value;
    }
}
