<?php

declare(strict_types=1);

use Psr\Log\NullLogger;
use crm\src\services\LoggerFactory;
use crm\src\controllers\ApiController;
use crm\src\controllers\HomeController;
use crm\src\controllers\TestController;
use crm\src\controllers\ErrorController;
use crm\src\controllers\LoginController;
use crm\src\controllers\API\UserController;
use crm\src\controllers\NotFoundController;
use crm\src\controllers\BootstrapController;
use  crm\src\services\RouteHandler\RouteHandler;
use  crm\src\services\RouteHandler\entities\Route;
use crm\src\services\Repositories\DbRepository\services\PdoFactory;

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

// Создаём маршруты:
$routeBootstrap = new Route(
    pattern: '^/bootstrap-key-A7F9X2M3Q8L1$',
    className: BootstrapController::class,
    extraData: [$pdo, $logger]
);

$routeApiUser = new Route(
    pattern: '^/api/users$',
    className: UserController::class,
    extraData: [$pdo, $logger]
);


$routeApi = new Route(
    pattern: '^/api$',
    className: ApiController::class,
    extraData: []
);

$routeLogin = new Route(
    pattern: '^/login',
    className: LoginController::class,
    extraData: [__DIR__]
);

$routeTEST = new Route(
    pattern: '^/test',
    className: TestController::class,
    extraData: [__DIR__]
);

$route1 = new Route(
    pattern: '^/user/(\d+)$',
    className: UserController::class,
    methodName: 'view',
    extraData: ['admin'] // extraData передастся как второй аргумент
);

$route2 = new Route(
    pattern: '^/$',
    className: HomeController::class,
    methodName: null,
    extraData: ['Добро пожаловать!'] // передаётся в конструктор
);

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
    routes: [ $route1, $route2, $routeTEST, $routeLogin, $routeApi, $routeBootstrap, $routeApiUser ],
    currentUrl: $_SERVER['REQUEST_URI'],
    defaultRoute: $rout404,
    errorRoute: $routError,
    logger: $logger
);

// Запускаем поиск маршрута и вызов контроллера:
$routeHandler->dispatch();
