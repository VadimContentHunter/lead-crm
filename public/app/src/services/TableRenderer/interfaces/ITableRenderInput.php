<?php

namespace crm\src\services\TableRenderer\interfaces;

/**
 * Interface for table rendering input data.
 */
interface ITableRenderInput
{
    /**
     * @return string[] List of table headers
     */
    public function getHeader(): array;

    /**
     * @return array<array<string, mixed>> Raw data rows
     */
    public function getRows(): array;

    /**
     * @return array<string, string> HTML attributes for <table>
     */
    public function getAttributes(): array;

    /**
     * @return string[] CSS classes for <table>
     */
    public function getClasses(): array;

    /**
     * @return array<string,string>
     */
    public function getAttrWrapper(): array;

    /**
     * @return string[]
     */
    public function getClassesWrapper(): array;

    /**
     * @return string[] Разрешённые колонки
     */
    public function getAllowedColumns(): array;

    /**
     * @return array<string, string> Карта переименования заголовков
     */
    public function getRenameMap(): array;

    public function getButtonHref(): string;
}
