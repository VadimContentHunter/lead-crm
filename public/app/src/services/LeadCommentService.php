<?php

namespace crm\src\services;

use crm\src\services\AppContext\IAppContext;
use crm\src\components\LeadManagement\_entities\Lead;
use crm\src\components\UserManagement\_entities\User;
use crm\src\components\CommentManagement\_entities\Comment;
use crm\src\components\CommentManagement\CommentManagement;

class LeadCommentService
{
    public function __construct(
        private CommentManagement $commentManagement,
        private ?User $thisUser
    ) {
    }

    public function logCreate(Lead $newLead): void
    {
        $message = "Создан новый лид:\n"
            . "Имя: {$newLead->fullName}\n"
            . "Контакт: {$newLead->contact}\n"
            . "Адрес: {$newLead->address}\n"
            . "Источник: " . ($newLead->source?->title ?? 'не указан') . "\n"
            . "Статус: " . ($newLead->status?->title ?? 'не указан') . "\n"
            . "Менеджер: " . ($newLead->accountManager?->login ?? 'не указан') . "\n"
            . "Группа: " . ($newLead->groupName ?? 'не указана');

        $this->createComment($newLead->id, $message);
    }

    public function logUpdate(?Lead $oldLead, Lead $newLead): void
    {
        if ($oldLead === null) {
            $this->createComment(0, "Создан новый лид:\n" . "ID: " . $newLead->id . "\n" . "Имя: {$newLead->fullName}\n");
            return;
        }

        $changes = [];

        if ($oldLead->fullName !== $newLead->fullName) {
            $changes[] = "Имя: '{$oldLead->fullName}' → '{$newLead->fullName}'";
        }

        if ($oldLead->contact !== $newLead->contact) {
            $changes[] = "Контакт: '{$oldLead->contact}' → '{$newLead->contact}'";
        }

        if ($oldLead->address !== $newLead->address) {
            $changes[] = "Адрес: '{$oldLead->address}' → '{$newLead->address}'";
        }

        if ($oldLead->source?->id !== $newLead->source?->id) {
            $changes[] = "Источник: '" . ($oldLead->source?->title ?? 'не указан') . "' → '" . ($newLead->source?->title ?? 'не указан') . "'";
        }

        if ($oldLead->status?->id !== $newLead->status?->id) {
            $changes[] = "Статус: '" . ($oldLead->status?->title ?? 'не указан') . "' → '" . ($newLead->status?->title ?? 'не указан') . "'";
        }

        if ($oldLead->accountManager?->id !== $newLead->accountManager?->id) {
            $changes[] = "Менеджер: '" . ($oldLead->accountManager?->login ?? 'не указан') . "' → '" . ($newLead->accountManager?->login ?? 'не указан') . "'";
        }

        if ($oldLead->groupName !== $newLead->groupName) {
            $changes[] = "Группа: '" . ($oldLead->groupName ?? 'не указана') . "' → '" . ($newLead->groupName ?? 'не указана') . "'";
        }

        $message = empty($changes) ? "Изменения в лиде не зафиксированы." : "Изменения в лиде:\n" . implode("\n", $changes);

        $this->createComment($newLead->id, $message);
    }

    public function logDelete(Lead $deletedLead): void
    {
        $message = "Удалён лид:\n"
            . "Имя: {$deletedLead->fullName}\n"
            . "Контакт: {$deletedLead->contact}\n"
            . "Адрес: {$deletedLead->address}\n"
            . "Источник: " . ($deletedLead->source?->title ?? 'не указан') . "\n"
            . "Статус: " . ($deletedLead->status?->title ?? 'не указан') . "\n"
            . "Менеджер: " . ($deletedLead->accountManager?->login ?? 'не указан') . "\n"
            . "Группа: " . ($deletedLead->groupName ?? 'не указана');

        $this->createComment($deletedLead->id, $message);
    }

    private function createComment(int $leadId, string $message): void
    {
        $comment = new Comment(
            comment: $message,
            leadId: $leadId,
            userId: $this->thisUser?->id
        );

        $this->commentManagement->create()->execute(
            $comment,
            $this->thisUser?->login ?? '---'
        );
    }
}
