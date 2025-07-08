<?php

namespace crm\src\components\StatusManagement\_common\interfaces;

use crm\src\components\StatusManagement\CreateStatus;
use crm\src\components\StatusManagement\GetStatus;
use crm\src\components\StatusManagement\UpdateStatus;
use crm\src\components\StatusManagement\DeleteStatus;

/**
 * Интерфейс для StatusManagement.
 */
interface IStatusManagement
{
    /**
     * Получение сервиса создания статусов.
     */
    public function create(): CreateStatus;

    /**
     * Получение сервиса получения статусов.
     */
    public function get(): GetStatus;

    /**
     * Получение сервиса обновления статусов.
     */
    public function update(): UpdateStatus;

    /**
     * Получение сервиса удаления статусов.
     */
    public function delete(): DeleteStatus;
}
