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

    /**
     * @param  string $wrapperTag
     * @param  array<string, string> $wrapperAttributes
     * @param  string[] $wrapperClasses
     * @return string Rendered table wrapped in a custom HTML element
     */
    public function asWrappedHtml(
        string $wrapperTag,
        array $wrapperAttributes = [],
        array $wrapperClasses = []
    ): string;
}
