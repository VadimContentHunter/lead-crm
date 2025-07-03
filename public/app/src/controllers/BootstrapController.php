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

        echo "<br>";
        $this->repository = new DbRepository($pdo);
        $resultStatuses = $this->repository->executeSql(CrmSchemaProvider::get('statuses'));
        if ($resultStatuses->isSuccess()) {
            echo "Statuses table created<br>";
            $logger->info('Statuses table created');
        } else {
            echo $resultStatuses->getError()?->getMessage() ?? 'Неизвестная ошибка' . "<br>";
            $logger->error($resultStatuses->getError()?->getMessage() ?? 'Неизвестная ошибка');
        }

        $this->repository = new DbRepository($pdo);
        $resultSources = $this->repository->executeSql(CrmSchemaProvider::get('sources'));
        if ($resultSources->isSuccess()) {
            echo "Sources table created<br>";
            $logger->info('Sources table created');
        } else {
            echo $resultSources->getError()?->getMessage() ?? 'Неизвестная ошибка' . "<br>";
            $logger->error($resultSources->getError()?->getMessage() ?? 'Неизвестная ошибка');
        }
    }
}
