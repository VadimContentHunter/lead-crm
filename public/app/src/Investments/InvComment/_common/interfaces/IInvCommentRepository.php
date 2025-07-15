<?php

namespace crm\src\Investments\InvComment\_common\interfaces;

use crm\src\_common\interfaces\IResultRepository;
use crm\src\Investments\InvComment\_common\DTOs\DbInvCommentDto;

/**
 * Интерфейс репозитория комментариев к инвестициям.
 *
 * @extends IResultRepository<DbInvCommentDto>
 */
interface IInvCommentRepository extends IResultRepository
{
    /**
     * Возвращает все комментарии, связанные с указанным leadUid.
     *
     * @param  string $leadUid
     * @return IInvCommentResult
     */
    public function getAllByLeadUid(string $leadUid): IInvCommentResult;

    /**
     * Удаляет все комментарии по leadUid.
     *
     * @param  string $leadUid
     * @return IInvCommentResult Содержит массив ID или ошибку
     */
    public function deleteAllByLeadUid(string $leadUid): IInvCommentResult;

    /**
     * Возвращает комментарии, оставленные конкретным пользователем.
     *
     * @param  string $whoId
     * @return IInvCommentResult
     */
    public function getAllByWhoId(string $whoId): IInvCommentResult;

    /**
     * Возвращает комментарии по leadUid с учётом типа (option).
     *
     * @param  string $leadUid
     * @param  int $option
     * @return IInvCommentResult
     */
    public function getByLeadUidAndOption(string $leadUid, int $option): IInvCommentResult;
}
