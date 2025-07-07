<?php

namespace crm\src\services\TemplateRenderer\_common;

use crm\src\services\TemplateRenderer\_common\interfaces\ITemplateBundle;

class TemplateBundle implements ITemplateBundle
{
    /**
     * @var ITemplateBundle[]
     */
    private array $appContext = [];

    public function __construct(
        private string $templatePath,
        /**
         * @var mixed[]
         */
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

    public function getpartialsContainerName(): ?string
    {
        return $this->partialsContainer;
    }

    /**
     * @return ITemplateBundle[]
     */
    public function getAppContext(): array
    {
        return $this->appContext;
    }

    public function addPartial(ITemplateBundle $partial): self
    {
        $this->appContext[] = $partial;

        return $this;
    }
}
