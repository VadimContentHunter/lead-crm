<?php

namespace crm\src\controllers\API;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\_common\repositories\BalanceRepository;
use crm\src\_common\adapters\BalanceValidatorAdapter;
use crm\src\components\BalanceManagement\BalanceManagement;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\_common\repositories\LeadRepository\LeadRepository;
use crm\src\components\BalanceManagement\_common\mappers\BalanceMapper;

class BalanceController
{
    private BalanceManagement $balanceManagement;

    private JsonRpcServerFacade $rpc;

    public function __construct(
        private string $projectPath,
        PDO $pdo,
        private LoggerInterface $logger = new NullLogger()
    ) {
        $this->logger->info('BalanceController initialized for project ' . $this->projectPath);
        $leadRepository = new LeadRepository($pdo, $logger);
        $this->balanceManagement = new BalanceManagement(
            new BalanceRepository($pdo, $logger),
            new BalanceValidatorAdapter(),
            $leadRepository
        );

        $this->rpc = new JsonRpcServerFacade();
        switch ($this->rpc->getMethod()) {
            case 'balance.add':
                $this->createBalance($this->rpc->getParams());
            // break;

            case 'balance.edit':
                $this->editBalance($this->rpc->getParams());
            // break;
            case 'balance.create.edit':
                $this->createOrEditBalance($this->rpc->getParams());
            // break;

            default:
                $this->rpc->replyError(-32601, 'Метод не найден');
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function createBalance(array $params): void
    {
        $leadId = $params['leadId'] ?? $params['lead_id'] ?? null;
        if (!filter_var($leadId, FILTER_VALIDATE_INT)) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'ID Лида должен быть целым числом.']
            ]);
        }

        if (
            !filter_var($params['current'], FILTER_VALIDATE_INT)
            && !filter_var($params['drain'], FILTER_VALIDATE_INT)
            && !filter_var($params['potential'], FILTER_VALIDATE_INT)
        ) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Неверные данные']
            ]);
        }

        $balanceDto = BalanceMapper::fromArray($params);
        if ($balanceDto === null) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Некорректные данные для создания Баланса.']
            ]);
        }
        $executeResult = $this->balanceManagement->create()->execute($balanceDto);
        if ($executeResult->isSuccess()) {
            $current = $executeResult->getCurrent() ?? 0;
            $drain = $executeResult->getDrain() ?? 0;
            $potential = $executeResult->getPotential() ?? 0;
            $leadId = $executeResult->getLeadId() ?? 'Не указан';
            $this->rpc->replyData([
                ['type' => 'success', 'message' => 'Баланс успешно добавлен'],
                ['type' => 'info', 'message' => <<<HTML
                            Добавленный Balance:
                            <br>lead_id: <b>$leadId</b>
                            <br>Текущий баланс: <b>$current</b>
                            <br>drain: <b>$drain</b>
                            <br>potential: <b>$potential</b>
                        HTML
                ]
            ]);
        } else {
            $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Баланс не был добавлен. Причина: ' . $errorMsg]
            ]);
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function editBalance(array $params): void
    {
        $leadId = $params['leadId'] ?? $params['lead_id'] ?? null;
        if (!filter_var($leadId, FILTER_VALIDATE_INT)) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'ID Лида должен быть целым числом.']
            ]);
        }

        if (
            !filter_var($params['current'], FILTER_VALIDATE_INT)
            && !filter_var($params['drain'], FILTER_VALIDATE_INT)
            && !filter_var($params['potential'], FILTER_VALIDATE_INT)
        ) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Неверные данные']
            ]);
        }

        $params['lead_id'] = (int)$leadId;
        $balanceDto = BalanceMapper::fromArray($params);
        if ($balanceDto === null) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Некорректные данные для создания Баланса.']
            ]);
        }
        $executeResult = $this->balanceManagement->update()->executeByLeadId($balanceDto);
        if ($executeResult->isSuccess()) {
            $current = $executeResult->getCurrent() ?? 0;
            $drain = $executeResult->getDrain() ?? 0;
            $potential = $executeResult->getPotential() ?? 0;
            $leadId = $executeResult->getLeadId() ?? 'Не указан';

            $this->rpc->replyData([
               ['type' => 'success', 'message' => 'Баланс успешно обновлен'],
               ['type' => 'info', 'message' => <<<HTML
                            Измененный Balance:
                            <br>lead_id: <b>$leadId</b>
                            <br>Текущий баланс: <b>$current</b>
                            <br>drain: <b>$drain</b>
                            <br>potential: <b>$potential</b>
                        HTML
               ]
            ]);
        } else {
            $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Баланс не был обновлен. Причина: ' . $errorMsg]
            ]);
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function createOrEditBalance(array $params): void
    {
        $leadId = $params['leadId'] ?? $params['lead_id'] ?? null;
        if (!filter_var($leadId, FILTER_VALIDATE_INT)) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'ID Лида должен быть целым числом.']
            ]);
        }

        if (
            !filter_var($params['current'], FILTER_VALIDATE_INT)
            && !filter_var($params['drain'], FILTER_VALIDATE_INT)
            && !filter_var($params['potential'], FILTER_VALIDATE_INT)
        ) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Неверные данные']
            ]);
        }

        $params['lead_id'] = (int)$leadId;
        $balanceResult = $this->balanceManagement->get()->getByLeadId($leadId);
        if ($balanceResult->isSuccess()) {
            $this->editBalance($params);
        } else {
            $this->createBalance($params);
        }
    }
}
