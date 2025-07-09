<?php

namespace crm\src\controllers\API;

use crm\src\services\AppContext\ISecurity;
use crm\src\services\AppContext\IAppContext;
use crm\src\_common\repositories\DepositRepository;
use crm\src\_common\adapters\DepositValidatorAdapter;
use crm\src\components\DepositManagement\DepositManagement;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\components\Security\_exceptions\JsonRpcSecurityException;
use crm\src\components\DepositManagement\_common\mappers\DepositMapper;

class DepositController
{
    private DepositManagement $depositManagement;

    private JsonRpcServerFacade $rpc;

    /**
     * @var array<string,callable>
     */
    private array $methods = [];
    public function __construct(
        private IAppContext $appContext
    ) {
        $this->depositManagement = $this->appContext->getDepositManagement();

        $this->rpc = $this->appContext->getJsonRpcServerFacade();

        $this->initMethodMap();
        $this->init();
    }

    private function initMethodMap(): void
    {
        if ($this->appContext instanceof ISecurity) {
            /**
             * @var DepositController $secureCall
             */
            $secureCall = $this->appContext->wrapWithSecurity($this);
        } else {
            $secureCall = $this;
        }

        $this->methods = [
            'deposit.add' => fn() => $secureCall->createDeposit($this->rpc->getParams()),
            'deposit.edit' => fn() => $secureCall->editDeposit($this->rpc->getParams()),
            'deposit.create.edit' => fn() => $secureCall->createOrEditDeposit($this->rpc->getParams()),
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
    public function createDeposit(array $params): void
    {
        $leadId = $params['leadId'] ?? $params['lead_id'] ?? null;
        if (!filter_var($leadId, FILTER_VALIDATE_INT)) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'ID Лида должен быть целым числом.']
            ]);
        }

        if (
            !filter_var($params['sum'], FILTER_VALIDATE_INT)
            && !filter_var($params['tx_id'], FILTER_VALIDATE_INT)
        ) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Неверные данные']
            ]);
        }

        $depositDto = DepositMapper::fromArray($params);
        if ($depositDto === null) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Некорректные данные для создания Депозита.']
            ]);
        }
        $executeResult = $this->depositManagement->create()->execute($depositDto);
        if ($executeResult->isSuccess()) {
            $sum = $executeResult->getSum() ?? 0;
            $txId = $executeResult->getTxId() ?? 0;
            $leadId = $executeResult->getLeadId() ?? 'Не указан';
            $this->rpc->replyData([
                ['type' => 'success', 'message' => 'Депозит успешно добавлен'],
                ['type' => 'info', 'message' => <<<HTML
                            Добавленный Deposit:
                            <br>sum: <b>$sum</b>
                            <br>tx_id: <b>$txId</b>
                            <br>lead_id: <b>$leadId</b>
                        HTML
                ]
            ]);
        } else {
            $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Депозит не был добавлен. Причина: ' . $errorMsg]
            ]);
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function editDeposit(array $params): void
    {
        $leadId = $params['leadId'] ?? $params['lead_id'] ?? null;
        if (!filter_var($leadId, FILTER_VALIDATE_INT)) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'ID Лида должен быть целым числом.']
            ]);
        }

        if (
            !filter_var($params['sum'], FILTER_VALIDATE_INT)
            && !filter_var($params['tx_id'], FILTER_VALIDATE_INT)
        ) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Неверные данные']
            ]);
        }

        $params['lead_id'] = (int)$leadId;
        $depositDto = DepositMapper::fromArray($params);
        if ($depositDto === null) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Некорректные данные для создания Депозита.']
            ]);
        }

        $executeResult = $this->depositManagement->update()->executeByLeadId($depositDto);
        if ($executeResult->isSuccess()) {
            $sum = $executeResult->getSum() ?? 0;
            $txId = $executeResult->getTxId() ?? 0;
            $leadId = $executeResult->getLeadId() ?? 'Не указан';

            $this->rpc->replyData([
               ['type' => 'success', 'message' => 'Баланс успешно обновлен'],
               ['type' => 'info', 'message' => <<<HTML
                            Измененный Депозит:
                            <br>sum: <b>$sum</b>
                            <br>tx_id: <b>$txId</b>
                            <br>lead_id: <b>$leadId</b>
                        HTML
               ]
            ]);
        } else {
            $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Депозит не был обновлен. Причина: ' . $errorMsg]
            ]);
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function createOrEditDeposit(array $params): void
    {
        $leadId = $params['leadId'] ?? $params['lead_id'] ?? null;
        if (!filter_var($leadId, FILTER_VALIDATE_INT)) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'ID Лида должен быть целым числом.']
            ]);
        }

        if (
            !filter_var($params['sum'], FILTER_VALIDATE_INT)
            && !filter_var($params['tx_id'], FILTER_VALIDATE_INT)
        ) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Неверные данные']
            ]);
        }

        $params['lead_id'] = (int)$leadId;
        $balanceResult = $this->depositManagement->get()->getByLeadId($leadId);
        if ($balanceResult->isSuccess()) {
            $this->editDeposit($params);
        } else {
            $this->createDeposit($params);
        }
    }
}
