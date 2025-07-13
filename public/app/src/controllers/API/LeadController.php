<?php

namespace crm\src\controllers\API;

use Throwable;
use crm\src\controllers\LeadPage;
use crm\src\services\LeadCommentService;
use crm\src\services\AppContext\ISecurity;
use crm\src\services\AppContext\IAppContext;
use crm\src\components\LeadManagement\_entities\Lead;
use crm\src\components\LeadManagement\LeadManagement;
use crm\src\components\BalanceManagement\BalanceManagement;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\components\LeadManagement\_common\mappers\LeadMapper;
use crm\src\components\Security\_exceptions\JsonRpcSecurityException;
use crm\src\components\LeadManagement\_common\mappers\LeadInputMapper;
use crm\src\components\BalanceManagement\_common\mappers\BalanceMapper;
use crm\src\components\LeadManagement\_common\mappers\LeadFilterMapper;

class LeadController
{
    private LeadManagement $leadManagement;

    private BalanceManagement $balanceManagement;

    private JsonRpcServerFacade $rpc;

    private LeadCommentService $leadCommentService;

    private LeadPage $leadPage;

    /**
     * @var array<string,callable>
     */
    private array $methods = [];

    public function __construct(
        private IAppContext $appContext
    ) {
        $this->leadManagement = $this->appContext->getLeadManagement();

        $this->balanceManagement = $this->appContext->getBalanceManagement();

        $this->rpc = $this->appContext->getJsonRpcServerFacade();

        $this->leadCommentService = $this->appContext->getLeadCommentService();

        $this->leadPage = new LeadPage($this->appContext);

        $this->initMethodMap();
        $this->init();
    }

    private function initMethodMap(): void
    {
        if ($this->appContext instanceof ISecurity) {
            /**
             * @var LeadController $secureCall
             */
            $secureCall = $this->appContext->wrapWithSecurity($this);
        } else {
            $secureCall = $this;
        }

        $this->methods = [
            'lead.add'                => fn() => $secureCall->createLead($this->rpc->getParams()),
            'lead.edit'               => fn() => $secureCall->editLead($this->rpc->getParams()),
            'lead.delete'             => fn() => $secureCall->deleteLead($this->rpc->getParams()),
            'lead.filter'             => fn() => $secureCall->filterLeads($this->rpc->getParams()),
            'lead.filter.table'       => fn() => $secureCall->filterLeadsFormatTable($this->rpc->getParams()),
            'lead.filter.table.clear' => fn() => $secureCall->filterLeadsFormatTable([]),
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
                $this->leadCommentService->sendComment(
                    $executeResult->getId() ?? 0,
                    'Лид создан (ID: ' . $executeResult->getId() . ')'
                );

                $fullName = $executeResult->getFullName() ?? 'не указано имя';
                $contact = $executeResult->getContact() ?? 'не указан контакт';
                $address = $executeResult->getAddress() ?? 'не указан адрес';
                $sourceTitle = $executeResult->getSourceTitle() ?? 'не указан источник';
                $statusTitle = $executeResult->getStatusTitle() ?? 'не указан статус';
                $accountManagerLogin = $executeResult->getAccountManagerLogin() ?? 'не указан менеджер';

                $this->filterLeadsFormatTable([], [
                    'messages' => [
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

            $oldData = $this->leadManagement->get()->byId($id)->getLead();
            $executeResult = $this->leadManagement->update()->execute($leadInputDto);
            if ($executeResult->isSuccess()) {
                $this->leadCommentService->compareObjects(
                    $oldData,
                    $executeResult->getLead(),
                    (int)$id,
                    'Лид обновлен: ID (' . $id . ')'
                );

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
     * @param array<string,mixed> $params
     */
    public function deleteLead(array $params): void
    {
        $id = $params['row_id'] ?? $params['rowId'] ?? $params['id'] ?? null;
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'ID Lead должен быть целым числом.']
            ]);
        }

        $executeResult = $this->leadManagement->delete()->byId((int)$id);
        if ($executeResult->isSuccess()) {
            // $this->leadCommentService->logDelete($executeResult->getLead());
            $this->leadCommentService->sendComment((int)$id, 'Лид удалён (ID: ' . (int)$id . ')');
            $this->filterLeadsFormatTable([], [
                'messages' => [
                    ['type' => 'success', 'message' => 'Лид (ID: ' . (int)$id . ') был успешно удалён']
                ]
            ]);
        } else {
            $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Пользователь не удалён. Причина: ' . $errorMsg]
            ]);
        }
    }

    /**
     * @param mixed[] $params
     *
     * @return string|mixed[]
     */
    public function getFilteredLeads(array $params): string|array
    {
        $executeResult = $this->leadManagement->get()->filteredWithHydrate(LeadFilterMapper::fromArray($params));
        if ($executeResult->isSuccess()) {
            $leadBalanceItems = $executeResult->mapEach(function (Lead|array $lead) {
                $newLead = LeadMapper::toFlatViewArray($lead);
                $balance = $this->balanceManagement
                    ->get()
                    ->getByLeadId($newLead['id'] ?? 0)
                    ->first()
                    ->mapData([BalanceMapper::class, 'toArray']);
                    unset($balance['id']);
                return array_merge($newLead, $balance ?? []);
            });

            return $leadBalanceItems->getArray();
        }
        $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
        return $errorMsg;
    }

    /**
     * @param array<string,mixed> $params
     * @param array<string,mixed> $resultMetadata
     */
    public function filterLeads(array $params, array $resultMetadata = []): void
    {
        $result = $this->getFilteredLeads($params);
        if (is_array($result)) {
            $this->rpc->replyData([array_merge(
                ['type' => 'success', 'leads' => $result],
                $resultMetadata
            )]);
        }

        $this->rpc->replyData([array_merge(
            ['type' => 'error', 'message' => "Ошибка при фильтрации. Причина: " . $result],
            $resultMetadata
        )]);
    }

    /**
     * @param array<string,mixed> $params
     * @param array<string,mixed> $resultMetadata
     */
    public function filterLeadsFormatTable(array $params, array $resultMetadata = []): void
    {
        $executeResult = $this->leadManagement->get()->filteredWithHydrate(LeadFilterMapper::fromArray($params));
        if ($executeResult->isSuccess()) {
            $this->rpc->replyData(array_merge(
                [
                    'type' => 'success',
                    'table' => $this->leadPage->getRenderTable($executeResult)
                ],
                $resultMetadata
            ));
        } else {
            $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
            $this->rpc->replyData([array_merge(
                ['type' => 'error', 'message' => "Ошибка при фильтрации. Причина: " . $errorMsg],
                $resultMetadata
            )]);
        }
    }
}
