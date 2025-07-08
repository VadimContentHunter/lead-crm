<?php

namespace crm\src\controllers;

use crm\src\services\AppContext\AppContext;

class LogoutPage
{
    public function __construct(
        private AppContext $appContext
    ) {
        $this->appContext->logoutAndRedirect();
    }
}
