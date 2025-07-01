<?php

namespace crm\src\services\TemplateRenderer\_common\interfaces;

interface IHttpHeaders
{
    /**
     * @return mixed[]
     */
    public function getAll(): array;

    public function getResponseCode(): ?int;
}
