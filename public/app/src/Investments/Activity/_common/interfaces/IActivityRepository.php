<?php

namespace crm\src\Investments\Activity\_common\interfaces;

use crm\src\_common\interfaces\IResultRepository;
use crm\src\Investments\Activity\_common\DTOs\DbActivityDto;
use crm\src\Investments\Activity\_common\interfaces\IActivityResult;

/**
 * Интерфейс репозитория инвестиционных сделок.
 *
 * @extends IResultRepository<DbActivityDto>
 */
interface IActivityRepository extends IResultRepository
{
    /**
     * Возвращает все сделки, связанные с указанным leadUid.
     *
     * @param  string $leadUid Уникальный
     *                         идентификатор, например
     *                         "928123456"
     * @return IActivityResult
     */
    public function getAllByLeadUid(string $leadUid): IActivityResult;

    /**
     * Удаляет все сделки по leadUid.
     *
     * @param  string $leadUid
     * @return IActivityResult Содержит массив ID или ошибку
     */
    public function deleteAllByLeadUid(string $leadUid): IActivityResult;

    /**
     * Возвращает все активные сделки по leadUid.
     *
     * @param  string $leadUid
     * @return IActivityResult
     */
    public function getAllActiveByLeadUid(string $leadUid): IActivityResult;

    /**
     * Возвращает все закрытые сделки по leadUid.
     *
     * @param  string $leadUid
     * @return IActivityResult
     */
    public function getAllClosedByLeadUid(string $leadUid): IActivityResult;
}
