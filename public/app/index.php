<?php

declare(strict_types=1);

use Psr\Log\NullLogger;
use crm\src\services\LoggerFactory;
use crm\src\controllers\HomeController;
use crm\src\controllers\UserController;
use crm\src\controllers\ErrorController;
use crm\src\controllers\NotFoundController;
use  crm\src\services\RouteHandler\RouteHandler;
use  crm\src\services\RouteHandler\entities\Route;

define('PROJECT_ROOT', __DIR__);

require_once PROJECT_ROOT . '/libs/autoload.php';

// $logger = LoggerFactory::createLogger(baseLogDir:PROJECT_ROOT . '/logs');
$logger = new NullLogger();

// Создаём маршруты:
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
    routes: [$route1, $route2],
    currentUrl: $_SERVER['REQUEST_URI'],
    defaultRoute: $rout404,
    errorRoute: $routError,
    logger: $logger
);

// Запускаем поиск маршрута и вызов контроллера:
$routeHandler->dispatch();
