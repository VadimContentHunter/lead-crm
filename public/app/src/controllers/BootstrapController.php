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

        $this->repository = new DbRepository($pdo, $this->logger);

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
        ];

        foreach ($schemas as $schemaKey => $displayName) {
            echo "<br>";
            $result = $this->repository->executeSql(CrmSchemaProvider::get($schemaKey) ?? '');
            if ($result->isSuccess()) {
                echo "{$displayName} table created<br>";
                $this->logger->info("{$displayName} table created");
            } else {
                $error = $result->getError()?->getMessage() ?? 'Неизвестная ошибка';
                echo $error . "<br>";
                $this->logger->error($error);
            }
        }


        echo "<br><br>Создание базовых моделей:<br><br>";
        $this->logger->info('Создание базовых моделей:');

        $handleAccessRole = new HandleAccessRole(new AccessRoleRepository($pdo, $this->logger));
        $handleAccessRole->addRole('superadmin', 'Superadmin');
        echo "<br>Создана роль: superadmin<br>";
        $this->logger->info('Создана роль: superadmin');

        $handleAccessRole->addRole('admin', 'admin');
        echo "<br>Создана роль: admin<br>";
        $this->logger->info('Создана роль: admin');

        $handleAccessRole->addRole('manager', 'manager');
        echo "<br>Создана роль: manager<br>";
        $this->logger->info('Создана роль: manager');

        $handleAccessRole->addRole('team-manager', 'team-manager');
        echo "<br>Создана роль: team-manager<br>";
        $this->logger->info('Создана роль: team-manager');


        echo "<br><br>Инициализация базовых пользователей:<br><br>";
        $userManagement = new UserManagement(
            new UserRepository($pdo, $logger),
            new UserValidatorAdapter()
        );
        $userSuperAdmin = $userManagement->create()->execute(new UserInputDto(
            login: 'superadmin',
            plainPassword: 'superadmin',
            confirmPassword: 'superadmin',
        ));

        if ($userSuperAdmin->isSuccess()) {
            echo "<br>Создан пользователь: superadmin<br>";
            $this->logger->info('Создан пользователь: superadmin');

            $handleAccessContext = new HandleAccessContext(new AccessContextRepository($pdo, $this->logger));
            if ($handleAccessRole->getRoleByName('superadmin') !== null) {
                if (
                    $handleAccessContext->createAccess(
                        userId: $userSuperAdmin->getId() ?? 0,
                        roleId: $handleAccessRole->getRoleByName('superadmin')->id ?? 0
                    )
                ) {
                    echo "<br>Создан контекст доступа: superadmin<br>";
                    $this->logger->info('Создан контекст доступа: superadmin');
                } else {
                    echo "<br>Не удалось создать контекст доступа: superadmin<br>";
                    $this->logger->error('Не удалось создать контекст доступа: superadmin');
                }
            } else {
                echo "<br>Не удалось создать контекст доступа: superadmin<br>";
                $this->logger->error('Не удалось создать контекст доступа: superadmin');
            }
        } else {
            $error = $userSuperAdmin->getError()?->getMessage() ?? 'Неизвестная ошибка';
             echo "<br>" . $error . "<br>";
            $this->logger->error($error);
        }


        echo "<br><br>Bootstrapping завершено.<br>";
        $this->logger->info('Bootstrapping завершено.');
    }
}
