<?php

use PDO;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\controllers\LeadPage;
use crm\src\controllers\UserPage;
use crm\src\controllers\LoginPage;
use crm\src\controllers\SourcePage;
use crm\src\controllers\StatusPage;
use crm\src\controllers\HomeController;
use crm\src\controllers\TestController;
use crm\src\controllers\API\LeadController;
use crm\src\controllers\API\UserController;
use crm\src\controllers\API\LoginController;
use crm\src\controllers\BootstrapController;
use crm\src\controllers\API\SourceController;
use crm\src\controllers\API\StatusController;
use crm\src\controllers\API\CommentController;
use crm\src\controllers\API\DepositController;
use crm\src\services\RouteHandler\entities\Route;

return static function (PDO $pdo, LoggerInterface $logger): array {

    return [
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

    new Route(
        pattern: '^/api/login$',
        className: LoginController::class,
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
        className: LoginPage::class,
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
};
