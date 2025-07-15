<?php

namespace crm\src\Investments\InvActivity\_common\interfaces;

use crm\src\_common\interfaces\IResultRepository;
use crm\src\Investments\InvActivity\_common\DTOs\DbInvActivityDto;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityResult;

/**
 * Интерфейс репозитория инвестиционных сделок.
 *
 * @extends IResultRepository<DbInvActivityDto>
 */
interface IInvActivityRepository extends IResultRepository
{
    /**
     * Возвращает все сделки, связанные с указанным leadUid.
     *
     * @param  string $leadUid Уникальный
     *                         идентификатор, например
     *                         "928123456"
     * @return IInvActivityResult
     */
    public function getAllByLeadUid(string $leadUid): IInvActivityResult;

    /**
     * Удаляет все сделки по leadUid.
     *
     * @param  string $leadUid
     * @return IInvActivityResult Содержит массив ID или ошибку
     */
    public function deleteAllByLeadUid(string $leadUid): IInvActivityResult;

    /**
     * Возвращает все активные сделки по leadUid.
     *
     * @param  string $leadUid
     * @return IInvActivityResult
     */
    public function getAllActiveByLeadUid(string $leadUid): IInvActivityResult;

    /**
     * Возвращает все закрытые сделки по leadUid.
     *
     * @param  string $leadUid
     * @return IInvActivityResult
     */
    public function getAllClosedByLeadUid(string $leadUid): IInvActivityResult;
}
