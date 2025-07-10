<?php

namespace crm\src\services;

use crm\src\services\AppContext\IAppContext;
use crm\src\components\LeadManagement\_entities\Lead;
use crm\src\components\UserManagement\_entities\User;
use crm\src\components\BalanceManagement\_entities\Balance;
use crm\src\components\CommentManagement\_entities\Comment;
use crm\src\components\CommentManagement\CommentManagement;
use crm\src\components\DepositManagement\_entities\Deposit;

class LeadCommentService
{
    public function __construct(
        private CommentManagement $commentManagement,
        private ?User $thisUser
    ) {
    }

    /**
     * @param array<string,string> $rename
     */
    public function compareObjects(
        object $obj1,
        object $obj2,
        int $leadId,
        string $messageStart,
        array $rename = []
    ): void {
        $data = [];

        // Получаем все публичные свойства первого объекта
        $properties = array_keys(get_object_vars($obj1));
        foreach ($properties as $property) {
            // Проверяем, есть ли такое свойство во втором объекте
            if (!property_exists($obj2, $property)) {
                continue;
            }

            if (is_object($obj1->$property) || is_object($obj2->$property)) {
                continue;
            }

            if ($property === "id" || $property === "id") {
                continue;
            }

            $value1 = $obj1->$property;
            $value2 = $obj2->$property;

            // Если значения разные
            if ($value1 != $value2) {
                // Берём новое название из $rename или оставляем имя свойства
                $fieldName = isset($rename[$property]) ? $rename[$property] : $property;
                $data[] = "'{$fieldName}': '{$value1}' => '{$value2}'";
            }
        }

        // $objId = '[' . ($obj1?->id ?? '---') . ' - объект] ';
        $message = $messageStart . implode(', ', $data);
        if (empty($data)) {
            $message = $messageStart . ' Данные не изменились!';
        }

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

    public function sendComment(int $leadId, string $message): void
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
