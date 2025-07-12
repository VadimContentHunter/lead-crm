<?php

namespace crm\src\controllers\API;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\services\AppContext\IAppContext;
use crm\src\_common\repositories\UserRepository;
use crm\src\_common\adapters\UserValidatorAdapter;
use crm\src\components\Security\SessionAuthManager;
use crm\src\components\UserManagement\UserManagement;
use crm\src\_common\repositories\AccessContextRepository;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\components\Security\_handlers\HandleAccessContext;
use crm\src\components\UserManagement\_common\interfaces\IUserManagement;

class LoginController
{
    private JsonRpcServerFacade $rpc;

    private SessionAuthManager $sessionAuthManager;

    private HandleAccessContext $handleAccessContext;

    private IUserManagement $userManagement;

    public function __construct(
        private IAppContext $appContext
    ) {

        $this->sessionAuthManager = $this->appContext->getSessionAuthManager();
        $this->handleAccessContext = $this->appContext->getHandleAccessContext();
        $this->userManagement = $this->appContext->getUserManagement();

        $this->rpc = $this->appContext->getJsonRpcServerFacade();
        switch ($this->rpc->getMethod()) {
            case 'auth.login':
                $this->login($this->rpc->getParams());
            // break;

            case 'auth.logout':
                $this->logout();
            // break;

            default:
                $this->rpc->replyError(-32601, 'Метод не найден');
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function login(array $params): void
    {
        if (
            is_string($params['login'] ?? null)
            && is_string($params['password'] ?? null)
        ) {
            $user = $this->userManagement->get()->executeByLogin($params['login']);
            if (!$user->isSuccess()) {
                $this->rpc->replyData([
                    ['type' => 'error', 'message' => 'Пользователь не найден']
                ]);
            }

            if (!password_verify($params['password'],  $user->getPasswordHash() ?? '')) {
                $this->rpc->replyData([
                    ['type' => 'error', 'message' => 'Некорректный пароль']
                ]);
            }

            // if (!$this->handleAccessContext->verifySessionHash($user->getId() ?? 0, $user->getLogin() ?? '', $user->getPasswordHash() ?? '')) {
            //     $this->rpc->replyData([
            //         ['type' => 'error', 'message' => 'Некорректная сессия']
            //     ]);
            // }

            $isUpdateSessionHash = $this->handleAccessContext->updateSessionHash(
                $user->getId() ?? 0,
                $user->getLogin() ?? '',
                $user->getPasswordHash() ?? ''
            );
            if (!$isUpdateSessionHash) {
                $this->rpc->replyData([
                    ['type' => 'error', 'message' => 'Не удалось обновить сессию']
                ]);
            }

            $sessionHash = $this->handleAccessContext->getSessionHashByUserId($user->getId() ?? 0);
            if (!$sessionHash) {
                $this->rpc->replyData([
                   ['type' => 'error', 'message' => 'Не удалось получить сессию']
                ]);
            }

            $this->sessionAuthManager->login($sessionHash);
            $this->rpc->replyData([
                ['type' => 'success', 'message' => 'Успешная авторизация'],
                ['type' => 'redirect', 'url' => '/p2p'],
            ]);
        } else {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Данные источника некорректного формата.']
            ]);
        }
    }

    public function logout(): void
    {
        $this->sessionAuthManager->logout();
        $this->rpc->replyData([
            ['type' => 'redirect', 'url' => '/login'],
        ]);
    }
}
