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

class InvLeadController
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
             * @var InvLeadController $secureCall
             */
            $secureCall = $this->appContext->wrapWithSecurity($this);
        } else {
            $secureCall = $this;
        }

        $this->methods = [
            'invest.lead.add' => fn() => $secureCall->createInvLead($this->rpc->getParams()),
            'invest.lead.get.form.create' => fn() => $secureCall->getFormCreateData($this->rpc->getParams()),
            'invest.lead.get.balance' => fn() => $secureCall->getBalance($this->rpc->getParams()),
            'invest.lead.update' => fn() => $secureCall->updateInvLead($this->rpc->getParams()),
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
    public function createInvLead(array $params): void
    {
        $result = $this->service->createInvLead($params);

        if ($result->isSuccess()) {
            $uid = $result->getUid();
            $fullName = $result->getFullName() ?? '---';
            $contact = $result->getContact() ?? '---';
            $email = $result->getEmail() ?? '---';
            $phone = $result->getPhone() ?? '---';
            $sourceTitle = $result->getSource()->label ?? '---';
            $statusTitle = $result->getStatus()->label ?? '---';
            $accountManagerLogin = $result->getAccountManager()->login ?? '---';

            $info = <<<HTML
                            Добавленный Лид:
                            <br> UID: <b>{$uid}</b>
                            <br> полное имя: <b>{$fullName}</b>
                            <br> контакт: <b>{$contact}</b>
                            <br> email: <b>{$email}</b>
                            <br> телефон: <b>{$phone}</b>
                            <br> источник: <b>{$sourceTitle}</b>
                            <br> статус: <b>{$statusTitle}</b>
                            <br> менеджер: <b>{$accountManagerLogin}</b>
                        HTML;

            $this->rpc->replyData([
                'type' => 'success',
                'table' => $this->service->getInvLeadTable()->getString() ?? '---',
                'messages' => [
                    ['type' => 'success', 'message' => 'Лид успешно добавлен'],
                    ['type' => 'info', 'message' => $info]
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
    public function updateInvLead(array $params): void
    {
        $result = $this->service->updateInvLead($params);

        if ($result->isSuccess()) {
            $uid = $result->getUid();
            $fullName = $result->getFullName() ?? '---';
            $contact = $result->getContact() ?? '---';
            $email = $result->getEmail() ?? '---';
            $phone = $result->getPhone() ?? '---';
            $sourceTitle = $result->getSource()->label ?? '---';
            $statusTitle = $result->getStatus()->label ?? '---';
            $accountManagerLogin = $result->getAccountManager()->login ?? '---';

            $info = <<<HTML
                            Добавленный Лид:
                            <br> UID: <b>{$uid}</b>
                            <br> полное имя: <b>{$fullName}</b>
                            <br> контакт: <b>{$contact}</b>
                            <br> email: <b>{$email}</b>
                            <br> телефон: <b>{$phone}</b>
                            <br> источник: <b>{$sourceTitle}</b>
                            <br> статус: <b>{$statusTitle}</b>
                            <br> менеджер: <b>{$accountManagerLogin}</b>
                        HTML;

            $this->rpc->replyData([
                'type' => 'success',
                'table' => $this->service->getInvLeadTable()->getString() ?? '---',
                'messages' => [
                    ['type' => 'success', 'message' => 'Лид успешно обновлен'],
                    ['type' => 'info', 'message' => $info]
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
    public function getFormCreateData(array $params): void
    {
        $accountManagerFunction = function (int $userId) {
            // return $this->appContext->getUserManagement()->get()->executeById($userId)->getLogin() ?? '—';

            $result = $this->appContext->getUserManagement()->get()->executeAll()->mapEach(
                function (array|User $user) use ($userId) {
                    $userArray = is_array($user) ? $user : UserMapper::toArray($user);
                    return [
                        'value' => $userArray['id'],
                        'text' => $userArray['login'],
                        'selected' => $userArray['id'] === $userId,
                    ];
                }
            )->getArray();
            // array_unshift($result, ['value' => '', 'text' => '— Выберите менеджера —', 'selected' => true]);
            return $result;
        };

        $result = $this->service->getFormCreateData(
            $params,
            $accountManagerFunction,
        );
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

     /**
      * @param array<string,mixed> $params
      */
    public function getBalance(array $params): void
    {
        $result = $this->service->getBalanceData($params);
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

    /**
     * @param array<string,mixed> $params
     */
    // public function editSourceCell(array $params): void
    // {
    //     $this->service->updateSource($params);
    // }
}
