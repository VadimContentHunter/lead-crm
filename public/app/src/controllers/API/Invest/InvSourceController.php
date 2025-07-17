<?php

namespace crm\src\controllers\API\Invest;

use Throwable;
use crm\src\services\AppContext\ISecurity;
use crm\src\services\AppContext\IAppContext;
use crm\src\Investments\InvSource\_entities\InvSource;
use crm\src\Investments\_application\InvestmentService;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\components\Security\_exceptions\JsonRpcSecurityException;

class InvSourceController
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
             * @var InvSourceController $secureCall
             */
            $secureCall = $this->appContext->wrapWithSecurity($this);
        } else {
            $secureCall = $this;
        }

        $this->methods = [
            'invest.source.add' => fn() => $secureCall->createInvSource($this->rpc->getParams()),
            'invest.source.get.table' => fn() => $secureCall->getSourceTable(),
            'invest.source.edit.cell' => fn() => $secureCall->editSourceCell($this->rpc->getParams()),
            'invest.source.delete' => fn() => $secureCall->delete($this->rpc->getParams()),
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
    public function createInvSource(array $params): void
    {
        $result = $this->service->createInvSource($params);

        if ($result->isSuccess()) {
            $title = $result->getLabel() ?? '---';

            $this->rpc->replyData([
                'type' => 'success',
                'table' => $this->service->getSourceTable()->getString() ?? '---',
                'messages' => [
                    ['type' => 'success', 'message' => 'Источник успешно добавлен'],
                    ['type' => 'info', 'message' => "Добавленный источник: <b>{$title}</b>"]
                ]
            ]);
        } else {
            $errorMessage = $result->getError()?->getMessage() ?? 'Произошла ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Произошла ошибка: ' . $errorMessage]
            ]);
        }
    }

    public function getSourceTable(): void
    {
        $this->rpc->replyData([
            'type' => 'success',
            'table' => $this->service->getSourceTable()->getString() ?? '---',
        ],);
    }

    /**
     * @param array<string,mixed> $params
     */
    public function editSourceCell(array $params): void
    {
        $result = $this->service->updateSource($params);

        if ($result->isSuccess()) {
            $id = $result->getData()['id'] ?? '---';
            $updatedCode = $result->getData()['code'] ?? null;
            $updatedLabel = $result->getData()['label'] ?? null;

            $infoData = "<br>id: <b>{$id}</b>";
            $infoData .= $updatedCode !== null ? "<br>code: <b>{$updatedCode}</b>" : "";
            $infoData .= $updatedLabel !== null ? "<br>label: <b>{$updatedLabel}</b>" : "";

            $this->rpc->replyData([
                'type' => 'success',
                'table' => $this->service->getSourceTable()->getString() ?? '---',
                'messages' => [
                    ['type' => 'success', 'message' => 'Источник успешно обновлен'],
                    ['type' => 'info', 'message' => "Обновленный источник: {$infoData}"]
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
        $result = $this->service->deleteSource($params);

        if ($result->isSuccess()) {
            $code = $result->getCode() ?? '---';
            $label = $result->getLabel() ?? '---';
            $id = $result->getId() ?? '---';

            $info = "<br>id: <b>{$id}</b>";
            $info .= "<br>code: <b>{$code}</b>";
            $info .= "<br>label: <b>{$label}</b>";
            $this->rpc->replyData([
                'type' => 'success',
                'table' => $this->service->getSourceTable()->getString() ?? '---',
                'messages' => [
                    ['type' => 'success', 'message' => 'Источник успешно удален'],
                    ['type' => 'info', 'message' => "Удалённый источник: {$info}"]
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
