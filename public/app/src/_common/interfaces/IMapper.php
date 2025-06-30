<?php

namespace crm\src\_common\interfaces;

interface IMapper
{
    /**
     * Преобразует объект $source в другой объект.
     *
     * @param  object $source
     * @return object
     */
    public function map(object $source): object;
}
