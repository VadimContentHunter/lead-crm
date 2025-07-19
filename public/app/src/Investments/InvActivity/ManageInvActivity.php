<?php

namespace crm\src\Investments\InvActivity;

use DateTimeImmutable;
use crm\src\Investments\InvActivity\CloseInvActivity;
use crm\src\Investments\InvActivity\CreateInvActivity;
use crm\src\Investments\InvActivity\DeleteInvActivity;
use crm\src\Investments\InvActivity\UpdateInvActivity;
use crm\src\Investments\InvActivity\_entities\InvActivity;
use crm\src\Investments\InvActivity\_common\DTOs\InvActivityInputDto;
use crm\src\Investments\InvActivity\_common\mappers\InvActivityMapper;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadRepository;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityResult;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityRepository;

final class ManageInvActivity
{
    public function __construct(
        private CreateInvActivity $create,
        private UpdateInvActivity $update,
        private DeleteInvActivity $delete,
        private CloseInvActivity $close,
        private IInvActivityRepository $repository,
        private IInvLeadRepository $invLeadRepo
    ) {
    }

    /**
     * @param array<string,mixed> $data
     */
    public function create(array $data): IInvActivityResult
    {
        return $this->create->handle(InvActivityMapper::fromArrayToInput($data));
    }

    public function updateById(InvActivityInputDto $input): IInvActivityResult
    {
        return $this->update->handle($input);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function deleteById(array $data): IInvActivityResult
    {
        return $this->delete->handle(InvActivityMapper::fromArrayToInput($data)->id ?? 0);
    }

    public function closeDeal(InvActivity $activity, ?DateTimeImmutable $closeTime = null, ?float $closePrice = null): IInvActivityResult
    {
        return $this->close->handle($activity, $closeTime, $closePrice);
    }

    public function getActivityTable(): IInvActivityResult
    {
        return (new InvActivityTableRenderer($this->repository))->getBaseTable();
    }

    /**
     * @param array<string,mixed> $params
     */
    public function getActivityData(array $params): IInvActivityResult
    {
        return (new RenderInvActivityForm($this->invLeadRepo))->getFormCreateData($params);
    }
}
