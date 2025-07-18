<?php

namespace crm\src\Investments\InvSource;

use crm\src\Investments\InvSource\_common\DTOs\DbInvSourceDto;
use crm\src\Investments\InvSource\_common\mappers\InvSourceMapper;
use crm\src\Investments\InvSource\_common\adapters\InvSourceResult;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceResult;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceRepository;

class DeleteSource
{
    public function __construct(private IInvSourceRepository $invSourceRepo)
    {
    }

    /**
     * @param array<string,mixed> $data
     */
    public function deleteSource(array $data): IInvSourceResult
    {
        $id = isset($data['id']) ? (int) $data['id']
                            : (isset($data['rowId']) ? (int) $data['rowId'] : null);

        $oldData = $this->invSourceRepo->getById($id ?? 0);
        $resultDelete = $this->invSourceRepo->deleteById($id ?? 0);
        if ($resultDelete->isSuccess()) {
            $data = $oldData->getData() instanceof DbInvSourceDto
                        ? InvSourceMapper::fromDbToEntity($oldData->getData())
                        : null;
            return InvSourceResult::success($data);
        }

        return InvSourceResult::failure($resultDelete->getError() ?? new \RuntimeException("Ошибка при удалении источника"));
    }
}
