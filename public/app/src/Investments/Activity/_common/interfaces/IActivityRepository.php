<?php

namespace crm\src\Investments\Activity\_common\interfaces;

use crm\src\_common\interfaces\IRepository;
use crm\src\Investments\Activity\_entities\InvActivity;
use crm\src\Investments\Activity\_common\interfaces\IActivityResult;

/**
 * Интерфейс репозитория для работы с инвестиционными сделками.
 *
 * @extends IRepository<InvActivity>
 */
interface IActivityRepository extends IRepository
{
    /**
     * Возвращает все сделки, связанные с указанным leadId.
     *
     * @param  int $leadId
     * @return IActivityResult
     */
    public function getAllByLeadId(int $leadId): IActivityResult;

    /**
     * Удаляет все сделки по leadId.
     *
     * @param  int $leadId
     * @return IActivityResult Содержит массив ID или ошибку
     */
    public function deleteAllByLeadId(int $leadId): IActivityResult;

    /**
     * Возвращает все активные сделки по leadId.
     *
     * @param  int $leadId
     * @return IActivityResult
     */
    public function getAllActiveByLeadId(int $leadId): IActivityResult;

    /**
     * Возвращает все закрытые сделки по leadId.
     *
     * @param  int $leadId
     * @return IActivityResult
     */
    public function getAllClosedByLeadId(int $leadId): IActivityResult;
}
