<?php

namespace crm\src\controllers\API;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\_common\repositories\StatusRepository;
use crm\src\_common\adapters\StatusValidatorAdapter;
use crm\src\components\StatusManagement\StatusManagement;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;

class StatusController
{
    private StatusManagement $statusManagement;

    private JsonRpcServerFacade $rpc;

    public function __construct(
        private string $projectPath,
        PDO $pdo,
        private LoggerInterface $logger = new NullLogger()
    ) {
        $this->logger->info('StatusController initialized for project ' . $this->projectPath);
        $this->statusManagement = new StatusManagement(
            new StatusRepository($pdo, $logger),
            new StatusValidatorAdapter()
        );

        $this->rpc = new JsonRpcServerFacade();
        switch ($this->rpc->getMethod()) {
            case 'status.add':
                $this->createStatus($this->rpc->getParams());
            // break;

            default:
                $this->rpc->replyError(-32601, 'Метод не найден');
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
