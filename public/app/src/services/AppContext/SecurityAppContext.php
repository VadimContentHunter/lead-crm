<?php

namespace crm\src\services\AppContext;

use PDO;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\components\Security\RoleNames;
use crm\src\services\AppContext\ISecurity;
use crm\src\services\AppContext\IAppContext;
use crm\src\components\Security\SecureWrapper;
use crm\src\_common\repositories\UserRepository;
use crm\src\_common\adapters\UserValidatorAdapter;
use crm\src\components\Security\BasedAccessGranter;
use crm\src\components\Security\SessionAuthManager;
use crm\src\components\Security\_entities\AccessRole;
use crm\src\components\UserManagement\_entities\User;
use crm\src\_common\repositories\AccessRoleRepository;
use crm\src\components\Security\_entities\AccessSpace;
use crm\src\_common\repositories\AccessSpaceRepository;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\_common\repositories\AccessContextRepository;
use crm\src\_common\adapters\Security\SecureUserManagement;
use crm\src\components\Security\_handlers\HandleAccessRole;
use crm\src\_common\adapters\Security\SecureHandleAccessRole;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;
use crm\src\_common\adapters\Security\SecureHandleAccessSpace;
use crm\src\components\Security\_handlers\HandleAccessContext;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
use crm\src\components\Security\_common\interfaces\IHandleAccessRole;
use crm\src\components\Security\_common\interfaces\IHandleAccessSpace;
use crm\src\components\UserManagement\_common\interfaces\IUserManagement;

class SecurityAppContext implements IAppContext, ISecurity
{
    public IUserManagement $userManagement;
    public IHandleAccessRole $handleAccessRole;
    public IHandleAccessSpace $handleAccessSpace;
    public SessionAuthManager $sessionAuthManager;
    public HandleAccessContext $handleAccessContext;

    public IAccessGranter $accessGranter;
    public AccessRoleRepository $accessRoleRepository;
    public AccessSpaceRepository $accessSpaceRepository;
    public AccessContextRepository $accessContextRepository;
    public UserRepository $userRepository;

    public TemplateRenderer $templateRenderer;
    // public JsonRpcServerFacade $jsonRpcServerFacade;

    public ?User $thisUser = null;
    public ?AccessRole $thisRole = null;
    public ?AccessSpace $thisSpace = null;
    public ?AccessContext $thisAccessContext = null;

    public function __construct(
        public string $projectPath,
        PDO $pdo,
        public LoggerInterface $logger = new NullLogger()
    ) {
        $this->templateRenderer = new TemplateRenderer(baseTemplateDir: $this->projectPath . '/src/templates/');

        $this->accessRoleRepository = new AccessRoleRepository($pdo, $logger);
        $this->accessSpaceRepository = new AccessSpaceRepository($pdo, $logger);
        $this->accessContextRepository = new AccessContextRepository($pdo, $logger);
        $this->userRepository = new UserRepository($pdo, $logger);

        $this->accessGranter = new BasedAccessGranter($this->accessRoleRepository, $this->accessSpaceRepository);
        $this->sessionAuthManager = new SessionAuthManager($this->accessContextRepository);
        $this->thisAccessContext = $this->sessionAuthManager->getCurrentAccessContext();

        $this->handleAccessContext = new HandleAccessContext($this->accessContextRepository);
        $this->handleAccessRole = new HandleAccessRole($this->accessRoleRepository);

        $this->handleAccessSpace = new SecureHandleAccessSpace(
            $this->accessSpaceRepository,
            $this->accessGranter,
            $this->thisAccessContext
        );

        $this->handleAccessRole = new SecureHandleAccessRole(
            $this->accessRoleRepository,
            $this->accessGranter,
            $this->thisAccessContext
        );

        $this->userManagement = new SecureUserManagement(
            $this->userRepository,
            new UserValidatorAdapter(),
            $this->accessGranter,
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


        if ($this->thisAccessContext !== null) {
            $userRepo = new UserRepository($pdo, $logger);
            $roleRepo = $this->accessRoleRepository;
            $spaceRepo = $this->accessSpaceRepository;

            $this->thisUser = $userRepo->getById($this->thisAccessContext->userId);
            $this->thisRole = $roleRepo->getById($this->thisAccessContext->roleId ?? 0);
            $this->thisSpace = $spaceRepo->getById($this->thisAccessContext->spaceId ?? 0);
        }
    }

    public function getUserManagement(): IUserManagement
    {
        return $this->userManagement;
    }

    public function getHandleAccessRole(): IHandleAccessRole
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

    public function getAccessGranter(): IAccessGranter
    {
        return $this->accessGranter;
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

    public function getTemplateRenderer(): TemplateRenderer
    {
        return $this->templateRenderer;
    }

    public function getJsonRpcServerFacade(): JsonRpcServerFacade
    {
        return new JsonRpcServerFacade();
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

    /**
     * @param array<string,mixed> $components
     */
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
                    'space' => $this->thisSpace?->name ?? '---',
                    'menuItems' => $this->getMenuItems()
                ]
            )))
            ->addPartial((new TemplateBundle(
                templatePath: 'partials/content.tpl.php',
                variables: $components,
                partialsContainer: 'content_container'
            )))
        );
    }

    /**
     * @return array<int,array{name:string,href:string,icon:string}>
     */
    public function getMenuItems(): array
    {
        $items = [
            [
                'name' => 'Главная',
                'href' => '/test',
                'icon' => 'fa-solid fa-house',
            ],
            [
                'name' => 'Добавить пользователя',
                'href' => '/page/user-add',
                'icon' => 'fa-solid fa-house',
            ],
            [
                'name' => 'Все пользователя',
                'href' => '/page/user-all',
                'icon' => 'fa-solid fa-house',
            ],
            [
                'name' => 'Добавить статус',
                'href' => '/page/status-add',
                'icon' => 'fa-solid fa-house',
            ],
            [
                'name' => 'Статусы',
                'href' => '/page/status-all',
                'icon' => 'fa-solid fa-house',
            ],
            [
                'name' => 'Добавить источник',
                'href' => '/page/source-add',
                'icon' => 'fa-solid fa-house',
            ],
            [
                'name' => 'Источники',
                'href' => '/page/source-all',
                'icon' => 'fa-solid fa-house',
            ],
            [
                'name' => 'Добавить лида',
                'href' => '/page/lead-add',
                'icon' => 'fa-solid fa-house',
            ],
            [
                'name' => 'Все лиды P2P',
                'href' => '/page/lead-all',
                'icon' => 'fa-solid fa-house',
            ],
            [
                'name' => 'Все лиды (Инвестка)',
                'href' => '/page/lead-all',
                'icon' => 'fa-solid fa-house',
            ],
        ];

        // if (RoleNames::isManager($this->thisRole->name)) {
        //     $items = array_filter($items, fn($item) => $item['name'] !== 'Добавить пользователя');
        //     // Если важен порядок ключей:
        //     $items = array_values($items);
        // }

        return $items;
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

    /**
     * Оборачивает переданный объект в SecureWrapper с текущим контекстом доступа.
     *
     * @template T of object
     * @param    T $target Объект, который нужно обернуть
     * @return   SecureWrapper
     */
    public function wrapWithSecurity(object $target): SecureWrapper
    {
        return new SecureWrapper(
            $target,
            $this->getAccessGranter(),
            $this->getThisAccessContext()
        );
    }
}
