<?php

namespace crm\src\controllers\API;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\_common\repositories\BalanceRepository;
use crm\src\_common\repositories\DepositRepository;
use crm\src\_common\adapters\BalanceValidatorAdapter;
use crm\src\_common\adapters\DepositValidatorAdapter;
use crm\src\components\BalanceManagement\BalanceManagement;
use crm\src\components\DepositManagement\DepositManagement;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\_common\repositories\LeadRepository\LeadRepository;
use crm\src\components\BalanceManagement\_common\mappers\BalanceMapper;
use crm\src\components\DepositManagement\_common\mappers\DepositMapper;

class DepositController
{
    private DepositManagement $depositManagement;

    private JsonRpcServerFacade $rpc;

    public function __construct(
        private string $projectPath,
        PDO $pdo,
        private LoggerInterface $logger = new NullLogger()
    ) {
        $this->depositManagement = new DepositManagement(
            new DepositRepository($pdo, $logger),
            new DepositValidatorAdapter()
        );

        $this->rpc = new JsonRpcServerFacade();
        switch ($this->rpc->getMethod()) {
            case 'deposit.add':
                $this->createDeposit($this->rpc->getParams());
            // break;

            case 'deposit.edit':
                $this->editDeposit($this->rpc->getParams());
            // break;
            case 'deposit.create.edit':
                $this->createOrEditDeposit($this->rpc->getParams());
            // break;

            default:
                $this->rpc->replyError(-32601, 'Метод не найден');
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

        $executeResult = $this->depositManagement->create()->execute(DepositMapper::fromArray($params));
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
            $executeResult = $this->depositManagement->update()->executeByLeadId(DepositMapper::fromArray($params));
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
