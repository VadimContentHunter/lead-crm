<?php

namespace crm\src\controllers\API;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\services\AppContext\ISecurity;
use crm\src\services\AppContext\IAppContext;
use crm\src\_common\repositories\BalanceRepository;
use crm\src\_common\adapters\BalanceValidatorAdapter;
use crm\src\components\BalanceManagement\BalanceManagement;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\_common\repositories\LeadRepository\LeadRepository;
use crm\src\components\Security\_exceptions\JsonRpcSecurityException;
use crm\src\components\BalanceManagement\_common\mappers\BalanceMapper;

class BalanceController
{
    private BalanceManagement $balanceManagement;

    private JsonRpcServerFacade $rpc;

    /**
     * @var array<string,callable>
     */
    private array $methods = [];

    public function __construct(
        private IAppContext $appContext
    ) {
        $this->balanceManagement = $this->appContext->getBalanceManagement();

        $this->rpc = $this->appContext->getJsonRpcServerFacade();

        $this->initMethodMap();
        $this->init();
    }

    private function initMethodMap(): void
    {
        if ($this->appContext instanceof ISecurity) {
            /**
             * @var BalanceController $secureCall
             */
            $secureCall = $this->appContext->wrapWithSecurity($this);
        } else {
            $secureCall = $this;
        }

        $this->methods = [
            'balance.add' => fn() => $secureCall->createBalance($this->rpc->getParams()),
            'comment.edit' => fn() => $secureCall->editBalance($this->rpc->getParams()),
            'balance.create.edit' => fn() => $secureCall->createOrEditBalance($this->rpc->getParams()),
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
