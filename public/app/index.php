<?php

declare(strict_types=1);

use Psr\Log\NullLogger;
use crm\src\controllers\LeadPage;
use crm\src\controllers\UserPage;
use crm\src\controllers\SourcePage;
use crm\src\controllers\StatusPage;
use crm\src\services\LoggerFactory;
use crm\src\controllers\ApiController;
use crm\src\controllers\HomeController;
use crm\src\controllers\TestController;
use crm\src\controllers\ErrorController;
use crm\src\controllers\LoginController;
use PHPUnit\TextUI\Configuration\Source;
use crm\src\controllers\API\LeadController;
use crm\src\controllers\API\UserController;
use crm\src\controllers\NotFoundController;
use crm\src\controllers\BootstrapController;
use crm\src\controllers\API\SourceController;
use crm\src\controllers\API\StatusController;
use crm\src\components\Security\SecureWrapper;
use crm\src\controllers\API\BalanceController;
use crm\src\controllers\API\CommentController;
use crm\src\controllers\API\DepositController;
use  crm\src\services\RouteHandler\RouteHandler;
use  crm\src\services\RouteHandler\entities\Route;
use crm\src\components\Security\BasedAccessGranter;
use crm\src\components\Security\SecureWrapperFactory;
use crm\src\components\DepositManagement\_entities\Deposit;
use crm\src\components\Security\_repositories\AccessRoleRepository;
use crm\src\services\Repositories\DbRepository\services\PdoFactory;
use crm\src\components\Security\_repositories\AccessSpaceRepository;

// define('PROJECT_ROOT', __DIR__);

require_once __DIR__ . '/libs/autoload.php';

// $logger = LoggerFactory::createLogger(baseLogDir:__DIR__ . '/logs');
$logger = new NullLogger();
$pdo = PdoFactory::create([
    'host' => 'host.docker.internal',
    'db'   => 'crm_db',
    'user' => 'root',
    'pass' => 'root',
]);


SecureWrapperFactory::init(new BasedAccessGranter(
    roleRepository: new AccessRoleRepository($pdo, $this->logger),
    spaceRepository: new AccessSpaceRepository($pdo, $this->logger),
));
// $secureWrapper = new SecureWrapper();

// Создаём маршруты:
$routes = [
    new Route(
        pattern: '^/bootstrap-key-A7F9X2M3Q8L1$',
        className: BootstrapController::class,
        extraData: [$pdo, $logger]
    ),

    // API

    new Route(
        pattern: '^/api/users$',
        className: UserController::class,
        extraData: [__DIR__, $pdo, $logger]
    ),

    new Route(
        pattern: '^/api/statuses$',
        className: StatusController::class,
        extraData: [__DIR__, $pdo, $logger]
    ),

    new Route(
        pattern: '^/api/sources$',
        className: SourceController::class,
        extraData: [__DIR__, $pdo, $logger]
    ),

    new Route(
        pattern: '^/api/leads$',
        className: LeadController::class,
        extraData: [__DIR__, $pdo, $logger]
    ),

    new Route(
        pattern: '^/api/comments$',
        className: CommentController::class,
        extraData: [__DIR__, $pdo, $logger]
    ),

    new Route(
        pattern: '^/api/deposits$',
        className: DepositController::class,
        extraData: [__DIR__, $pdo, $logger]
    ),

    new Route(
        pattern: '^/api/deposits$',
        className: DepositController::class,
        extraData: [__DIR__, $pdo, $logger]
    ),

    // PAGES
    // PAGES-USER

    new Route(
        pattern: '^/page/user-add$',
        className: UserPage::class,
        methodName: 'showAddUserPage',
        extraData: [__DIR__, $pdo, $logger]
    ),

    new Route(
        pattern: '^/page/user-all$',
        className: UserPage::class,
        methodName: 'showAllUserPage',
        extraData: [__DIR__, $pdo, $logger]
    ),

    new Route(
        pattern: '^/page/user-edit/(\d+)$',
        className: UserPage::class,
        methodName: 'showEditUserPage',
        extraData: [__DIR__, $pdo, $logger]
    ),

    // PAGES-STATUS

    new Route(
        pattern: '^/page/status-add$',
        className: StatusPage::class,
        methodName: 'showAddStatusPage',
        extraData: [__DIR__, $pdo, $logger]
    ),

    new Route(
        pattern: '^/page/status-all$',
        className: StatusPage::class,
        methodName: 'showAllStatusPage',
        extraData: [__DIR__, $pdo, $logger]
    ),

    // PAGES-SOURCE

    new Route(
        pattern: '^/page/source-add$',
        className: SourcePage::class,
        methodName: 'showAddSourcePage',
        extraData: [__DIR__, $pdo, $logger]
    ),

    new Route(
        pattern: '^/page/source-all$',
        className: SourcePage::class,
        methodName: 'showAllSourcePage',
        extraData: [__DIR__, $pdo, $logger]
    ),

    // PAGES-LEAD

    new Route(
        pattern: '^/page/lead-add$',
        className: LeadPage::class,
        methodName: 'showAddLeadPage',
        extraData: [__DIR__, $pdo, $logger]
    ),

    new Route(
        pattern: '^/page/lead-all$',
        className: LeadPage::class,
        methodName: 'showAllLeadPage',
        extraData: [__DIR__, $pdo, $logger]
    ),

    new Route(
        pattern: '^/page/lead-edit/(\d+)$',
        className: LeadPage::class,
        methodName: 'showEditLeadPage',
        extraData: [__DIR__, $pdo, $logger]
    ),

    // PAGES-OTHER

    new Route(
        pattern: '^/login',
        className: LoginController::class,
        extraData: [__DIR__]
    ),

    new Route(
        pattern: '^/test',
        className: TestController::class,
        extraData: [__DIR__]
    ),

    new Route(
        pattern: '^/$',
        className: HomeController::class,
        methodName: null,
        extraData: ['Добро пожаловать!']
    ),
];

$rout404 = new Route(
    pattern: '.*',
    className: NotFoundController::class,
    methodName: 'show404',
);

$routError = new Route(
    pattern: '.*',
    className: ErrorController::class,
);

// Создаём обработчик маршрутов, передаём список маршрутов и URL для обработки:
$routeHandler = new RouteHandler(
    routes: $routes,
    currentUrl: $_SERVER['REQUEST_URI'],
    defaultRoute: $rout404,
    errorRoute: $routError,
    logger: $logger
);

// Запускаем поиск маршрута и вызов контроллера:
$routeHandler->dispatch();
