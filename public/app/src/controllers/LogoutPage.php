<?php

namespace crm\src\controllers;

use crm\src\services\AppContext;

class LogoutPage
{
    public function __construct(
        private ?AppContext $appContext = null
    ) {
        $this->appContext->logoutAndRedirect();
    }
}
