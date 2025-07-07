<?php

namespace crm\src\controllers;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\services\AppContext\AppContext;
use crm\src\services\AppContext\IAppContext;
use crm\src\_common\repositories\UserRepository;
use crm\src\_common\adapters\UserValidatorAdapter;
use crm\src\services\TemplateRenderer\HeaderManager;
use crm\src\components\UserManagement\UserManagement;
use crm\src\_common\repositories\AccessRoleRepository;
use crm\src\_common\repositories\AccessSpaceRepository;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\components\Security\_handlers\HandleAccessRole;
use crm\src\components\Security\_handlers\HandleAccessSpace;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;

class AccessDeniedPage
{
    private TemplateRenderer $renderer;
    public function __construct(
        private IAppContext $appContext
    ) {
        $this->renderer = $this->appContext->getTemplateRenderer();
        $this->showPage();
    }

    public function showPage(): void
    {
        $headers = new HeaderManager();
        $headers->set('Content-Type', 'text/html; charset=utf-8');
        $this->renderer->setHeaders($headers);

        try {
            $components = ['components' => $this->getComponents()];
            $headers->setResponseCode(403);
            echo $this->renderer->renderBundle($this->appContext?->getLayout($components));
        } catch (Throwable $e) {
            // Внутренняя ошибка — HTTP 500
            $headers->setResponseCode(500);
            // header('Content-Type: text/plain; charset=utf-8');
            // echo "Произошла ошибка: " . $e->getMessage();
            throw $e;
        }
    }

    /**
     * @return TemplateBundle[]
     */
    public function getComponents(): array
    {
        return [
            (new TemplateBundle(
                templatePath: 'components/accessDenied.tpl.php',
                variables: ['message' => urldecode($_GET['message'] ?? '')]
            ))
        ];
    }
}
