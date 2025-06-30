<?php

namespace crm\src\components\SourcesManagement\_entities;

class Source
{
    public function __construct(
        public string $title,
        public ?int $id = null,
    ) {
    }
}
