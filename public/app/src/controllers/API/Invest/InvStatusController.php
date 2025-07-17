<?php

namespace crm\src\controllers\API\Invest;

use Throwable;
use crm\src\services\AppContext\ISecurity;
use crm\src\services\AppContext\IAppContext;
use crm\src\Investments\_application\InvestmentService;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\components\Security\_exceptions\JsonRpcSecurityException;

class InvStatusController
{
    private JsonRpcServerFacade $rpc;

    private InvestmentService $service;

    /**
     * @var array<string,callable>
     */
    private array $methods = [];

    public function __construct(
        private IAppContext $appContext
    ) {
        $this->rpc = $this->appContext->getJsonRpcServerFacade();
        $this->service = $this->appContext->getInvestmentService();

        $this->initMethodMap();
        $this->init();
    }

    private function initMethodMap(): void
    {
        if ($this->appContext instanceof ISecurity) {
            /**
             * @var InvStatusController $secureCall
             */
            $secureCall = $this->appContext->wrapWithSecurity($this);
        } else {
            $secureCall = $this;
        }

        $this->methods = [
            'invest.status.add' => fn() => $secureCall->createInvStatus($this->rpc->getParams()),
            'invest.status.get.table' => fn() => $secureCall->getStatusTable(),
            'invest.status.edit.cell' => fn() => $secureCall->editStatusCell($this->rpc->getParams()),
            'invest.status.delete' => fn() => $secureCall->delete($this->rpc->getParams()),
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
        } catch (Throwable $e) {
            $this->rpc->replyError(-32000, $e->getMessage());
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function createInvStatus(array $params): void
    {
        $result = $this->service->createInvStatus($params);

        if ($result->isSuccess()) {
            $title = $result->getLabel() ?? '---';

            $this->rpc->replyData([
                'type' => 'success',
                'table' => $this->service->getStatusTable()->getString() ?? '---',
                'messages' => [
                    ['type' => 'success', 'message' => 'Статус успешно добавлен'],
                    ['type' => 'info', 'message' => "Добавленный статус: <b>{$title}</b>"]
                ]
            ]);
        } else {
            $errorMessage = $result->getError()?->getMessage() ?? 'Произошла ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Произошла ошибка: ' . $errorMessage]
            ]);
        }
    }

    public function getStatusTable(): void
    {
        $this->rpc->replyData([
            'type' => 'success',
            'table' => $this->service->getStatusTable()->getString() ?? '---',
        ]);
    }

    /**
     * @param array<string,mixed> $params
     */
    public function editStatusCell(array $params): void
    {
        $result = $this->service->updateStatus($params);

        if ($result->isSuccess()) {
            $id = $result->getData()['id'] ?? '---';
            $updatedCode = $result->getData()['code'] ?? null;
            $updatedLabel = $result->getData()['label'] ?? null;

            $infoData = "<br>id: <b>{$id}</b>";
            $infoData .= $updatedCode !== null ? "<br>code: <b>{$updatedCode}</b>" : "";
            $infoData .= $updatedLabel !== null ? "<br>label: <b>{$updatedLabel}</b>" : "";

            $this->rpc->replyData([
                'type' => 'success',
                'table' => $this->service->getStatusTable()->getString() ?? '---',
                'messages' => [
                    ['type' => 'success', 'message' => 'Статус успешно обновлен'],
                    ['type' => 'info', 'message' => "Обновленный статус: {$infoData}"]
                ]
            ]);
        } else {
            $errorMessage = $result->getError()?->getMessage() ?? 'Произошла ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Произошла ошибка: ' . $errorMessage]
            ]);
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function delete(array $params): void
    {
        $result = $this->service->deleteStatus($params);

        if ($result->isSuccess()) {
            $code = $result?->getCode() ?? '---';
            $label = $result?->getLabel() ?? '---';
            $id = $result?->getId() ?? '---';

            $info = "<br>id: <b>{$id}</b>";
            $info .= "<br>code: <b>{$code}</b>";
            $info .= "<br>label: <b>{$label}</b>";

            $this->rpc->replyData([
                'type' => 'success',
                'table' => $this->service->getStatusTable()->getString() ?? '---',
                'messages' => [
                    ['type' => 'success', 'message' => 'Статус успешно удален'],
                    ['type' => 'info', 'message' => "Удалённый статус: {$info}"]
                ]
            ]);
        } else {
            $errorMessage = $result->getError()?->getMessage() ?? 'Произошла ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Произошла ошибка: ' . $errorMessage]
            ]);
        }
    }
}
