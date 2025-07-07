<?php

namespace crm\src\services\AppContext;

use crm\src\components\Security\SessionAuthManager;
use crm\src\components\Security\_entities\AccessRole;
use crm\src\components\UserManagement\_entities\User;
use crm\src\_common\repositories\AccessRoleRepository;
use crm\src\components\Security\_entities\AccessSpace;
use crm\src\_common\repositories\AccessSpaceRepository;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\_common\repositories\AccessContextRepository;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;
use crm\src\components\Security\_handlers\HandleAccessContext;
use crm\src\components\Security\_common\interfaces\IHandleAccessRole;
use crm\src\components\Security\_common\interfaces\IHandleAccessSpace;
use crm\src\components\UserManagement\_common\interfaces\IUserManagement;

interface IAppContext
{
    public function getUserManagement(): IUserManagement;

    public function getHandleAccessRole(): IHandleAccessRole;

    public function getHandleAccessSpace(): IHandleAccessSpace;

    public function getSessionAuthManager(): SessionAuthManager;

    public function getHandleAccessContext(): HandleAccessContext;

    public function getAccessRoleRepository(): AccessRoleRepository;

    public function getAccessSpaceRepository(): AccessSpaceRepository;

    public function getAccessContextRepository(): AccessContextRepository;

    public function getTemplateRenderer(): TemplateRenderer;

    public function getThisUser(): ?User;

    public function getThisRole(): ?AccessRole;

    public function getThisSpace(): ?AccessSpace;

    public function getThisAccessContext(): ?AccessContext;

    public function getLayout(array $components = []): TemplateBundle;

    public function checkSessionAndRedirect(): void;

    public function redirectIfNotAuthenticated(): void;

    public function logoutAndRedirect(): never;
}
