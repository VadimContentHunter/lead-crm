<?php

namespace crm\src\services\AppContext;

use crm\src\components\Security\SessionAuthManager;
use crm\src\components\LeadManagement\LeadManagement;
use crm\src\components\Security\_entities\AccessRole;
use crm\src\components\UserManagement\_entities\User;
use crm\src\_common\repositories\AccessRoleRepository;
use crm\src\components\Security\_entities\AccessSpace;
use crm\src\_common\repositories\AccessSpaceRepository;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\_common\repositories\AccessContextRepository;
use crm\src\components\SourceManagement\SourceManagement;
use crm\src\components\BalanceManagement\BalanceManagement;
use crm\src\components\CommentManagement\CommentManagement;
use crm\src\components\DepositManagement\DepositManagement;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;
use crm\src\components\Security\_handlers\HandleAccessContext;
use crm\src\_common\repositories\LeadRepository\LeadRepository;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
use crm\src\components\Security\_common\interfaces\IHandleAccessRole;
use crm\src\components\Security\_common\interfaces\IHandleAccessSpace;
use crm\src\components\LeadManagement\_common\interfaces\ILeadRepository;
use crm\src\components\UserManagement\_common\interfaces\IUserManagement;
use crm\src\components\StatusManagement\_common\interfaces\IStatusManagement;

interface IAppContext
{
    public function getBalanceManagement(): BalanceManagement;

    public function getDepositManagement(): DepositManagement;

    public function getLeadManagement(): LeadManagement;

    public function getCommentManagement(): CommentManagement;

    public function getSourceManagement(): SourceManagement;

    public function getStatusManagement(): IStatusManagement;

    public function getUserManagement(): IUserManagement;

    public function getHandleAccessRole(): IHandleAccessRole;

    public function getHandleAccessSpace(): IHandleAccessSpace;

    public function getSessionAuthManager(): SessionAuthManager;

    public function getHandleAccessContext(): HandleAccessContext;

    public function getAccessGranter(): IAccessGranter;

    public function getAccessRoleRepository(): AccessRoleRepository;

    public function getAccessSpaceRepository(): AccessSpaceRepository;

    public function getAccessContextRepository(): AccessContextRepository;

    public function getLeadRepository(): ILeadRepository;

    public function getTemplateRenderer(): TemplateRenderer;

    public function getJsonRpcServerFacade(): JsonRpcServerFacade;

    public function getThisUser(): ?User;

    public function getThisRole(): ?AccessRole;

    public function getThisSpace(): ?AccessSpace;

    public function getThisAccessContext(): ?AccessContext;

    /**
     * @param array<string,mixed> $components
     */
    public function getLayout(array $components = []): TemplateBundle;

    public function checkSessionAndRedirect(): void;

    public function redirectIfNotAuthenticated(): void;

    public function logoutAndRedirect(): never;
}
