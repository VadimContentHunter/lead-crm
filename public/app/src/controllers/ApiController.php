<?php

namespace crm\src\controllers;

class HomeController
{
    public function __construct(string $welcomeMessage)
    {
        echo "HomeController создан с сообщением: $welcomeMessage\n";
    }
}
