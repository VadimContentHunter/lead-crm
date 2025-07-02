<?php

namespace crm\src\controllers;

use PDO;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\services\CrmSchemaProvider;
use crm\src\services\Repositories\DbRepository\DbRepository;

class BootstrapController
{
    private DbRepository $repository;

    public function __construct(
        PDO $pdo,
        private LoggerInterface $logger = new NullLogger()
    ) {
        echo "<br><br>Bootstrapping...<br>";
        echo "Создание таблиц:<br><br>";
        $logger->info('Bootstrapping...');
        $logger->info('Создание таблиц:');

        $this->repository = new DbRepository($pdo);
        $resultUsers = $this->repository->executeSql(CrmSchemaProvider::get('users'));
        if ($resultUsers->isSuccess()) {
            echo "Users table created<br>";
            $logger->info('Users table created');
        } else {
            echo $resultUsers->getError()?->getMessage() ?? 'Неизвестная ошибка' . "<br>";
            $logger->error($resultUsers->getError()?->getMessage() ?? 'Неизвестная ошибка');
        }
    }
}
