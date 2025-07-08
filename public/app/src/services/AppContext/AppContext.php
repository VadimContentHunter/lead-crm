<?php

namespace crm\src\services\AppContext;

use PDO;
use Monolog\Logger;
use ReflectionClass;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;
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
use crm\src\components\Security\_entities\AccessContext;
use crm\src\_common\repositories\AccessContextRepository;
use crm\src\components\Security\_handlers\HandleAccessRole;
use crm\src\components\Security\_handlers\HandleAccessSpace;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;
use crm\src\components\Security\_handlers\HandleAccessContext;

class AppContext
{
    public UserManagement $userManagement;

    public HandleAccessRole $handleAccessRole;

    public HandleAccessSpace $handleAccessSpace;

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
        $this->logger->info('AppContext initialized ' . $this->projectPath);
        $this->userManagement = new UserManagement(
            new UserRepository($pdo, $logger),
            new UserValidatorAdapter()
        );
        $this->accessContextRepository = new AccessContextRepository($pdo, $this->logger);
        $this->accessRoleRepository = new AccessRoleRepository($pdo, $this->logger);
        $this->accessSpaceRepository = new AccessSpaceRepository($pdo, $this->logger);

        $this->sessionAuthManager = new SessionAuthManager($this->accessContextRepository);
        $this->handleAccessContext = new HandleAccessContext($this->accessContextRepository);
        $this->handleAccessRole = new HandleAccessRole($this->accessRoleRepository);
        $this->handleAccessSpace = new HandleAccessSpace($this->accessSpaceRepository);

        $this->thisAccessContext = $this->sessionAuthManager->getCurrentAccessContext();
        if ($this->thisAccessContext !== null) {
            $this->thisUser = $this->userManagement->get()->executeById($this->thisAccessContext->userId)->getUser();
            $this->thisRole = $this->handleAccessRole->getRoleById($this->thisAccessContext->roleId ?? 0);
            $this->thisSpace = $this->handleAccessSpace->getSpaceById($this->thisAccessContext->spaceId ?? 0);
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
     * Создаёт экземпляр класса по именованным или позиционным аргументам.
     *
     * @param  class-string $className
     * @param  array<int|string,mixed> $params    Позиционные или именованные аргументы конструктора.
     * @return object
     *
     * @throws InvalidArgumentException
     */
    public static function createInstance(string $className, array $params = []): object
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException("Класс $className не найден.");
        }

        $reflection = new ReflectionClass($className);
        $constructor = $reflection->getConstructor();

        // Если конструктора нет, создаём экземпляр без аргументов
        if ($constructor === null) {
            return new $className();
        }

        // Если передан обычный список (0, 1, 2...), просто передаём как есть
        if (array_is_list($params)) {
            return $reflection->newInstanceArgs($params);
        }

        // Иначе разбираем по именам параметров
        $args = [];
        foreach ($constructor->getParameters() as $param) {
            $name = $param->getName();

            if (array_key_exists($name, $params)) {
                $args[] = $params[$name];
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } else {
                throw new InvalidArgumentException("Не передан обязательный параметр '$name' для класса $className");
            }
        }

        return $reflection->newInstanceArgs($args);
    }
}
