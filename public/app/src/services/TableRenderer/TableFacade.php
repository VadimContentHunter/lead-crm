<?php

namespace crm\src\services\TableRenderer;

use crm\src\services\TableRenderer\TableDecorator;
use crm\src\services\TableRenderer\TableRenderResult;
use crm\src\services\TableRenderer\interfaces\ITableRenderInput;
use crm\src\services\TableRenderer\interfaces\ITableTransformer;
use crm\src\services\TableRenderer\interfaces\ITableRenderResult;

/**
 * Универсальный фасад для работы с таблицами.
 */
class TableFacade
{
    public function __construct(
        private ITableTransformer $transformer,
        private TableDecorator $decorator
    ) {
    }

    /**
     * Полный цикл обработки таблицы.
     */
    public function renderTable(ITableRenderInput $input): ITableRenderResult
    {
        $transformed = $this->transformer->transform($input->getHeader(), $input->getRows());
        $decorated = $this->decorator->decorateWithActions($input->getHeader(), $transformed);

        return new TableRenderResult(
            $decorated['header'],
            $decorated['rows'],
            $input->getAttributes(),
            $input->getClasses()
        );
    }

    /**
     * Полный цикл с фильтрацией и переименованием столбцов.
     *
     * @param  ITableRenderInput $input
     * @return ITableRenderResult
     */
    public function renderFilteredTable(ITableRenderInput $input): ITableRenderResult
    {
        $filtered = $this->transformer->filterAndRename(
            $input->getHeader(),
            $input->getRows(),
            $input->getAllowedColumns(),
            $input->getRenameMap()
        );

        $decorated = $this->decorator->decorateWithActions($filtered['header'], $filtered['rows']);

        return new TableRenderResult(
            $decorated['header'],
            $decorated['rows'],
            $input->getAttributes(),
            $input->getClasses(),
            $input->getClassesWrapper(),
            $input->getAttrWrapper()
        );
    }
}
