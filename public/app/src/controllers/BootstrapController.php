<?php

namespace crm\src\controllers;

use PDO;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\services\CrmSchemaProvider;
use crm\src\_common\repositories\UserRepository;
use crm\src\_common\adapters\UserValidatorAdapter;
use crm\src\components\UserManagement\UserManagement;
use crm\src\_common\repositories\AccessRoleRepository;
use crm\src\_common\repositories\AccessContextRepository;
use crm\src\components\Security\_handlers\HandleAccessRole;
use crm\src\services\Repositories\DbRepository\DbRepository;
use crm\src\components\Security\_handlers\HandleAccessContext;
use crm\src\components\UserManagement\_common\DTOs\UserInputDto;
use crm\src\Investments\_services\InvestmentSchemaProvider;

class BootstrapController
{
    private DbRepository $repository;
    private PDO $pdo;

    public function __construct(
        PDO $pdo,
        private LoggerInterface $logger = new NullLogger()
    ) {
        $this->pdo = $pdo;
        $this->repository = new DbRepository($pdo, $this->logger);

        echo "<br><br>Bootstrapping...<br>";
        $this->logger->info('Bootstrapping...');

        $this->createCommonSchemas();
        $this->createDefaultRoles();
        $this->createSuperAdmin();

        echo "<br><br>Bootstrapping завершено.<br>";
        $this->logger->info('Bootstrapping завершено.');
    }

    private function createCommonSchemas(): void
    {
        echo "Создание таблиц CRM:<br><br>";
        $this->logger->info('Создание таблиц CRM:');

        $schemas = [
            'users'           => 'Users',
            'statuses'        => 'Statuses',
            'sources'         => 'Sources',
            'leads'           => 'Leads',
            'balances'        => 'Balances',
            'deposits'        => 'Deposits',
            'comments'        => 'Comments',
            'access_roles'    => 'Access roles',
            'access_spaces'   => 'Access spaces',
            'access_contexts' => 'Access contexts',

            'inv_sources'     => 'Investment sources',
            'inv_statuses'    => 'Investment statuses',
            'inv_leads'       => 'Investment leads',
            'inv_balances'    => 'Investment balances',
            'inv_comments'    => 'Investment comments',
            'inv_deposits'    => 'Investment deposits',
            'inv_activities'  => 'Investment activities',
        ];

        foreach ($schemas as $key => $name) {
            $this->executeSchema(CrmSchemaProvider::get($key), $name);
        }
    }

    private function createDefaultRoles(): void
    {
        echo "<br><br>Создание ролей:<br><br>";
        $this->logger->info('Создание ролей:');

        $handleAccessRole = new HandleAccessRole(new AccessRoleRepository($this->pdo, $this->logger));
        foreach (['superadmin', 'admin', 'manager', 'team-manager'] as $role) {
            $handleAccessRole->addRole($role, $role);
            echo "<br>Создана роль: {$role}<br>";
            $this->logger->info("Создана роль: {$role}");
        }
    }

    private function createSuperAdmin(): void
    {
        echo "<br><br>Инициализация пользователя superadmin:<br><br>";

        $userManagement = new UserManagement(
            new UserRepository($this->pdo, $this->logger),
            new UserValidatorAdapter()
        );

        $result = $userManagement->create()->execute(new UserInputDto(
            login: 'superadmin',
            plainPassword: 'superadmin',
            confirmPassword: 'superadmin',
        ));

        if ($result->isSuccess()) {
            echo "<br>Создан пользователь: superadmin<br>";
            $this->logger->info('Создан пользователь: superadmin');

            $handleAccessRole = new HandleAccessRole(new AccessRoleRepository($this->pdo, $this->logger));
            $handleAccessContext = new HandleAccessContext(new AccessContextRepository($this->pdo, $this->logger));

            $role = $handleAccessRole->getRoleByName('superadmin');
            if (
                $role && $handleAccessContext->createAccess(
                    userId: $result->getId() ?? 0,
                    roleId: $role->id ?? 0
                )
            ) {
                echo "<br>Создан контекст доступа: superadmin<br>";
                $this->logger->info('Создан контекст доступа: superadmin');
            } else {
                echo "<br>Не удалось создать контекст доступа: superadmin<br>";
                $this->logger->error('Не удалось создать контекст доступа: superadmin');
            }
        } else {
            $error = $result->getError()?->getMessage() ?? 'Неизвестная ошибка';
            echo "<br>{$error}<br>";
            $this->logger->error($error);
        }
    }

    private function executeSchema(?string $sql, string $label): void
    {
        if (!$sql) {
            echo "Schema for {$label} is missing<br>";
            $this->logger->warning("Schema for {$label} is missing");
            return;
        }

        $result = $this->repository->executeSql($sql);

        if ($result->isSuccess()) {
            echo "{$label} table created<br>";
            $this->logger->info("{$label} table created");
        } else {
            $error = $result->getError()?->getMessage() ?? 'Неизвестная ошибка';
            echo "{$label} — ошибка: {$error}<br>";
            $this->logger->error("{$label} error: {$error}");
        }
    }
}
