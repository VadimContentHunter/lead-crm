<?php

namespace crm\src\services\TemplateRenderer\_common\interfaces;

interface IHttpHeaders
{
    public function getAll(): array;

    public function getResponseCode(): ?int;
}
