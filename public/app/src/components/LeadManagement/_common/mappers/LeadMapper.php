<?php

namespace crm\src\components\LeadManagement\_common\mappers;

use crm\src\components\LeadManagement\_entities\Lead;
use crm\src\components\LeadManagement\_common\DTOs\SourceDto;
use crm\src\components\LeadManagement\_common\DTOs\StatusDto;
use crm\src\components\LeadManagement\_common\DTOs\AccountManagerDto;
use DateTime;

class LeadMapper
{
    /**
     * Преобразует массив данных из БД в сущность Lead.
     *
     * @param  array<string, mixed> $data
     * @return Lead
     */
    public static function fromArray(array $data): Lead
    {
        return new Lead(
            fullName: (string)($data['full_name'] ?? ''),
            contact: (string)($data['contact'] ?? ''),
            address: (string)($data['address'] ?? ''),
            source: isset($data['source_id'])
                ? new SourceDto((int)$data['source_id'], '') // Можно заменить на SourceDtoMapper::fromArray() позже
                : null,
            status: isset($data['status_id'])
                ? new StatusDto((int)$data['status_id'], '')
                : null,
            accountManager: isset($data['account_manager_id'])
                ? new AccountManagerDto((int)$data['account_manager_id'], '')
                : null,
            createdAt: isset($data['created_at'])
                ? new DateTime($data['created_at'])
                : null,
            id: isset($data['id']) ? (int)$data['id'] : null,
            groupName: isset($data['group_name']) ? (string)$data['group_name'] : null
        );
    }

    /**
     * Преобразует сущность Lead в массив для БД.
     *
     * @param  Lead $lead
     * @return array<string, mixed>
     */
    public static function toArray(Lead $lead): array
    {
        return [
            'id' => $lead->id,
            'full_name' => $lead->fullName,
            'contact' => $lead->contact,
            'address' => $lead->address,
            'source_id' => $lead->source?->id,
            'status_id' => $lead->status?->id,
            'account_manager_id' => $lead->accountManager?->id,
            'created_at' => $lead->createdAt?->format('Y-m-d H:i:s'),
            'group_name' => $lead->groupName
        ];
    }

    /**
     * Преобразует сущность Lead в полный массив со всеми вложенными структурами.
     *
     * @param  Lead $lead
     * @return array<string, mixed>
     */
    public static function toFullArray(Lead $lead): array
    {
        return [
            'id' => $lead->id,
            'full_name' => $lead->fullName,
            'contact' => $lead->contact,
            'address' => $lead->address,
            'source' => $lead->source instanceof SourceDto
                ? SourceDtoMapper::toArray($lead->source)
                : null,
            'status' => $lead->status instanceof StatusDto
                ? StatusDtoMapper::toArray($lead->status)
                : null,
            'account_manager' => $lead->accountManager instanceof AccountManagerDto
                ? AccountManagerMapper::toArray($lead->accountManager)
                : null,
            'created_at' => $lead->createdAt?->format('Y-m-d H:i:s'),
            'group_name' => $lead->groupName,
        ];
    }


    /**
     * Извлекает только указанные поля из Lead или массива.
     *
     * @param  Lead|array<string, mixed> $lead
     * @param  string[] $fields
     * @return array<string, mixed>
     */
    public static function extractFields(Lead|array $lead, array $fields): array
    {
        $result = [];

        foreach ($fields as $field) {
            $value = null;

            if ($lead instanceof Lead) {
                $value = match ($field) {
                    'id' => $lead->id,
                    'full_name' => $lead->fullName,
                    'contact' => $lead->contact,
                    'address' => $lead->address,
                    'source_id' => $lead->source?->id,
                    'status_id' => $lead->status?->id,
                    'account_manager_id' => $lead->accountManager?->id,
                    'created_at' => $lead->createdAt?->format('Y-m-d H:i:s'),
                    'group_name' => $lead->groupName,
                    default => null,
                };
            } elseif (is_array($lead)) {
                $value = $lead[$field] ?? null;
            }

            $result[$field] = $value;
        }

        return $result;
    }

    /**
     * Преобразует сущность Lead в массив полной структуры.
     *
     * @param  Lead $lead
     * @return array<string, mixed>
     */
    public static function toFullArrayWithoutId(Lead $lead): array
    {
        return [
            'id' => $lead->id,
            'full_name' => $lead->fullName,
            'contact' => $lead->contact,
            'address' => $lead->address,
            'source' => $lead->source instanceof SourceDto ? SourceDtoMapper::toArray($lead->source) : null,
            'status' => $lead->status instanceof StatusDto ? StatusDtoMapper::toArray($lead->status) : null,
            'account_manager' => $lead->accountManager ? AccountManagerMapper::toArray($lead->accountManager) : null,
            'created_at' => $lead->createdAt?->format('Y-m-d H:i:s'),
            'groupName' => $lead->groupName,
        ];
    }

    /**
     * Приводит Lead или массив к плоской структуре со строками вместо вложенных объектов.
     *
     * @param  Lead|array<string, mixed> $lead
     * @return array<string, mixed>
     */
    public static function toFlatViewArray(Lead|array $lead): array
    {
        $result = [
            'id' => null,
            'contact' => null,
            'full_name' => null,
            'account_manager' => null,
            'address' => null,
            'source' => null,
            'status' => null,
            'created_at' => null,
            'groupName' => null,
        ];

        if ($lead instanceof Lead) {
            $result['id'] = $lead->id;
            $result['contact'] = $lead->contact;
            $result['full_name'] = $lead->fullName;
            $result['account_manager'] = $lead->accountManager?->login;
            $result['address'] = $lead->address;
            $result['source'] = $lead->source?->title;
            $result['status'] = $lead->status?->title;
            $result['created_at'] = $lead->createdAt?->format('Y-m-d H:i:s');
            $result['groupName'] = $lead->groupName;
            // balance здесь нет, добавь, если нужно
        } elseif (is_array($lead)) {
            $result['id'] = $lead['id'] ?? null;
            $result['contact'] = $lead['contact'] ?? $lead['contact_id'] ?? null;
            $result['full_name'] = $lead['full_name'] ?? null;
            $result['account_manager'] = $lead['account_manager']['login'] ?? $lead['account_manager'] ?? ($lead['account_manager_id'] ?? null);
            $result['address'] = $lead['address'] ?? null;
            $result['source'] = $lead['source']['title'] ?? $lead['source'] ?? ($lead['source_id'] ?? null);
            $result['status'] = $lead['status']['title'] ?? $lead['status'] ?? ($lead['status_id'] ?? null);
            $result['created_at'] = $lead['created_at'] ?? null;
            $result['groupName'] = $lead['group_name'] ?? $lead['group_name'] ?? null;
        }

        return $result;
    }
}
