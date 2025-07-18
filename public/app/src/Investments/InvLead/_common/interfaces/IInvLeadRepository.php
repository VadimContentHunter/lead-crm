<?php

namespace crm\src\Investments\InvLead\_common\interfaces;

use crm\src\_common\interfaces\IResultRepository;
use crm\src\Investments\InvLead\_common\DTOs\DbInvLeadDto;

/**
 * Интерфейс репозитория инвестиционных лидов.
 *
 * @extends IResultRepository<DbInvLeadDto>
 */
interface IInvLeadRepository extends IResultRepository
{
    /**
     * Возвращает лида по его уникальному UID.
     *
     * @param  string $uid Например: "928000001"
     * @return IInvLeadResult
     */
    public function getByUid(string $uid): IInvLeadResult;

    /**
     * Удаляет лида по UID.
     *
     * @param  string $uid
     * @return IInvLeadResult
     */
    public function deleteByUid(string $uid): IInvLeadResult;

    /**
     * Возвращает всех лидов, закреплённых за аккаунт-менеджером.
     *
     * @param  int $managerId ID менеджера
     * @return IInvLeadResult
     */
    public function getAllByAccountManagerId(int $managerId): IInvLeadResult;

    /**
     * Получает все сущности.
     *
     * @return IInvLeadResult
     */
    public function getAll(): IInvLeadResult;
}
