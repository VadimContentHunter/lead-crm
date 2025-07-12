<?php

namespace crm\src\controllers\API;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\controllers\StatusPage;
use crm\src\services\AppContext\ISecurity;
use crm\src\services\AppContext\IAppContext;
use crm\src\services\TableRenderer\TableFacade;
use crm\src\_common\repositories\StatusRepository;
use crm\src\services\TableRenderer\TableDecorator;
use crm\src\_common\adapters\StatusValidatorAdapter;
use crm\src\services\TableRenderer\TableRenderInput;
use crm\src\services\TableRenderer\TableTransformer;
use crm\src\components\StatusManagement\_entities\Status;
use crm\src\components\StatusManagement\StatusManagement;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\components\Security\_exceptions\JsonRpcSecurityException;
use crm\src\components\UserManagement\_common\mappers\UserFilterMapper;
use crm\src\components\StatusManagement\_common\interfaces\IStatusManagement;

class StatusController
{
    private StatusPage $statusPage;

    private IStatusManagement $statusManagement;

    private JsonRpcServerFacade $rpc;

    /**
     * @var array<string,callable>
     */
    private array $methods = [];

    public function __construct(
        private IAppContext $appContext,
    ) {
        $this->statusPage = new StatusPage($this->appContext);
        $this->statusManagement = $this->appContext->getStatusManagement();
        $this->rpc = $this->appContext->getJsonRpcServerFacade();

        $this->initMethodMap();
        $this->init();
    }

    private function initMethodMap(): void
    {
        if ($this->appContext instanceof ISecurity) {
            /**
             * @var StatusController $secureCall
             */
            $secureCall = $this->appContext->wrapWithSecurity($this);
        } else {
            $secureCall = $this;
        }

        $this->methods = [
            'status.add'        => fn() => $secureCall->createStatus($this->rpc->getParams()),
            'status.delete'     => fn() => $secureCall->deleteStatus($this->rpc->getParams()),
            'status.get.table'  => fn() => $secureCall->getFormatTable(),
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
    public function createStatus(array $params): void
    {
        if (is_string($params['title'] ?? null)) {
            $executeResult = $this->statusManagement->create()->execute($params['title']);
            $title =  $executeResult->getTitle() ?? 'неизвестный статус';
            if ($executeResult->isSuccess()) {
                $this->rpc->replyData([
                    ['type' => 'success', 'message' => 'Статус успешно добавлен'],
                    ['type' => 'info', 'message' => "Добавленный статус: <b>{$title}</b>"]
                ]);
            } else {
                $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
                $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Статус не добавлен. Причина: ' . $errorMsg]
                ]);
            }
        } else {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Данные статуса некорректного формата.']
            ]);
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function deleteStatus(array $params): void
    {
        $id = $params['row_id'] ?? $params['rowId'] ?? $params['id'] ?? null;
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'ID Статус должен быть целым числом.']
            ]);
        }

        $executeResult = $this->statusManagement->delete()->executeById((int)$id);
        if ($executeResult->isSuccess()) {
            $this->getFormatTable([
                'messages' => [
                    ['type' => 'success', 'message' => 'Статус (ID: ' . (int)$id . ') был успешно удалён']
                ]
            ]);
        } else {
            $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Статус не удалён. Причина: ' . $errorMsg]
            ]);
        }
    }

    public function getFormatTable(array $resultMetadata = []): void
    {
        $this->rpc->replyData(array_merge(
            [
                'type' => 'success',
                'table' => $this->statusPage->getRenderTable(),
            ],
            $resultMetadata
        ));
    }
}
