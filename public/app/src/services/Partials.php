<?php

namespace crm\src\services;

use PDO;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\Assert;
use crm\src\_common\repositories\UserRepository;
use crm\src\_common\adapters\UserValidatorAdapter;
use crm\src\components\Security\SessionAuthManager;
use crm\src\components\Security\_entities\AccessRole;
use crm\src\components\UserManagement\_entities\User;
use crm\src\components\UserManagement\UserManagement;
use crm\src\_common\repositories\AccessRoleRepository;
use crm\src\components\Security\_entities\AccessSpace;
use crm\src\_common\repositories\AccessSpaceRepository;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\_common\repositories\AccessContextRepository;
use crm\src\components\Security\_handlers\HandleAccessRole;
use crm\src\components\Security\_handlers\HandleAccessSpace;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;
use crm\src\components\Security\_handlers\HandleAccessContext;

class Partials
{
    public UserManagement $userManagement;

    public HandleAccessRole $handleAccessRole;

    public HandleAccessSpace $handleAccessSpace;

    public SessionAuthManager $sessionAuthManager;

    public HandleAccessContext $handleAccessContext;

    public ?User $thisUser = null;

    public ?AccessRole $thisRole = null;

    public ?AccessSpace $thisSpace = null;

    public function __construct(
        private string $projectPath,
        PDO $pdo,
        private LoggerInterface $logger = new NullLogger()
    ) {
        $this->logger->info('Partials initialized ' . $this->projectPath);
        $this->userManagement = new UserManagement(
            new UserRepository($pdo, $logger),
            new UserValidatorAdapter()
        );
        $accessContextRepository = new AccessContextRepository($pdo, $this->logger);
        $this->sessionAuthManager = new SessionAuthManager($accessContextRepository);
        $this->handleAccessContext = new HandleAccessContext($accessContextRepository);
        $this->handleAccessRole = new HandleAccessRole(new AccessRoleRepository($pdo, $this->logger));
        $this->handleAccessSpace = new HandleAccessSpace(new AccessSpaceRepository($pdo, $this->logger));

        $accessContext = $this->sessionAuthManager->getCurrentAccessContext();
        if ($accessContext !== null) {
            $this->thisUser = $this->userManagement->get()->executeById($accessContext->userId)->getUser();
            $this->thisRole = $this->handleAccessRole->getRoleById($accessContext->roleId ?? 0);
            $this->thisSpace = $this->handleAccessSpace->getSpaceById($accessContext->spaceId ?? 0);
        }
    }

    /**
     * @param array<string, mixed> $components
     */
    public function getLayout(
        array $components = [],
    ): TemplateBundle {
        // $renderer = new TemplateRenderer(baseTemplateDir: $this->projectPath . '/src/templates/');
        // $layout = (new TemplateBundle(templatePath: 'components/addUser.tpl.php'));

        return (new TemplateBundle(
            templatePath: 'layout.tpl.php',
            variables: [
                'body_js' => [
                    '/assets/js/app.js'
                ]
            ],
            partialsContainer: 'content'
        ))
        ->addPartial((new TemplateBundle(
            templatePath: 'partials/head.tpl.php',
            variables: [
                'title' => 'Тестовая страница',
                'css' => [
                    '/assets/css/reset.css',
                    '/assets/css/fonts.css',
                    '/assets/css/styles.css',
                    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css'
                ],
                'js' => [
                    // '/assets/js/JsonRpcResponseHandler.js',
                    // '/assets/js/JsonRpcClient.js'
                ]
            ],
            partialsContainer: 'head'
        )))
        ->addPartial(
            (new TemplateBundle(
                templatePath: 'containers/page-container.tpl.php',
                partialsContainer: 'main_container'
            ))->addPartial((new TemplateBundle(
                templatePath: 'partials/main-menu.tpl.php',
                partialsContainer: 'main_menu',
                variables: [
                    'login' => $this->thisUser?->login ?? '---',
                    'role' => $this->thisRole?->name ?? '---',
                    'space' => $this->thisSpace?->name ?? '---'
                ]
            )))
            ->addPartial((new TemplateBundle(
                templatePath: 'partials/content.tpl.php',
                variables: $components,
                partialsContainer: 'content_container'
            )))
        );
    }
}
