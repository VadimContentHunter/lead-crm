<?php

namespace crm\src\controllers\API\Invest;

use Throwable;
use crm\src\services\AppContext\ISecurity;
use crm\src\services\AppContext\IAppContext;
use crm\src\components\UserManagement\_entities\User;
use crm\src\Investments\_application\InvestmentService;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\components\UserManagement\_common\mappers\UserMapper;
use crm\src\components\Security\_exceptions\JsonRpcSecurityException;

class InvActivityController
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
             * @var InvActivityController $secureCall
             */
            $secureCall = $this->appContext->wrapWithSecurity($this);
        } else {
            $secureCall = $this;
        }

        $this->methods = [
            'active.get.form' => fn() => $secureCall->getFormActivityData($this->rpc->getParams()),
            'active.add' => fn() => $secureCall->createInvActivity($this->rpc->getParams()),
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
    public function createInvActivity(array $params): void
    {
        $resultActivity = $this->service->createActivity($params);

        if ($resultActivity->isSuccess()) {
            $uid = $resultActivity->getLeadUid() ?? '---';
            $getHash = $resultActivity->getHash() ?? '---';
            $pair = $resultActivity->getPair() ?? '---';
            $amount = $resultActivity->getAmount() ?? '---';
            $result = $resultActivity->getResult() ?? '---';
            $type = $resultActivity->getType() ?? '---';

            $info = <<<HTML
                Добавленная активность:
                <br> UID: <b>{$uid}</b>
                <br> hash: <b>{$getHash}</b>
                <br> пара: <b>{$pair}</b>
                <br> сумма: <b>{$amount}</b>
                <br> результат: <b>{$result}</b>
                <br> тип: <b>{$type}</b>
            HTML;

            $this->rpc->replyData([
                'type' => 'success',
                'table' => $this->service->getActivityTable()->getString() ?? '---',
                'messages' => [
                    ['type' => 'success', 'message' => 'Лид успешно добавлен'],
                    ['type' => 'info', 'message' => $info]
                ]
            ]);
        } else {
            $errorMessage = $resultActivity->getError()?->getMessage() ?? 'Произошла ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Произошла ошибка: ' . $errorMessage]
            ]);
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function getFormActivityData(array $params): void
    {
        $result = $this->service->getActivityData($params);
        if ($result->isSuccess()) {
            $this->rpc->replyData([
                'type' => 'success',
                'data' =>  $result->getData()
            ]);
        } else {
            $errorMessage = $result->getError()?->getMessage() ?? 'Произошла ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Произошла ошибка: ' . $errorMessage]
            ]);
        }
    }
}
