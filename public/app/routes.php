<?php

use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\controllers\P2pPage;
use crm\src\controllers\LeadPage;
use crm\src\controllers\UserPage;
use crm\src\controllers\LoginPage;
use crm\src\controllers\InvestPage;
use crm\src\controllers\LogoutPage;
use crm\src\controllers\HomeController;
use crm\src\controllers\InvestEditPage;
use crm\src\controllers\TestController;
use crm\src\controllers\AccessDeniedPage;
use crm\src\controllers\API\ApiController;
use crm\src\controllers\API\LeadController;
use crm\src\controllers\API\UserController;
use crm\src\controllers\API\LoginController;
use crm\src\controllers\BootstrapController;
use crm\src\services\AppContext\IAppContext;
use crm\src\controllers\API\SourceController;
use crm\src\controllers\API\StatusController;
use crm\src\controllers\API\BalanceController;
use crm\src\controllers\API\CommentController;
use crm\src\controllers\API\DepositController;
use crm\src\services\RouteHandler\entities\Route;
use crm\src\controllers\API\Invest\InvLeadController;
use crm\src\controllers\API\Invest\InvSourceController;
use crm\src\controllers\API\Invest\InvStatusController;
use crm\src\controllers\API\Invest\InvActivityController;

/**
 * @return Route[]
 */
function loadRoutes(PDO $pdo, IAppContext $appContext, LoggerInterface $logger = new NullLogger()): array
{
    return [
        new Route(
            pattern: '^/bootstrap-key-A7F9X2M3Q8L1/?$',
            className: BootstrapController::class,
            extraData: [$pdo, $logger]
        ),

        // API

        new Route(
            pattern: '^/api/?$',
            className: ApiController::class,
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^/api/invest/activities/?$',
            className: InvActivityController::class,
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^/api/invest/leads/?$',
            className: InvLeadController::class,
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^/api/invest/sources/?$',
            className: InvSourceController::class,
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^/api/invest/statuses/?$',
            className: InvStatusController::class,
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^/api/users/?$',
            className: UserController::class,
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^/api/statuses/?$',
            className: StatusController::class,
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^/api/sources/?$',
            className: SourceController::class,
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^/api/leads/?$',
            className: LeadController::class,
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^/api/comments/?$',
            className: CommentController::class,
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^/api/balances/?$',
            className: BalanceController::class,
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^/api/deposits/?$',
            className: DepositController::class,
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^/api/login/?$',
            className: LoginController::class,
            extraData: [$appContext]
        ),

        // PAGES
        // PAGES-USER

        new Route(
            pattern: '^/page/user-add/?$',
            className: UserPage::class,
            methodName: 'showAddUserPage',
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^/users/?$',
            className: UserPage::class,
            methodName: 'showAllUserPage',
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^/page/user-edit/(\d+)/?$',
            className: UserPage::class,
            methodName: 'showEditUserPage',
            extraData: [$appContext]
        ),

        // PAGES-LEAD

        new Route(
            pattern: '^/page/lead-edit/(\d+)/?$',
            className: LeadPage::class,
            methodName: 'showEditLeadPage',
            extraData: [$appContext]
        ),

        // PAGES-OTHER

        new Route(
            pattern: '^/invest/lead-edit/(\d+)/?$',
            className: InvestEditPage::class,
            methodName: 'renderPage',
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^/invest/?$',
            className: InvestPage::class,
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^/p2p/?$',
            className: P2pPage::class,
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^/login/?$',
            className: LoginPage::class,
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^/logout/?$',
            className: LogoutPage::class,
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^/access-denied/?$',
            className: AccessDeniedPage::class,
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^/test/?$',
            className: TestController::class,
            extraData: [$appContext]
        ),

        new Route(
            pattern: '^//?$',
            className: HomeController::class,
            methodName: null,
            extraData: ['Добро пожаловать!']
        ),
    ];
}
