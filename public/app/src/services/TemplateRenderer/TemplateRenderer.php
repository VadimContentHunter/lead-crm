<?php

namespace crm\src\services\TemplateRenderer;

use crm\src\services\TemplateRenderer\_common\interfaces\ITemplateBundle;

/**
 * @todo Некачественный быстро созданный класс - переделать
 */
class TemplateRenderer
{
    private ?HeaderManager $headers = null;

    public function __construct(
        private string $baseTemplateDir
    ) {
    }

    public function setHeaders(HeaderManager $headers): void
    {
        $this->headers = $headers;
    }

    public function renderBundle(ITemplateBundle $bundle): string
    {
        $partialsRendered = [];

        foreach ($bundle->getPartials() as $partial) {
            $rendered = $this->renderBundle($partial);
            $container = $partial->getPartialsContainerName();

            if ($container) {
                $partialsRendered[$container] = $rendered;
            }
        }

        // Основные переменные + рендеренные partial'ы
        $mainVars = array_merge($bundle->getVariables(), $partialsRendered);

        $output = $this->render($bundle->getTemplatePath(), $mainVars);

        if ($this->headers !== null) {
            if ($code = $this->headers->getResponseCode()) {
                http_response_code($code);
            }

            foreach ($this->headers->getAll() as $name => $value) {
                header("$name: $value");
            }
        }

        return $output;
    }

    private function render(string $relativePath, array $variables): string
    {
        $fullPath = rtrim($this->baseTemplateDir, '/') . '/' . ltrim($relativePath, '/');

        if (!file_exists($fullPath)) {
            http_response_code(500);
            throw new \RuntimeException("Template '$relativePath' not found at '$fullPath'");
        }

        extract($variables);
        ob_start();
        include $fullPath;
        return ob_get_clean();
    }
}
