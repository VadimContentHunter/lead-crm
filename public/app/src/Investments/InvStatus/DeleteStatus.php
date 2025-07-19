<?php

namespace crm\src\Investments\InvStatus;

use crm\src\Investments\InvStatus\_common\DTOs\DbInvStatusDto;
use crm\src\Investments\InvStatus\_common\mappers\InvStatusMapper;
use crm\src\Investments\InvStatus\_common\adapters\InvStatusResult;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusResult;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusRepository;

class DeleteStatus
{
    public function __construct(private IInvStatusRepository $invStatusRepo)
    {
    }

    /**
     * @param array<string,mixed> $data
     */
    public function deleteStatus(array $data): IInvStatusResult
    {
        $id = InvStatusMapper::fromArrayToInput($data)->id;
        $oldData = $this->invStatusRepo->getById($id ?? 0);
        $resultDelete = $this->invStatusRepo->deleteById($id ?? 0);
        if ($resultDelete->isSuccess()) {
            $data = $oldData->getData() instanceof DbInvStatusDto
                        ? InvStatusMapper::fromDbToEntity($oldData->getData())
                        : null;
            return InvStatusResult::success($data);
        }

        return InvStatusResult::failure($resultDelete->getError() ?? new \RuntimeException("Ошибка при удалении источника"));
    }
}
