<?php

namespace crm\src\controllers\API;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\_common\repositories\SourceRepository;
use crm\src\_common\adapters\SourceValidatorAdapter;
use crm\src\components\SourceManagement\SourceManagement;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;

class SourceController
{
    private SourceManagement $sourceManagement;

    private JsonRpcServerFacade $rpc;

    public function __construct(
        private string $projectPath,
        PDO $pdo,
        private LoggerInterface $logger = new NullLogger()
    ) {
        $this->logger->info('SourceController initialized for project ' . $this->projectPath);
        $this->sourceManagement = new SourceManagement(
            new SourceRepository($pdo, $logger),
            new SourceValidatorAdapter()
        );

        $this->rpc = new JsonRpcServerFacade();
        switch ($this->rpc->getMethod()) {
            case 'source.add':
                $this->createSource($this->rpc->getParams());
            // break;

            default:
                $this->rpc->replyError(-32601, 'Метод не найден');
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function createSource(array $params): void
    {
        if (is_string($params['title'] ?? null)) {
            $executeResult = $this->sourceManagement->create()->execute($params['title']);
            $title =  $executeResult->getTitle() ?? 'неизвестный источник';
            if ($executeResult->isSuccess()) {
                $this->rpc->replyData([
                    ['type' => 'success', 'message' => 'Источник успешно добавлен'],
                    ['type' => 'info', 'message' => "Добавленный источник: <b>{$title}</b>"]
                ]);
            } else {
                $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
                $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Источник не добавлен. Причина: ' . $errorMsg]
                ]);
            }
        } else {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Данные источника некорректного формата.']
            ]);
        }
    }
}
