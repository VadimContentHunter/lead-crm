<?php

namespace crm\src\controllers\API;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\services\TableRenderer\TableFacade;
use crm\src\_common\adapters\LeadValidatorAdapter;
use crm\src\services\TableRenderer\TableDecorator;
use crm\src\_common\repositories\BalanceRepository;
use crm\src\services\TableRenderer\TableRenderInput;
use crm\src\services\TableRenderer\TableTransformer;
use crm\src\_common\adapters\BalanceValidatorAdapter;
use crm\src\components\LeadManagement\_entities\Lead;
use crm\src\components\LeadManagement\LeadManagement;
use crm\src\components\BalanceManagement\BalanceManagement;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\_common\repositories\LeadRepository\LeadRepository;
use crm\src\components\LeadManagement\_common\mappers\LeadMapper;
use crm\src\_common\repositories\LeadRepository\LeadSourceRepository;
use crm\src\_common\repositories\LeadRepository\LeadStatusRepository;
use crm\src\components\LeadManagement\_common\mappers\LeadInputMapper;
use crm\src\components\BalanceManagement\_common\mappers\BalanceMapper;
use crm\src\components\LeadManagement\_common\mappers\LeadFilterMapper;
use crm\src\_common\repositories\LeadRepository\LeadAccountManagerRepository;

class LeadController
{
    private LeadManagement $leadManagement;

    private BalanceManagement $balanceManagement;

    private JsonRpcServerFacade $rpc;

    public function __construct(
        private string $projectPath,
        PDO $pdo,
        private LoggerInterface $logger = new NullLogger()
    ) {
        $this->logger->info('LeadController initialized for project ' . $this->projectPath);
        $leadRepository = new LeadRepository($pdo, $logger);
        $this->leadManagement = new LeadManagement(
            leadRepository:  $leadRepository,
            sourceRepository: new LeadSourceRepository($pdo, $logger),
            statusRepository: new LeadStatusRepository($pdo, $logger),
            accountManagerRepository: new LeadAccountManagerRepository($pdo, $logger),
            validator: new LeadValidatorAdapter()
        );

        $this->balanceManagement = new BalanceManagement(
            new BalanceRepository($pdo, $logger),
            new BalanceValidatorAdapter(),
            $leadRepository
        );

        $this->rpc = new JsonRpcServerFacade();
        switch ($this->rpc->getMethod()) {
            case 'lead.add':
                $this->createLead($this->rpc->getParams());
            // break;

            case 'lead.edit':
                $this->editLead($this->rpc->getParams());
            // break;

            case 'lead.filter':
                $this->filterLeads($this->rpc->getParams());
            // break;

            case 'lead.filter.table':
                $this->filterLeadsFormatTable($this->rpc->getParams());
            // break;

            case 'lead.filter.table.clear':
                $this->filterLeadsFormatTable([]);
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
            $leadInputDto = LeadInputMapper::fromArray($params);
            if ($leadInputDto === null) {
                $this->rpc->replyData([
                    ['type' => 'error', 'message' => 'Некорректные данные для создания лида.']
                ]);
            }

            $executeResult = $this->leadManagement->create()->execute($leadInputDto);
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
            $leadInputDto = LeadInputMapper::fromArray($params);
            if ($leadInputDto === null) {
                $this->rpc->replyData([
                    ['type' => 'error', 'message' => 'Некорректные данные для создания лида.']
                ]);
            }
            $executeResult = $this->leadManagement->update()->execute($leadInputDto);
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

    /**
     * @param array<string, mixed> $params
     */
    public function filterLeads(array $params): void
    {
        $executeResult = $this->leadManagement->get()->filtered(LeadFilterMapper::fromArray($params));
        if ($executeResult->isSuccess()) {
            // $balance = $this->balanceManagement->get()->getByLeadId($executeResult->getArray()[0]['id']);
            $leadBalanceItem = $executeResult->getValidMappedList(function (array $lead) {
                $balance = $this->balanceManagement
                    ->get()
                    ->getByLeadId($lead['id'] ?? 0)
                    ->first()
                    ->mapData([BalanceMapper::class, 'toArray']);

                return array_merge($lead, $balance ?? []);
            });

            $this->rpc->replyData([
                ['type' => 'success', 'leads' => $leadBalanceItem->getArray()]
            ]);
        } else {
            $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Ошибка при фильтрации. Причина: ' . $errorMsg]
            ]);
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function filterLeadsFormatTable(array $params): void
    {
        $executeResult = $this->leadManagement->get()->filteredWithHydrate(LeadFilterMapper::fromArray($params));
        if ($executeResult->isSuccess()) {
            $leadBalanceItems = $executeResult->mapEach(function (Lead|array $lead) {
                $lead = LeadMapper::toFlatViewArray($lead);
                $balance = $this->balanceManagement
                    ->get()
                    ->getByLeadId($lead['id'] ?? 0)
                    ->first()
                    ->mapData([BalanceMapper::class, 'toArray']);

                return array_merge(LeadMapper::toFlatViewArray($lead), $balance ?? []);
            });

            $headers = array_merge(
                array_keys(LeadMapper::toFlatViewArray(
                    $this->leadManagement->get()->executeColumnNames()->getArray()
                )),
                $this->balanceManagement->get()->executeColumnNames()->getArray()
            );
            // $headers = array_values(array_unique(array_merge(
            //     $this->leadManagement->get()->executeColumnNames()->getArray(),
            //     $this->balanceManagement->get()->executeColumnNames()->getArray()
            // )));

            $input = new TableRenderInput(
                header: $headers,
                rows: $leadBalanceItems->getArray(),
                attributes: ['id' => 'lead-table-1', 'data-module' => 'leads'],
                classes: ['base-table'],
                hrefButton: '/page/lead-edit',
                allowedColumns: [
                    'id',
                    'contact',
                    'full_name',
                    'account_manager',
                    'address',
                    'source',
                    'status',
                    'current',
                    'drain',
                    'potential',
                ],
                renameMap: [
                    'full_name' => 'Полное имя',
                    'account_manager' => 'Менеджер',
                    'contact' => 'Контакт',
                    'address' => 'Адрес',
                    'source' => 'Источник',
                    'status' => 'Статус',
                    'current' => 'Текущие',
                    'drain' => 'Потери',
                    'potential' => 'Потенциал',
                ]
            );

            $tableFacade = new TableFacade(new TableTransformer(),  new TableDecorator());
            $this->rpc->replyData([
                'type' => 'success',
                'table' => $tableFacade->renderFilteredTable($input)->asHtml()
            ]);
        } else {
            $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Ошибка при фильтрации. Причина: ' . $errorMsg]
            ]);
        }
    }
}
