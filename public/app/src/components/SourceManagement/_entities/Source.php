<?php

namespace crm\src\components\SourceManagement\_entities;

class Source
{
    public function __construct(
        public string $title,
        public ?int $id = null,
    ) {
    }
}
