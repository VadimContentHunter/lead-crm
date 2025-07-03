<?php

namespace crm\src\services\TableRenderer\interfaces;

/**
 * Interface for table rendering result.
 */
interface ITableRenderResult
{
    /**
     * @return string[] Final header
     */
    public function getHeader(): array;

    /**
     * @return array<array<mixed>> Final rows with transformed content
     */
    public function getRows(): array;

    /**
     * @return string Rendered HTML <table>
     */
    public function asHtml(): string;
}
