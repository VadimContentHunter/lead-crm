<?php

namespace crm\src\controllers\API;

use crm\src\services\AppContext\ISecurity;
use crm\src\services\AppContext\IAppContext;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;

class ApiController
{
    /**
     * @var array<string, callable>
     */
    private array $methods = [];

    private JsonRpcServerFacade $rpc;

    public function __construct(
        private IAppContext $appContext,
    ) {
        $this->rpc = $this->appContext->getJsonRpcServerFacade();

        $this->initMethodMap();
        $this->init();
    }

    private function initMethodMap(): void
    {
        $this->methods = [
            'error' => fn() => $this->getError($this->rpc->getParams())
        ];
    }

    public function init(): void
    {
         $method = $this->rpc->getMethod();

        if (!isset($this->methods[$method])) {
            $this->rpc->replyError(-32601, 'Метод не найден');
            return;
        }

        ($this->methods[$method])();
    }

    public function getError(array $params): void
    {
        $a = $_GET;
        $this->rpc->replyError($params['code'] ?? 100, $params['message'] ?? 'Неизвестная ошибка');
    }
}
