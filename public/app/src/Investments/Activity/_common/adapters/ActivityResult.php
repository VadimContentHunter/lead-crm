<?php

namespace crm\src\Investments\Activity\_common\adapters;

use Throwable;
use crm\src\Investments\Activity\_entities\InvActivity;
use crm\src\Investments\Activity\_common\InvActivityCollection;
use crm\src\_common\interfaces\AResult;
use crm\src\Investments\Activity\_common\interfaces\IActivityResult;

class ActivityResult extends AResult implements IActivityResult
{
    /**
     * Переопределённый фабричный метод успеха.
     *
     * @param  InvActivity[]|InvActivityCollection|InvActivity|int[]|null $data
     * @return static
     */
    public static function success(mixed $data = null): static
    {
        if (is_array($data) && isset($data[0]) && $data[0] instanceof InvActivity) {
            $data = new InvActivityCollection($data);
        }

        return new static($data, null);
    }

    public static function failure(Throwable $error): static
    {
        return new static(null, $error);
    }

    public function getActivity(): ?InvActivity
    {
        return $this->data instanceof InvActivity ? $this->data : null;
    }

    public function getHash(): ?string
    {
        return $this->getActivity()?->activityHash;
    }

    public function getLeadUid(): ?string
    {
        return $this->getActivity()?->leadUid;
    }

    public function getAmount(): ?float
    {
        return $this->getActivity()?->amount;
    }

    public function getResult(): ?float
    {
        return $this->getActivity()?->result;
    }

    public function getId(): ?int
    {
        return $this->getActivity()?->id;
    }

    public function getCollection(): InvActivityCollection
    {
        return $this->data instanceof InvActivityCollection
            ? $this->data
            : new InvActivityCollection([]);
    }
}
