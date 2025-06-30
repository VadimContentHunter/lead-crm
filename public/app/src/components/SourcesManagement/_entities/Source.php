<?php

namespace crm\src\components\SourcesManagement\entities;

class Source
{
    public function __construct(
        public string $title,
        public ?int $id = null,
    ) {
    }
}
