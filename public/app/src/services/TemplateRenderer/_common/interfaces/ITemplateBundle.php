<?php

namespace crm\src\services\TemplateRenderer\_common\interfaces;

interface ITemplateBundle
{
    public function getTemplatePath(): string;
    public function getVariables(): array;
    public function getPartialsContainerName(): ?string;
    public function getPartials(): array;
    public function addPartial(ITemplateBundle $partial): void;
}
