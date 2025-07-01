<?php

namespace crm\src\controllers;

use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;

class ApiController
{
    public function __construct()
    {
        $rpc = new JsonRpcServerFacade();

        // $t = $_COOKIE ?? [];

        switch ($rpc->getMethod()) {
            case 'user.add':
                $params = $rpc->getParams();

                if (($params['password'] ?? '') !== ($params['password_confirm'] ?? '')) {
                    $rpc->replyData([
                        ['type' => 'error', 'message' => 'Пароли не совпадают.']
                    ]);
                } else {
                    $rpc->replyData([
                        ['type' => 'success', 'message' => 'Пользователь добавлен']
                    ]);
                }
                // break;

            case 'page.update':
            // вернуть новый контент
                $rpc->replyContentUpdate('main.main-content', '<p>Обновлено</p>');
                // break;

            default:
                $rpc->replyError(-32601, 'Метод не найден');
        }
    }
}
