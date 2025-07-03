<?php

namespace crm\src\controllers\API;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\_common\adapters\LeadValidatorAdapter;
use crm\src\components\LeadManagement\LeadManagement;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\_common\repositories\LeadRepository\LeadRepository;
use crm\src\_common\repositories\LeadRepository\LeadSourceRepository;
use crm\src\_common\repositories\LeadRepository\LeadStatusRepository;
use crm\src\_common\repositories\LeadRepository\LeadAccountManagerRepository;

class LeadController
{
    private LeadManagement $sourceManagement;

    private JsonRpcServerFacade $rpc;

    public function __construct(
        private string $projectPath,
        PDO $pdo,
        private LoggerInterface $logger = new NullLogger()
    ) {
        $this->sourceManagement = new LeadManagement(
            leadRepository: new LeadRepository($pdo, $logger),
            sourceRepository: new LeadSourceRepository($pdo, $logger),
            statusRepository: new LeadStatusRepository($pdo, $logger),
            accountManagerRepository: new LeadAccountManagerRepository($pdo, $logger),
            validator: new LeadValidatorAdapter()
        );

        $this->rpc = new JsonRpcServerFacade();
        switch ($this->rpc->getMethod()) {
            case 'lead.add':
                $this->createLead($this->rpc->getParams());
            // break;

            default:
                $this->rpc->replyError(-32601, 'Метод не найден');
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function createLead(array $params): void
    {
        if (
            is_string($params['login'] ?? null)
            && is_string($params['password'] ?? null)
            && is_string($params['password_confirm'] ?? null)
        ) {
        }
        // if (is_string($params['title'] ?? null)) {
        //     $executeResult = $this->sourceManagement->create()->execute($params['title']);
        //     $title =  $executeResult->getTitle() ?? 'неизвестный источник';
        //     if ($executeResult->isSuccess()) {
        //         $this->rpc->replyData([
        //             ['type' => 'success', 'message' => 'Источник успешно добавлен'],
        //             ['type' => 'info', 'message' => "Добавленный источник: <b>{$title}</b>"]
        //         ]);
        //     } else {
        //         $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
        //         $this->rpc->replyData([
        //         ['type' => 'error', 'message' => 'Источник не добавлен. Причина: ' . $errorMsg]
        //         ]);
        //     }
        // } else {
        //     $this->rpc->replyData([
        //         ['type' => 'error', 'message' => 'Данные источника некорректного формата.']
        //     ]);
        // }
    }
}
