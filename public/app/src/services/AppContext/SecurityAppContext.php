<?php

namespace crm\src\services\AppContext;

use PDO;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\services\AppContext\IAppContext;
use crm\src\components\Security\SecureWrapper;
use crm\src\_common\repositories\UserRepository;
use crm\src\_common\adapters\UserValidatorAdapter;
use crm\src\components\Security\BasedAccessGranter;
use crm\src\components\Security\SessionAuthManager;
use crm\src\components\Security\_entities\AccessRole;
use crm\src\components\Security\SecureWrapperFactory;
use crm\src\components\UserManagement\_entities\User;
use crm\src\components\UserManagement\UserManagement;
use crm\src\_common\repositories\AccessRoleRepository;
use crm\src\components\Security\_entities\AccessSpace;
use crm\src\_common\repositories\AccessSpaceRepository;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\_common\repositories\AccessContextRepository;
use crm\src\components\Security\_handlers\HandleAccessRole;
use crm\src\components\Security\_handlers\HandleAccessSpace;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;
use crm\src\_common\adapters\Security\SecureHandleAccessSpace;
use crm\src\components\Security\_handlers\HandleAccessContext;
use crm\src\components\Security\_common\interfaces\IHandleAccessSpace;

class SecurityAppContext implements IAppContext
{
    public UserManagement $userManagement;
    public HandleAccessRole $handleAccessRole;
    public IHandleAccessSpace $handleAccessSpace;
    public SessionAuthManager $sessionAuthManager;
    public HandleAccessContext $handleAccessContext;
    public AccessRoleRepository $accessRoleRepository;
    public AccessSpaceRepository $accessSpaceRepository;
    public AccessContextRepository $accessContextRepository;

    public ?User $thisUser = null;
    public ?AccessRole $thisRole = null;
    public ?AccessSpace $thisSpace = null;
    public ?AccessContext $thisAccessContext = null;

    public function __construct(
        public string $projectPath,
        PDO $pdo,
        public LoggerInterface $logger = new NullLogger()
    ) {
        $this->accessRoleRepository = new AccessRoleRepository($pdo, $logger);
        $this->accessSpaceRepository = new AccessSpaceRepository($pdo, $logger);
        $this->accessContextRepository = new AccessContextRepository($pdo, $logger);

        $this->sessionAuthManager = new SessionAuthManager($this->accessContextRepository);
        $this->userManagement = new UserManagement(new UserRepository($pdo, $logger), new UserValidatorAdapter());
        $this->thisAccessContext = $this->sessionAuthManager->getCurrentAccessContext();

        $this->handleAccessContext = new HandleAccessContext($this->accessContextRepository);
        $this->handleAccessRole = new HandleAccessRole($this->accessRoleRepository);

        $this->handleAccessSpace = new SecureHandleAccessSpace(
            $this->accessSpaceRepository,
            new BasedAccessGranter($this->accessRoleRepository, $this->accessSpaceRepository),
            $this->thisAccessContext
        );

        // $this->handleAccessSpace = new SecureHandleAccessSpace(
        //     $this->accessSpaceRepository,
        //     new BasedAccessGranter($this->accessRoleRepository, $this->accessSpaceRepository),
        //     $this->thisAccessContext
        // );

        // $this->handleAccessSpace = SecureWrapper::createWrapped(
        //     HandleAccessSpace::class,
        //     [$this->accessSpaceRepository],
        //     new BasedAccessGranter($this->accessRoleRepository, $this->accessSpaceRepository),
        //     $this->thisAccessContext
        // );

        // $this->handleAccessSpace = SecureWrapperFactory::createAndWrapObject(HandleAccessSpace::class, [
        //     new AccessSpaceRepository($pdo, $this->logger)
        // ]);


        // if ($this->thisAccessContext !== null) {
        //     $this->thisUser = $this->userManagement->get()->executeById($this->thisAccessContext->userId)->getUser();
        //     $this->thisRole = $this->handleAccessRole->getRoleById($this->thisAccessContext->roleId ?? 0);
        //     $this->thisSpace = $this->handleAccessSpace->getSpaceById($this->thisAccessContext->spaceId ?? 0);
        // }
    }

    public function getUserManagement(): UserManagement
    {
        return $this->userManagement;
    }

    public function getHandleAccessRole(): HandleAccessRole
    {
        return $this->handleAccessRole;
    }

    public function getHandleAccessSpace(): IHandleAccessSpace
    {
        return $this->handleAccessSpace;
    }

    public function getSessionAuthManager(): SessionAuthManager
    {
        return $this->sessionAuthManager;
    }

    public function getHandleAccessContext(): HandleAccessContext
    {
        return $this->handleAccessContext;
    }

    public function getAccessRoleRepository(): AccessRoleRepository
    {
        return $this->accessRoleRepository;
    }

    public function getAccessSpaceRepository(): AccessSpaceRepository
    {
        return $this->accessSpaceRepository;
    }

    public function getAccessContextRepository(): AccessContextRepository
    {
        return $this->accessContextRepository;
    }

    public function getThisUser(): ?User
    {
        return $this->thisUser;
    }

    public function getThisRole(): ?AccessRole
    {
        return $this->thisRole;
    }

    public function getThisSpace(): ?AccessSpace
    {
        return $this->thisSpace;
    }

    public function getThisAccessContext(): ?AccessContext
    {
        return $this->thisAccessContext;
    }

    public function getLayout(array $components = []): TemplateBundle
    {
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

    public function checkSessionAndRedirect(): void
    {
        if ($this->sessionAuthManager->checkAccess()) {
            header('Location: /page/lead-all');
            exit;
        }
    }

    public function redirectIfNotAuthenticated(): void
    {
        if (!$this->sessionAuthManager->checkAccess()) {
            header('Location: /login');
            exit;
        }
    }

    public function logoutAndRedirect(): never
    {
        $this->sessionAuthManager->logout();
        header('Location: /login');
        exit;
    }
}
