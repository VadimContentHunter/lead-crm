<?php

namespace crm\src\Investments\Comment\_common\interfaces;

use crm\src\_common\interfaces\IResultRepository;
use crm\src\Investments\Comment\_common\DTOs\DbInvCommentDto;

/**
 * Интерфейс репозитория комментариев к инвестициям.
 *
 * @extends IResultRepository<DbInvCommentDto>
 */
interface ICommentRepository extends IResultRepository
{
    /**
     * Возвращает все комментарии, связанные с указанным leadUid.
     *
     * @param  string $leadUid
     * @return ICommentResult
     */
    public function getAllByLeadUid(string $leadUid): ICommentResult;

    /**
     * Удаляет все комментарии по leadUid.
     *
     * @param  string $leadUid
     * @return ICommentResult Содержит массив ID или ошибку
     */
    public function deleteAllByLeadUid(string $leadUid): ICommentResult;

    /**
     * Возвращает комментарии, оставленные конкретным пользователем.
     *
     * @param  string $whoId
     * @return ICommentResult
     */
    public function getAllByWhoId(string $whoId): ICommentResult;

    /**
     * Возвращает комментарии по leadUid с учётом типа (option).
     *
     * @param  string $leadUid
     * @param  int $option
     * @return ICommentResult
     */
    public function getByLeadUidAndOption(string $leadUid, int $option): ICommentResult;
}
