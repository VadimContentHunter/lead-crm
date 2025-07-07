<?php

namespace crm\src\services\TemplateRenderer\_common\interfaces;

interface ITemplateBundle
{
    public function getTemplatePath(): string;

    /**
     * @return mixed[]
     */
    public function getVariables(): array;
    public function getpartialsContainerName(): ?string;

    /**
     * @return ITemplateBundle[]
     */
    public function getAppContext(): array;
    public function addPartial(ITemplateBundle $partial): self;
}
