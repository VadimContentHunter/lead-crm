<?php

namespace crm\src\Investments\InvActivity;

use crm\src\Investments\InvActivity\_exceptions\InvActivityException;
use crm\src\Investments\InvActivity\_common\adapters\InvActivityResult;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityRepository;

final class DeleteInvActivity
{
    public function __construct(private IInvActivityRepository $repository)
    {
    }

    public function handle(int $id): InvActivityResult
    {
        try {
            if ($id <= 0) {
                return InvActivityResult::failure(new InvActivityException("Некорректный ID для удаления"));
            }

            $result = $this->repository->deleteById($id);

            if (!$result->isSuccess()) {
                return InvActivityResult::failure(
                    $result->getError() ?? new InvActivityException("Ошибка при удалении сделки")
                );
            }

            return InvActivityResult::success($id);
        } catch (\Throwable $e) {
            return InvActivityResult::failure($e);
        }
    }
}
