<?php
declare(strict_types=1);

use crm\src\controllers\HomeController;
use crm\src\controllers\UserController;
use crm\src\controllers\ErrorController;
use crm\src\controllers\NotFoundController;
use crm\src\components\RouteHandler\RouteHandler;
use crm\src\components\RouteHandler\entities\Route;

require_once __DIR__ . '/libs/autoload.php';

error_reporting(E_ALL);
set_error_handler(function(int $severity, string $message, string $file, int $line) {
    // Если ошибка не включена в error_reporting - пропускаем её
    if (!(error_reporting() & $severity)) {
        return false; // продолжить стандартный обработчик
    }
    // Генерируем исключение
    throw new \ErrorException($message, 0, $severity, $file, $line);
});


// Создаём маршруты:
$route1 = new Route(
    pattern: '#^/user/(\d+)$#',
    className: UserController::class,
    methodName: 'view',
    extraData: ['admin'] // extraData передастся как второй аргумент
);

$route2 = new Route(
    pattern: '#^/$#',
    className: HomeController::class,
    methodName: null,
    extraData: ['Добро пожаловать!'] // передаётся в конструктор
);

$rout404 = new Route(
    pattern: '#.*#',
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
    errorRoute: $routError
);

// Запускаем поиск маршрута и вызов контроллера:
$routeHandler->dispatch(); 