<?php

namespace crm\src\services\TemplateRenderer\_common;

use crm\src\services\TemplateRenderer\_common\interfaces\ITemplateBundle;

class TemplateBundle implements ITemplateBundle
{
    private array $partials = [];

    public function __construct(
        private string $templatePath,
        private array $variables = [],
        private ?string $partialsContainer = null
    ) {
    }

    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function getPartialsContainerName(): ?string
    {
        return $this->partialsContainer;
    }

    public function getPartials(): array
    {
        return $this->partials;
    }

    public function addPartial(ITemplateBundle $partial): void
    {
        $this->partials[] = $partial;
    }
}
