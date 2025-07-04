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
        $this->logger->info('Bootstrapping...');
        $this->logger->info('Создание таблиц:');

        $this->repository = new DbRepository($pdo);
        $resultUsers = $this->repository->executeSql(CrmSchemaProvider::get('users') ?? '');
        if ($resultUsers->isSuccess()) {
            echo "Users table created<br>";
            $this->logger->info('Users table created');
        } else {
            echo $resultUsers->getError()?->getMessage() ?? 'Неизвестная ошибка' . "<br>";
            $this->logger->error($resultUsers->getError()?->getMessage() ?? 'Неизвестная ошибка');
        }

        echo "<br>";
        $this->repository = new DbRepository($pdo);
        $resultStatuses = $this->repository->executeSql(CrmSchemaProvider::get('statuses') ?? '');
        if ($resultStatuses->isSuccess()) {
            echo "Statuses table created<br>";
            $this->logger->info('Statuses table created');
        } else {
            echo $resultStatuses->getError()?->getMessage() ?? 'Неизвестная ошибка' . "<br>";
            $this->logger->error($resultStatuses->getError()?->getMessage() ?? 'Неизвестная ошибка');
        }

        echo "<br>";
        $this->repository = new DbRepository($pdo);
        $resultSources = $this->repository->executeSql(CrmSchemaProvider::get('sources') ?? '');
        if ($resultSources->isSuccess()) {
            echo "Sources table created<br>";
            $this->logger->info('Sources table created');
        } else {
            echo $resultSources->getError()?->getMessage() ?? 'Неизвестная ошибка' . "<br>";
            $this->logger->error($resultSources->getError()?->getMessage() ?? 'Неизвестная ошибка');
        }

        echo "<br>";
        $this->repository = new DbRepository($pdo);
        $resultLeads = $this->repository->executeSql(CrmSchemaProvider::get('leads') ?? '');
        if ($resultLeads->isSuccess()) {
            echo "Leads table created<br>";
            $this->logger->info('Leads table created');
        } else {
            echo $resultLeads->getError()?->getMessage() ?? 'Неизвестная ошибка' . "<br>";
            $this->logger->error($resultLeads->getError()?->getMessage() ?? 'Неизвестная ошибка');
        }

        echo "<br>";
        $this->repository = new DbRepository($pdo);
        $resultBalances = $this->repository->executeSql(CrmSchemaProvider::get('balances') ?? '');
        if ($resultBalances->isSuccess()) {
            echo "Balances table created<br>";
            $this->logger->info('Balances table created');
        } else {
            echo $resultBalances->getError()?->getMessage() ?? 'Неизвестная ошибка' . "<br>";
            $this->logger->error($resultBalances->getError()?->getMessage() ?? 'Неизвестная ошибка');
        }

        echo "<br>";
        $this->repository = new DbRepository($pdo);
        $resultDeposits = $this->repository->executeSql(CrmSchemaProvider::get('deposits') ?? '');
        if ($resultDeposits->isSuccess()) {
            echo "Deposits table created<br>";
            $this->logger->info('Deposits table created');
        } else {
            echo $resultDeposits->getError()?->getMessage() ?? 'Неизвестная ошибка' . "<br>";
            $this->logger->error($resultDeposits->getError()?->getMessage() ?? 'Неизвестная ошибка');
        }

        echo "<br>";
        $this->repository = new DbRepository($pdo);
        $resultComments = $this->repository->executeSql(CrmSchemaProvider::get('comments') ?? '');
        if ($resultComments->isSuccess()) {
            echo "Comments table created<br>";
            $this->logger->info('Comments table created');
        } else {
            echo $resultComments->getError()?->getMessage() ?? 'Неизвестная ошибка' . "<br>";
            $this->logger->error($resultComments->getError()?->getMessage() ?? 'Неизвестная ошибка');
        }
    }
}
