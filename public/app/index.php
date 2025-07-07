<?php

declare(strict_types=1);

use Psr\Log\NullLogger;
use crm\src\services\AppContext;
use crm\src\controllers\ErrorController;
use crm\src\controllers\NotFoundController;
use  crm\src\services\RouteHandler\RouteHandler;
use  crm\src\services\RouteHandler\entities\Route;
use crm\src\services\Repositories\DbRepository\services\PdoFactory;

session_start();

// define('PROJECT_ROOT', __DIR__);
require_once __DIR__ . '/libs/autoload.php';
require_once __DIR__ . '/routes.php';

// $logger = LoggerFactory::createLogger(baseLogDir:__DIR__ . '/logs');
$logger = new NullLogger();
$pdo = PdoFactory::create([
    'host' => 'host.docker.internal',
    'db'   => 'crm_db',
    'user' => 'root',
    'pass' => 'root',
]);
$appContext = new AppContext(__DIR__, $pdo, $logger);

// SecureWrapperFactory::init(new BasedAccessGranter(
//     roleRepository: new AccessRoleRepository($pdo, $logger),
//     spaceRepository: new AccessSpaceRepository($pdo, $logger),
// ));


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
    routes: loadRoutes($pdo, $logger, $appContext),
    currentUrl: $_SERVER['REQUEST_URI'],
    defaultRoute: $rout404,
    errorRoute: $routError,
    logger: $logger
);

// Запускаем поиск маршрута и вызов контроллера:
$routeHandler->dispatch();
