<?php

namespace crm\src\controllers\API;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\services\AppContext\ISecurity;
use crm\src\services\AppContext\IAppContext;
use crm\src\services\TableRenderer\TableFacade;
use crm\src\_common\repositories\SourceRepository;
use crm\src\services\TableRenderer\TableDecorator;
use crm\src\_common\adapters\SourceValidatorAdapter;
use crm\src\services\TableRenderer\TableRenderInput;
use crm\src\services\TableRenderer\TableTransformer;
use crm\src\components\SourceManagement\_entities\Source;
use crm\src\components\SourceManagement\SourceManagement;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\components\Security\_exceptions\JsonRpcSecurityException;

class SourceController
{
    private SourceManagement $sourceManagement;

    private JsonRpcServerFacade $rpc;

    /**
     * @var array<string,callable>
     */
    private array $methods = [];

    public function __construct(
        private IAppContext $appContext
    ) {
        $this->sourceManagement = $this->appContext->getSourceManagement();

        $this->rpc = $this->appContext->getJsonRpcServerFacade();

        $this->initMethodMap();
        $this->init();
    }

    private function initMethodMap(): void
    {
        if ($this->appContext instanceof ISecurity) {
            /**
             * @var SourceController $secureCall
             */
            $secureCall = $this->appContext->wrapWithSecurity($this);
        } else {
            $secureCall = $this;
        }

        $this->methods = [
            'source.add'        => fn() => $secureCall->createSource($this->rpc->getParams()),
            'source.delete'     => fn() => $secureCall->deleteSource($this->rpc->getParams()),
            'source.get.table'  => fn() => $secureCall->getFormatTable(),
        ];
    }

    public function init(): void
    {
        try {
            $method = $this->rpc->getMethod();

            if (!isset($this->methods[$method])) {
                throw new JsonRpcSecurityException('Метод не найден', -32601);
            }

            ($this->methods[$method])();
        } catch (JsonRpcSecurityException $e) {
            $this->rpc->send($e->toJsonRpcError($this->rpc->getId()));
        } catch (\Throwable $e) {
            $this->rpc->replyError(-32000, $e->getMessage());
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function createSource(array $params): void
    {
        if (is_string($params['title'] ?? null)) {
            $executeResult = $this->sourceManagement->create()->execute($params['title']);
            $title =  $executeResult->getTitle() ?? 'неизвестный источник';
            if ($executeResult->isSuccess()) {
                $this->rpc->replyData([
                    ['type' => 'success', 'message' => 'Источник успешно добавлен'],
                    ['type' => 'info', 'message' => "Добавленный источник: <b>{$title}</b>"]
                ]);
            } else {
                $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
                $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Источник не добавлен. Причина: ' . $errorMsg]
                ]);
            }
        } else {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Данные источника некорректного формата.']
            ]);
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function deleteSource(array $params): void
    {
        $id = $params['row_id'] ?? $params['rowId'] ?? $params['id'] ?? null;
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'ID Источника должен быть целым числом.']
            ]);
        }

        $source = $this->sourceManagement->delete()->executeById((int)$id);
        if ($source->isSuccess()) {
            $this->getFormatTable();
        } else {
            $errorMsg = $source->getError()?->getMessage() ?? 'неизвестная ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Источник не удалён. Причина: ' . $errorMsg]
            ]);
        }
    }

    public function getFormatTable(): void
    {
        $headers = $this->sourceManagement->get()->executeColumnNames()->getArray();
        $rows = $this->sourceManagement->get()->executeAllMapped(function (Source $source) {
            return [
                'id' => $source->id,
                'title' => $source->title,
            ];
        })->getArray();

        $input = new TableRenderInput(
            header: $headers,
            rows: $rows,
            attributes: ['id' => 'source-table-1', 'data-module' => 'sources'],
            classes: ['base-table'],
            hrefButton: '/page/source-edit',
            hrefButtonDel: '/page/source-delete',
            attributesWrapper: [
                'table-r-id' => 'source-table-1'
            ],
            allowedColumns: [
                'id',
                'title',
            ],
            renameMap: [],
        );

        $tableFacade = new TableFacade(new TableTransformer(),  new TableDecorator());
        $this->rpc->replyData([
            'type' => 'success',
            'table' => $tableFacade->renderFilteredTable($input)->asHtml()
        ]);
    }
}
