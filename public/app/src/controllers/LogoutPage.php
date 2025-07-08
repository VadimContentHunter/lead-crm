<?php

namespace crm\src\controllers;

use crm\src\services\AppContext\IAppContext;

class LogoutPage
{
    public function __construct(
        private IAppContext $appContext
    ) {
        $this->appContext->logoutAndRedirect();
    }
}
