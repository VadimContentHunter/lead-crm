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
use crm\src\components\LeadManagement\_common\mappers\LeadInputMapper;
use crm\src\_common\repositories\LeadRepository\LeadAccountManagerRepository;

class LeadController
{
    private LeadManagement $leadManagement;

    private JsonRpcServerFacade $rpc;

    public function __construct(
        private string $projectPath,
        PDO $pdo,
        private LoggerInterface $logger = new NullLogger()
    ) {
        $this->leadManagement = new LeadManagement(
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

            case 'lead.edit':
                $this->editLead($this->rpc->getParams());
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
            is_string($params['fullName'] ?? null)
            && is_string($params['contact'] ?? null)
        ) {
            $executeResult = $this->leadManagement->create()->execute(LeadInputMapper::fromArray($params));

            if ($executeResult->isSuccess()) {
                $fullName = $executeResult->getFullName() ?? 'не указано имя';
                $contact = $executeResult->getContact() ?? 'не указан контакт';
                $address = $executeResult->getAddress() ?? 'не указан адрес';
                $sourceTitle = $executeResult->getSourceTitle() ?? 'не указан источник';
                $statusTitle = $executeResult->getStatusTitle() ?? 'не указан статус';
                $accountManagerLogin = $executeResult->getAccountManagerLogin() ?? 'не указан менеджер';

                $this->rpc->replyData([
                    ['type' => 'success', 'message' => 'Лид успешно добавлен'],
                    ['type' => 'info', 'message' => <<<HTML
                            Добавленный Лид:
                            <br> полное имя: <b>{$fullName}</b>
                            <br> контакт: <b>{$contact}</b>
                            <br> адрес: <b>{$address}</b>
                            <br> источник: <b>{$sourceTitle}</b>
                            <br> статус: <b>{$statusTitle}</b>
                            <br> менеджер: <b>{$accountManagerLogin}</b>
                        HTML
                    ]
                ]);
            } else {
                $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
                $this->rpc->replyData([
                    ['type' => 'error', 'message' => 'Лид не был добавлен. Причина: ' . $errorMsg]
                ]);
            }
        } else {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Данные источника некорректного формата.']
            ]);
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function editLead(array $params): void
    {
        $id = $params['leadId'] ?? $params['id'] ?? null;
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'ID Лида должен быть целым числом.']
            ]);
        }

        $params['id'] = (int)$id;
        if (
            is_string($params['fullName'] ?? null)
            && is_string($params['contact'] ?? null)
        ) {
            $executeResult = $this->leadManagement->update()->execute(LeadInputMapper::fromArray($params));
            if ($executeResult->isSuccess()) {
                $fullName = $executeResult->getFullName() ?? 'не указано имя';
                $contact = $executeResult->getContact() ?? 'не указан контакт';
                $address = $executeResult->getAddress() ?? 'не указан адрес';
                $sourceTitle = $executeResult->getSourceTitle() ?? 'не указан источник';
                $statusTitle = $executeResult->getStatusTitle() ?? 'не указан статус';
                $accountManagerLogin = $executeResult->getAccountManagerLogin() ?? 'не указан менеджер';

                $this->rpc->replyData([
                   ['type' => 'success', 'message' => 'Лид успешно обновлен'],
                   ['type' => 'info', 'message' => <<<HTML
                            Добавленный Лид:
                            <br> полное имя: <b>{$fullName}</b>
                            <br> контакт: <b>{$contact}</b>
                            <br> адрес: <b>{$address}</b>
                            <br> источник: <b>{$sourceTitle}</b>
                            <br> статус: <b>{$statusTitle}</b>
                            <br> менеджер: <b>{$accountManagerLogin}</b>
                        HTML
                   ]
                ]);
            } else {
                $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
                $this->rpc->replyData([
                    ['type' => 'error', 'message' => 'Лид не был обновлен. Причина: ' . $errorMsg]
                ]);
            }
        } else {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Данные источника некорректного формата.']
            ]);
        }
    }
}
