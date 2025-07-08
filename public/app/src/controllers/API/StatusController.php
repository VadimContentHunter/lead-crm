<?php

namespace crm\src\controllers\API;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\services\AppContext\ISecurity;
use crm\src\services\AppContext\IAppContext;
use crm\src\_common\repositories\StatusRepository;
use crm\src\_common\adapters\StatusValidatorAdapter;
use crm\src\components\StatusManagement\StatusManagement;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\components\Security\_exceptions\JsonRpcSecurityException;

class StatusController
{
    private StatusManagement $statusManagement;

    private JsonRpcServerFacade $rpc;

    /**
     * @var array<string, callable>
     */
    private array $methods = [];

    public function __construct(
        private IAppContext $appContext,
    ) {
        $this->statusManagement = $this->appContext->getStatusManagement();

        $this->rpc = $this->appContext->getJsonRpcServerFacade();
        switch ($this->rpc->getMethod()) {
            case 'status.add':
                $this->createStatus($this->rpc->getParams());
            // break;

            default:
                $this->rpc->replyError(-32601, 'Метод не найден');
        }
    }

    private function initMethodMap(): void
    {
        if ($this->appContext instanceof ISecurity) {
            /**
             * @var UserController $secureCall
             */
            $secureCall = $this->appContext->wrapWithSecurity($this);
        } else {
            $secureCall = $this;
        }

        $this->methods = [
            'user.add'                => fn() => $secureCall->createUser($this->rpc->getParams()),
            'user.edit'               => fn() => $secureCall->editUser($this->rpc->getParams()),
            'user.delete'             => fn() => $secureCall->deleteUser($this->rpc->getParams()),
            'user.filter'             => fn() => $secureCall->filterUsers($this->rpc->getParams()),
            'user.filter.table'       => fn() => $secureCall->filterUsersFormatTable($this->rpc->getParams()),
            'user.filter.table.clear' => fn() => $secureCall->filterUsersFormatTable([]),
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
}
